<?php

namespace Scubawhere\Services;

use Scubawhere\Context;
use Scubawhere\Entities\Accommodation;
use Scubawhere\Entities\Booking;
use Scubawhere\Entities\Price;
use Scubawhere\Exceptions\ConflictException;
use Scubawhere\Repositories\AccommodationRepoInterface;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class AccommodationService {

	/** @var \Scubawhere\Repositories\AccommodationRepo */
	protected $accommodations;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 *
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * Service used to validate and associate prices
	 *
	 * @var \Scubawhere\Services\PriceService
	 */
	protected $price_service;


	public function __construct(AccommodationRepoInterface $accommodation_repo,
								LogService                 $log_service,
								PriceService               $price_service)
	{
		$this->accommodations = $accommodation_repo;
		$this->log_service = $log_service;
		$this->price_service = $price_service;
	}

	/**
     * Get an accommodation for a company from its id
	 *
     * @param int $id
	 *
     * @return \Scubawhere\Entities\Accommodation
     */
	public function get($id) {
		return $this->accommodations->get($id, ['prices', 'basePrices']);
	}

	/**
     * Get all accommodations for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->accommodations->all(['prices', 'basePrices']);
	}

	/**
     * Get all accommodations for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->accommodations->allWithTrashed(['prices', 'basePrices']);
	}

	/**
	 * Validate the 'before' and 'after' date to ensure they do cross over and set the default to today + 1 month if not set
	 *
	 * @param  array $data Contains a 'before' and 'after' index with date strings
	 *
	 * @return array $data The same $data variable with the modified before and after
	*/
	private function validateFilterDates(array $data)
	{
		// Transform parameter strings into DateTime objects
        $data['after'] = new \DateTime($data['after'], new \DateTimeZone(Context::get()->timezone)); // Defaults to NOW, when parameter is NULL
        if (empty($data['before'])) 
        {
            if ($data['after'] > new \DateTime('now', new \DateTimeZone(Context::get()->timezone))) 
            {
                // If the submitted `after` date lies in the future, move the `before` date to return 1 month of results
                $data['before'] = clone $data['after']; // Shallow copies without reference to cloned object
                $data['before']->add(new \DateInterval('P1M')); // Extends the date 1 month into the future
            } 
            else 
            {
                // If 'after' date lies in the past or is NOW, return results up to 1 month into the future
                $data['before'] = new \DateTime('+1 month', new \DateTimeZone(Context::get()->timezone));
            }
        } 
        else 
        {
            // If a 'before' date is submitted, simply use it
            $data['before'] = new \DateTime($data['before'], new \DateTimeZone(Context::get()->timezone));
        }

        if ($data['after'] > $data['before']) 
        {
            return Response::json(array('errors' => array('The supplied \'after\' date is later than the given \'before\' date.')), 400); // 400 Bad Request
        }
        return $data;
	}

	/**
	 * Get all accommodations available between a 'before' and 'after' date
	 *
	 * @param  array $data Array consisting of 'before' and 'after' date aswell as accommodation_id
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getFilter($data)
	{
        $data = $this->validateFilterDates($data);

        $current_date = clone $data['after'];
        $result = array();

		$query = [];if(!empty($data['accommodation_id'])) {
			$query = array(array('id', '=', $data['accommodation_id']));
		}

		$accommodations = $this->accommodations->getWhere($query);

        if(!$accommodations->isEmpty())
        {
        	 // Generate the utilisation for every day within the requested date range
	        do {
	            $key = $current_date->format('Y-m-d');

	            $result[$key] = array();

	            $accommodations->each(function ($el) use ($key, &$result, $current_date) {
	                $result[$key][$el->id] = array(
	                    $el->bookings()
	                        ->wherePivot('start', '<=', $current_date)
	                        ->wherePivot('end', '>', $current_date)
	                        ->where(function ($query) {
	                            $query->whereIn('status', Booking::$counted);
	                        })
	                        ->count(),
	                    $el->capacity,
	                );
	            });

	            $current_date->add(new \DateInterval('P1D'));
	        } while ($current_date < $data['before']);
        }

        return $result;
	}

	/**
	 * Create a manifest of al the bookings in a night for an accommodation
	 *
	 * @param array $dates
	 * @return array
	 * @internal param int $id ID of the accommodation
	 * @internal param string $date Date string of the date to get the manifest for
	 */
	public function getManifest(array $dates)
	{
		$dates = $this->formatDates($dates);
		return $this->accommodations->getAvailability($dates);
	}

	protected function formatDates(array $dates)
	{
		$dateStrings = [];
		if (isset($dates['after'])) {
			$dateStrings['after'] = new \DateTime($dates['after']);
			$dateStrings['before'] = clone $dateStrings['after'];
			$before  = $dateStrings['before']->add(new \DateInterval('P1D'));
			$dateStrings['after'] = $before->format('Y:m:d');
		} else {
			$dateStrings['after'] = new \DateTime($dates['after']);
			$dateStrings['before'] = $dateStrings['before']->format('Y-m-d H:i:s');
			$dateStrings['after']  = new \DateTime($dates['after']);
			$dateStrings['after']  = $dateStrings['before']->format('Y-m-d H:i:s');
		}
		return $dateStrings;
	}


	/**
	 * @todo deprecate
	 * @param array $dates
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getAvailability(array $dates)
	{
		$dates = $this->formatDates($dates);
		return $this->accommodations->getAvailability($dates);
	}

	/**
	 * Validate, create and save the accommodation and prices to the database
	 *
	 * @param array $data [description]
	 * @return Accommodation
	 * @throws HttpUnprocessableEntity
	 * @internal param array $base_prices [description]
	 * @internal param array $prices [description]
	 */
	public function create($data)
	{
		// Check that their is atleast one price given
		if(empty($data['prices'])) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please submit atleast one price']);
		}
		return Accommodation::create($data)->syncPrices($data['prices']);
	}

	/**
	 * Validate, update and save the accommodation and prices to the database
	 *
	 * @param  int $id ID of the accommodation
	 * @param  array $data Information about accommodation
	 * @return Accommodation
	 * @throws HttpUnprocessableEntity
	 */
	public function update($id, $data)
	{
		// Check that their is atleast one price given
		if(empty($data['prices'])) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please submit atleast one price']);
		}

		/*
 		 * @note, can I make the get function look more natural? It reads oddly, get($id, 'prices')
		 * its not immediately obvious what the string is (its a relationship) by maybe creating another
		 * function getWith() would be good. But then your left with getById() or even worse the
		 * 2 combined, getByIdWith(). How about getBy(), then if the name of the variable is whats used to get
		 * it, i.e. getBy($id) // using say 1, by then getBy($name) // say john as a string.
		*/
    	return $this->accommodations
			->get($id, 'prices')
			->update($data)
			->syncPrices($data['prices']);
	}

	/**
	 * Remove the accommodation from the database.
	 *
	 * In addition delete any quotes or packages associated to it. This will fail if their are
	 * future paid bookings associated to the accommodation, and the booking ids are then logged
	 *
	 * @return bool|null
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * @throws \Exception
	 *
	 * @param  int $id ID of the accommodation
	 */
	public function delete($id)
	{
		// Get the accommodation with any future bookings using it
		$accommodation = $this->accommodations->getWithFutureBookings($id);

		// Get the accommodation bookings that are either reserved, started or confirmed
		$bookings = $accommodation->getActiveBookings();

		// If there are any active bookings that are using the accommofation, log them and their
		// booking reference and return a http conflict error
		if (empty($bookings)) {

			$logger = $this->log_service->create('Attempting to delete the accommodation, ' . $accommodation->name);
			$logger->write('The accommodation is used in the booking ?', $bookings, ['reference'])->save();
			throw new ConflictException([
					'The accommodation could not be deleted as it is booked in the future, '.
					'please <a href="#troubleshooting?id=' . $logger->getId() . '">click here</a> to find how to delete it.', empty($bookings)
				]);
		}

		// Delete all quotes that use the accommodation
		// @note feels kinda annoying I cant call delete on a collection, maybe i should add it ?
		$accommodation->getQuotes()->each(function ($quote) { $quote->delete(); });

		// Remove the accommodation from any packages that are using them, and then delete the package
		return $accommodation->removeFromPackages()->delete();
	}

}
