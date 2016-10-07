<?php

namespace ScubaWhere\Services;

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use ScubaWhere\Exceptions\ConflictException;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\PackageRepoInterface;

class PackageService {

	/** 
	 *	Repository to access the package models
	 *	@var \ScubaWhere\Repositories\PackageRepo
	 */
	protected $package_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var ScubaWhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * @param PackageRepoInterface     Injected using \ScubaWhere\Repositories\PackageRepoServiceProvider
	 * @param LogService               Injected using laravel's IOC container
	 * @param PriceService             Injected using laravel's IOC container
	 */
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
     * @param int ID of the package
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an package for a company
     */
	public function get($id) {
		return $this->package_repo->get($id);
	}

	/**
     * Get all packages for a company
     * @param int ID of the package
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all packages for a company
     */
	public function getAll() {
		return $this->package_repo->all();
	}

	/**
     * Get all packages for a company including soft deleted models
     * @param int ID of the package
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all packages for a company including soft deleted models
     */
	public function getAllWithTrashed($id) {
		return $this->package_repo->allWithTrashed();
	}

	/**
     * Get all packages for a company
     * @param int ID of the package
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all packages for a company
     */
	public function getAvailable() {
		return $this->package_repo->allAvailable();
	}

	/**
	 * Validate, create and save the package and prices to the database
	 * @param  array Data to autofill package model
	 * @throws \ScubaWhere\Exceptions\InvalidInputException
	 * @throws \ScubaWhere\Exceptions\BadRequestException
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model for the package
	 */
	public function create($data, $tickets, $courses, $accommodations, $addons, $base_prices, $prices) 
	{
		try 
		{
			$this->package_repo->begin();

			$prices = $this->price_service->validatePrices($base_prices, $prices);
			$package = $this->package_repo->create($data, $tickets, $courses, $accommodations, $addons);
			
			$this->price_service->associatePrices($package->basePrices(), $prices['base']);
			if($prices['seasonal']) {
				$this->price_service->associatePrices($package->prices(), $prices['seasonal']);
			}
			
			$this->package_repo->finish();
		}
		catch(\Exception $e) {
			$this->package_repo->undo();
			throw $e;
		}
		return $package;
	}

	/**
	 * Validate, update and save the package and prices to the database
	 * @param  int   $id           ID of the package
	 * @param  array $data         Information about package
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model of the package
	 */
	public function update($id, $data, $tickets, $courses, $accommodations, $addons, $base_prices, $prices) 
	{
		try 
		{
			$this->package_repo->begin();

			$prices = $this->price_service->validatePrices($base_prices, $prices);
			$package = $this->package_repo->update($id, $data, $tickets, $courses, $accommodations, $addons);

			if($prices['base']) {
				$this->price_service->associatePrices($package->basePrices(), $prices['base']);
			}
			if($prices['seasonal']) {
				$this->price_service->associatePrices($package->prices(), $prices['seasonal']);
			}

			$this->package_repo->finish();
		}
		catch(\Exception $e) {
			$this->package_repo->undo();
			throw $e;
		}
		return $package;
	}

	/**
	 * Remove the package from the database.
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the package, and the booking ids are then logged
	 * @throws \ScubaWhere\Exceptions\ConflictException
	 * @throws Exception
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
		$package = \Package::onlyOwners()
			->with(['bookingdetails.session' => function($q) {
				$q->where('start', '>=', Helper::localtime());
			},
			'bookingdetails.training_session' => function($q) {
				$q->where('start', '>=', Helper::localtime());
			}
			])
			->findOrFail( $id );

		$booking_ids = $package->bookingdetails
			->map(function($obj) {
				if($obj->session != null || $obj->training_session != null) {
					return $obj->booking_id;
				}
			})
			->toArray();

		$bookings = \Booking::onlyOwners()
			->whereIn('id', $booking_ids)
			->get(['id', 'reference', 'status']);

		$quotes = $bookings->map(function($obj) {
			if($obj->status == 'saved') return $obj->id;
		})
		->toArray();

		\Booking::onlyOwners()->whereIn('id', $quotes)->delete();

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
			throw new ConflictException(
				['The package could not be deleted as it is used in bookings in the future, '.
					'Please visit the troubleshooting tab for more info on how to delete it.']);
		}

		$package->delete();
		$this->price_service->delete('Package', $package->id);
	}

}