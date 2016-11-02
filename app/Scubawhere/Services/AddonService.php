<?php

namespace Scubawhere\Services;

use Scubawhere\Entities\Booking;
use Scubawhere\Exceptions\ConflictException;
use Scubawhere\Repositories\AddonRepoInterface;

class AddonService {

	/** @var \Scubawhere\Repositories\AddonRepo */
	protected $addon_repo;

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

	public function __construct(AddonRepoInterface $addon_repo,
								LogService $log_service,
								PriceService $price_service)
	{
		$this->addon_repo = $addon_repo;
		$this->log_service = $log_service;
		$this->price_service = $price_service;
	}

	/**
     * Get an addon for a company from its id
	 *
     * @param int $id
	 *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 *
     * @return \Scubawhere\Entities\Addon
     */
	public function get($id) {
		return $this->addon_repo->get($id, ['basePrices']);
	}

	/**
     * Get all addons for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->addon_repo->all(['basePrices']);
	}

	/**
     * Get all addons for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->addon_repo->allWithTrashed(['basePrices']);
	}

	/**
	 * Validate, create and save the addon and prices to the database
	 *
	 * @note Prices is not yet implmeneted on the front end (27/10/16)
	 *
	 * @param array $data
	 * @param array $base_prices
	 * @param array $prices
	 *
	 * @return \Scubawhere\Entities\Addon
	 */
	public function create(array $data, array $base_prices, array $prices)
	{
		$prices = $this->price_service->validatePrices($base_prices, $prices);
		$addon = $this->addon_repo->create($data);

        $this->price_service->associatePrices($addon->basePrices(), $prices['base']);
        $addon->load(['basePrices']);
        return $addon;
	}

	/**
	 * Validate, update and save the addon and prices to the database
	 *
	 * @param  int   $id           ID of the addon
	 * @param  array $data         Information about addon
	 * @param  array $base_prices  Prices to associate to the addon model
	 * @param  array $prices       Seasonal prices to
	 *
	 * @return \Scubawhere\Entities\Addon
	 */
	public function update($id, $data, $base_prices, $prices) 
	{
    	$prices = $this->price_service->validatePrices($base_prices, $prices);
		$addon = $this->addon_repo->update($id, $data);

        if($prices['base']) $this->price_service->associatePrices($addon->basePrices(), $prices['base']);
        $addon->load(['basePrices']);
        return $addon;
	}

	/**
	 * Remove the addon from the database.
	 * 
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the addon, and the booking ids are then logged.
	 * 
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * 
	 * @throws \Exception
	 * 
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
		$addon = $this->addon_repo->getUsedInFutureBookings($id);

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