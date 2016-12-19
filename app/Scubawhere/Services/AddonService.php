<?php

namespace Scubawhere\Services;

use Scubawhere\Entities\Addon;
use Scubawhere\Entities\Booking;
use Scubawhere\Exceptions\ConflictException;
use Scubawhere\Repositories\AddonRepoInterface;

class AddonService {

	/** @var \Scubawhere\Repositories\AddonRepo */
	protected $addonRepo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * Service used to validate and associate prices
	 * @var \Scubawhere\Services\PriceService
	 */
	protected $price_service;

	public function __construct(AddonRepoInterface $addon_repo,
								LogService $log_service,
								PriceService $price_service)
	{
		$this->addonRepo = $addon_repo;
		$this->log_service = $log_service;
		$this->price_service = $price_service;
	}

	/**
     * Get an addon for a company from its id
	 *
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Scubawhere\Entities\Addon
     */
	public function get($id) {
		return $this->addonRepo->get($id, ['prices']);
	}

	/**
     * Get all addons for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->addonRepo->all(['prices']);
	}

	/**
     * Get all addons for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->addonRepo->allWithTrashed(['prices']);
	}

	/**
	 * Validate, create and save the addon and prices to the database
	 *
	 * @note Prices is not yet implmeneted on the front end (27/10/16)
	 * @param array $data
	 * @return \Scubawhere\Entities\Addon
	 * @throws \Scubawhere\Exceptions\Http\HttpBadRequest
	 */
	public function create(array $data)
	{
		return Addon::create($data)->syncPrices($data['prices']);
	}

	/**
	 * Validate, update and save the addon and prices to the database
	 *
	 * @param  int $id ID of the addon
	 * @param  array $data Information about addon
	 * @return Addon
	 * @throws \Scubawhere\Exceptions\Http\HttpBadRequest
	 * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
	 *
	 */
	public function update($id, $data) 
	{
		return $this->addonRepo
			->get($id)
			->update($data)
			->syncPrices($data['prices']);
	}

	/**
	 * Remove the addon from the database.
	 * 
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the addon, and the booking ids are then logged.
	 * 
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * @throws \Exception
	 * @param  int $id ID of the addon
	 */
	public function delete($id)
	{
		/**
		 * 1. Get the addon model with any sessions it is booked in
		 * 2. Delete any quote associated with the addon
		 * 3. Filter through bookings that have not been cancelled
		 * 4. If there are valid (not cancelled) bookings, log all of their refrences and return a conflict
		 * 5. Check if the addon is used in any packages, if so, remove them
		 * 6. Delete the addon and its associated prices
		 */
		
		// STEP 1.
		$addon = $this->addonRepo->getWithFutureBookings($id);

		$booking_ids = $addon->bookingdetails->map(function($obj) {
			if($obj->session != null || $obj->training_session != null) {
				return $obj->booking_id;
			}
		})
		->toArray();

		$bookings = Booking::onlyOwners()
			->whereIn('id', $booking_ids)
			->get(['id', 'reference', 'status']);

		// STEP 2.
		$quotes = $bookings->map(function($obj) {
			if($obj->status == 'saved') return $obj->id;
		})
		->toArray();

		Booking::onlyOwners()->whereIn('id', $quotes)->delete();

		// STEP 3.
		$bookings = $bookings->filter(function($obj){
			if($obj->status != 'cancelled' && $obj->status != 'saved') return $obj;	
		})->toArray();

		// STEP 4.
		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the addon, ' . $addon->name);

			foreach($bookings as $obj) {
				$logger->append('The addon is used in the future in booking ' . '['.$obj['reference'].']');
			}
			throw new ConflictException(
				['The addon could not be deleted as it is used in bookings in the future, '.
						'Please <a href="#troubleshooting?id='. $logger->getId() .'">click here</a> for more info on how to delete it.']);
		}

        // STEP 5.
		if(!$addon->getDeletableAttribute()) 
		{
			if($addon->packages()->exists()) 
			{
                $packages = $addon->packages();
                /**
                 * Loop through each package and soft delete the pivot betweeen
                 * addon and package, so that packages used in the future
                 * can use a previous state
                 * http://stackoverflow.com/questions/17350072/soft-delete-on-a-intermediate-table-for-many-to-many-relationship
                */
				foreach($packages as $obj) 
				{
                    \DB::table('packageables')
                        ->where('packageable_type', 'Addon')
                        ->where('packageable_id', $addon->id)
                        ->where('package_id', $obj->id)
                        ->update(array('deleted_at' => \DB::raw('NOW()')));    
                }
                $addon->save();
            }
        }
        // STEP 6.
        $addon->delete();
        $this->price_service->delete('Addon', $addon->id);
	}

}