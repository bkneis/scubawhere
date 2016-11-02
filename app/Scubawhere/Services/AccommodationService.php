<?php

namespace Scubawhere\Services;

use Scubawhere\Context;
use Scubawhere\Entities\Booking;
use Scubawhere\Exceptions\ConflictException;
use Scubawhere\Repositories\AccommodationRepoInterface;

class AccommodationService {

	/** @var \Scubawhere\Repositories\AccommodationRepo */
	protected $accommodation_repo;

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
		$this->accommodation_repo = $accommodation_repo;
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
		return $this->accommodation_repo->get($id, ['prices', 'basePrices']);
	}

	/**
     * Get all accommodations for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->accommodation_repo->all(['prices', 'basePrices']);
	}

	/**
     * Get all accommodations for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->accommodation_repo->allWithTrashed(['prices', 'basePrices']);
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

		$query = [];
		if(!empty($data['accommodation_id'])) {
			$query = array(array('id', '=', $data['accommodation_id']));
		}

		$accommodations = $this->accommodation_repo->getWhere($query);

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
	 * Transform the data retrieved by the database into a manifest
	 *
	 * @param $data Collected from the database
	 *
	 * @return array Formatted array to be used as the manifest
	 */
	private function transformManifest($data)
	{
		return array(
			'booking' => array(
				'ref'   => $data->reference,
				'paid'  => ((int) $data->paid - (int) $data->refunded),
				'price' => $data->price
			),
			'customer' => array(
				'firstname'    => $data->firstname,
				'lastname'     => $data->lastname,
				'country'      => $data->country_id,
				'phone'        => $data->phone,
				'last_dive'    => $data->last_dive,
				'fin_size'     => $data->shoe_size,
				'bcd_size'     => $data->chest_size,
				'wetsuit_size' => $data->height,
				'notes'        => $data->notes
			)
		);
	}

	/**
	 * Create a manifest of al the bookings in a night for an accommodation
	 * 
	 * @param int    $id   ID of the accommodation
	 * @param string $date Date string of the date to get the manifest for
	 *
	 * @return array
	 */
	public function getManifest($id, $date)
	{
		$before = new \DateTime($date);
		$after  = $before->add(new \DateInterval('P1D'));
		$before = $before->format('Y:m:d H:i:s');
		$after  = $after->format('Y:m:d H:i:s');

		$data = $this->accommodation_repo->getBookings($id, [$before, $after]);

		$manifest = [];
		foreach($data as $obj) {
			array_push($manifest, $this->transformManifest($obj));
		}

		return $manifest;

	}

	/**
	 * Validate, create and save the accommodation and prices to the database
	 *
	 * @param array $data        [description]
	 * @param array $base_prices [description]
	 * @param array $prices      [description]
	 *
	 * @return \Scubawhere\Entities\Accommodation
	 */
	public function create($data, $base_prices, $prices) 
	{
		$prices = $this->price_service->validatePrices($base_prices, $prices);
		$accommodation = $this->accommodation_repo->create($data);

        $this->price_service->associatePrices($accommodation->basePrices(), $prices['base']);
        if($prices['seasonal']) $this->price_service->associatePrices($accommodation->prices(), $prices['seasonal']);

        return $accommodation;
	}

	/**
	 * Validate, update and save the accommodation and prices to the database
	 *
	 * @param  int   $id           ID of the accommodation
	 * @param  array $data         Information about accommodation
	 * @param  array $base_prices  Prices to associate to the accommodation model
	 * @param  array $prices       Seasonal prices to
	 *
	 * @return [type]              [description]
	 */
	public function update($id, $data, $base_prices, $prices) 
	{
    	$prices = $this->price_service->validatePrices($base_prices, $prices);
    	$accommodation = $this->accommodation_repo->update($id, $data);

    	if($prices['base']) $this->price_service->associatePrices($accommodation->basePrices(), $prices['base']);
    	if($prices['seasonal']) $this->price_service->associatePrices($accommodation->prices(), $prices['seasonal']);

    	return $accommodation;
	}

	/**
	 * Remove the accommodation from the database.
	 *
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the accommodation, and the booking ids are then logged
	 *
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * @throws \Exception
	 *
	 * @param  int $id ID of the accommodation
	 */
	public function delete($id)
	{
		$accommodation = $this->accommodation_repo->getUsedInFutureBookings($id);
	
		$quotes = $accommodation->bookings
			->map(function($obj) {
				if($obj->status == 'saved') return $obj->id;
			})
			->toArray();

		Booking::onlyOwners()->whereIn('id', $quotes)->delete();

		$bookings = $accommodation->bookings
			->filter(function($obj) {
				if($obj->status != 'cancelled' && $obj->status != 'saved') return $obj;
			})
			->toArray();

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the accommodation, ' 
												 . $accommodation->name);
			foreach($bookings as $obj) {
				$logger->append('The accommodation is used in the booking ' . '['.$obj['reference'].']');
			}
			throw new ConflictException([
					'The accommodation could not be deleted as it is booked in the future, '.
					'please <a href="#troubleshooting?id=' . $logger->getId() . '">click here</a> to find how to delete it.'
				]);
		}

        // Check if the user wants to delete accommodation even when in packages
		if(!$accommodation->getDeletableAttribute()) 
		{
			if ($accommodation->packages()->exists()) 
			{
                // Loop through each package and remove its pivot from packages
                $packages = $accommodation->packages();
				foreach($packages as $obj) 
				{
                    //$accommodation->packages()->detach($obj->id);
                    \DB::table('packageables')
                        ->where('packageable_type', 'Accommodation')
                        ->where('packageable_id', $accommodation->id)
                        ->where('package_id', $obj->id)
                        ->update(array('deleted_at' => \DB::raw('NOW()')));    
                }
                $accommodation->save();
            }
        }

        $accommodation->delete();
	}

}