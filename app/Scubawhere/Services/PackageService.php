<?php

namespace Scubawhere\Services;

use Scubawhere\Entities\Booking;
use Scubawhere\Entities\Package;
use Scubawhere\Exceptions\Http\HttpConflict;
use Scubawhere\Repositories\PackageRepoInterface;

class PackageService {

	/** @var \Scubawhere\Repositories\PackageRepo */
	protected $package_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 *
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;

	public function __construct(PackageRepoInterface $package_repo,
								LogService $log_service,
								PriceService $price_service) 
	{
		$this->package_repo = $package_repo;
		$this->log_service = $log_service;
		$this->price_service = $price_service;
	}

	/**
     * Get an package for a company from its id
	 *
     * @param int $id ID of the package
	 *
     * @return \Scubawhere\Entities\Package
     */
	public function get($id) {
		return $this->package_repo->get($id, [
			'tickets',
			'courses',
			'courses.trainings',
			'courses.tickets',
			'accommodations',
			'addons',
			'basePrices',
			'prices'
		]);
	}

	/**
     * Get all packages for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->package_repo->all([
			'tickets',
			'courses',
			'courses.trainings',
			'courses.tickets',
			'accommodations',
			'addons',
			'basePrices',
			'prices'
		]);
	}

	/**
     * Get all packages for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->package_repo->allWithTrashed([
			'tickets',
			'courses',
			'courses.trainings',
			'courses.tickets',
			'accommodations',
			'addons',
			'basePrices',
			'prices'
		]);
	}

	/**
     * Get all packages for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAvailable() {
		return $this->package_repo->getAvailable();
	}

	/**
	 * Validate, create and save the package and prices to the database
	 *
	 * @param array $data Data to autofill package model
	 * @throws \Exception
	 * @return \Scubawhere\Entities\Package
	 */
	public function create(array $data)
	{
		try {
			\DB::beginTransaction();

			$package = Package::create($data)
				->syncPrices($data['prices'])
				->syncItems($data);
			
			\DB::commit();
		}
		catch(\Exception $e) {
			\DB::rollback();
			// @todo How to throw the correct http response and get the error message ??
			throw $e;
		}
		return $package;
	}

	/**
	 * Validate, update and save the package and prices to the database
	 *
	 * @param  int   $id           ID of the package
	 * @param  array $data         Information about package
	 *
	 * @throws \Exception
	 *
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model of the package
	 */
	public function update($id, $data) 
	{
		try 
		{
			\DB::beginTransaction();

			$package = $this->package_repo
				->get($id)
				->update($data)
				->syncPrices($data['prices'])
				->syncItems($data);

			\DB::commit();
		}
		catch(\Exception $e) {
			\DB::rollback();
			throw $e;
		}
		return $package;
	}

	/**
	 * Remove the package from the database.
	 *
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the package, and the booking ids are then logged.
	 *
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * @throws \Exception
	 *
	 * @param  int $id ID of the package
	 */
	public function delete($id)
	{
		/**
		 * 1. Get the package model with any sessions its tickets or classes is booked in for
		 * 2. Filter through bookings that have not been cancelled
		 * 3. If there are valid (not cancelled) bookings, log all of their refrences and return a conflict
		 * 4. Delete the package and its associated prices
		 */

		$package = $this->package_repo->getUsedInFutureBookings($id);

		$booking_ids = $package->bookingdetails
			->map(function($obj) {
				if($obj->session != null || $obj->training_session != null) {
					return $obj->booking_id;
				}
			})
			->toArray();

		$bookings = Booking::onlyOwners()
			->whereIn('id', $booking_ids)
			->get(['id', 'reference', 'status']);

		$quotes = $bookings->map(function($obj) {
			if($obj->status == 'saved') return $obj->id;
		})
		->toArray();

		Booking::onlyOwners()->whereIn('id', $quotes)->delete();

		$bookings = $bookings->filter(function($obj){
			if($obj->status != 'cancelled' && $obj->status != 'saved') return $obj;	
		})->toArray();

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the package, '
												. $package->name);
			foreach($bookings as $obj) {
				$logger->append('The package is used in the future in booking ' . '['.$obj['reference'].']');
			}
			throw new HttpConflict(__CLASS__ . __METHOD__,
				['The package could not be deleted as it is used in bookings in the future, '.
					'Please please <a href="#troubleshooting?id='. $logger->getId() .'">click here</a> for more info on how to delete it.']);
		}

		$package->delete();
		$this->price_service->delete('Package', $package->id);
	}

}