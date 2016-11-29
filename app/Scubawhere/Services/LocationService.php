<?php

namespace Scubawhere\Services;

use Scubawhere\Exceptions\MethodNotSupportedException;
use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Services\LogService;
use Scubawhere\Exceptions\Http\HttpConflict;
use Scubawhere\Exceptions\Http\HttpBadRequest;
use Scubawhere\Repositories\LocationRepoInterface;

class LocationService {

	/** @var \Scubawhere\Repositories\LocationRepo */
	protected $location_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;


	public function __construct(LocationRepoInterface $location_repo, LogService $log_service) 
	{
		$this->location_repo = $location_repo;
		$this->log_service = $log_service;
	}

	/**
     * Get an location for a company from its id
	 *
     * @param int $id ID of the location
     *
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an location for a company
     */
	public function get($id) {
		return $this->location_repo->get($id, ['tags']);
	}

	/**
     * Get all locations for a company
     * @param int ID of the location
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all locations for a company
     */
	public function getAll() {
		return $this->location_repo->all(['tags']);
	}

	/**
     * Get all locations for a company including soft deleted models
     * @param int ID of the location
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all locations for a company including soft deleted models
     */
	public function getAllWithTrashed() {
		return $this->location_repo->allWithTrashed(['tags']);
	}

	/**
	 * Validate, create and save the location and prices to the database
	 *
	 * @param  array Data to autofill location model
	 *
	 * @throws \Scubawhere\Exceptions\Http\HttpBadRequest
	 *
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model for the location
	 */
	public function create($data, $tags) 
	{
		$location = $this->location_repo->create($data);
		// Sync tags
		if($tags) {
			try {
				$location->tags()->sync($tags);
			}
			catch(\Exception $e) {
				throw new HttpBadRequest(__CLASS__.__METHOD__, ['Could not assign tags to location, \'tags\' array is propably erroneous.']);
			}
		}
		return $location;
	}

	/**
	 * Validate, update and save the location and prices to the database
	 *
	 * @param  int   $id           ID of the location
	 * @param  array $description  Information about location
	 *
	 * @return \Scubawhere\Entities\Location
	 */
	public function update($id, $description) 
	{
		$description = Helper::sanitiseBasicTags($description);
    	return $this->location_repo->update($id, $description);
	}

	/**
	 * @throws MethodNotSupportedException
	 */
	public function delete($id)
	{
		throw new MethodNotSupportedException(['error']);
	}

	/**
	 * Attach an existing location to a companies profile
	 *
	 * @param int $id ID of the location
	 */
	public function attach($id) 
	{
		$location = $this->get($id);
		Context::get()->locations()->attach( $location->id );
	}

	/**
	 * Dettach an existing location from a companies profile
	 *
	 * @param int $id ID of the location
	 *
	 * @throws HttpConflict
	 *
	 * @return \Scubawhere\Entities\Location
	 */
	public function dettach($id) 
	{
		/**
		 * 1. Get the location model with any trips associated to it and any future sessions related to
		 * 	  the location. i.e. if a ticket is used in a future booking, that contains a trip that uses this location
		 * 2. Check if there are any trips associated to the location, if not skip to 9
		 * 3. Check if any of the trips contain tickets that are booked in the future
		 * (4). If so, log the booking references and return a conflict
		 * 5. Check if any of the trips rely on this location (each trip needs atleast 1 location)
		 * (6). If so, log the trip names and return a conflict
		 * 7. Detach any trips from the location
		 * 8. Detach the location from the company and return OK 
		 */

		// STEP 1.
		$location = Context::get()->locations()
			->with(['trips.locations',
			'trips.tickets.bookingdetails.session' => function($q) {
				$q->where('start', '>=', Helper::localtime());
			}])
			->findOrFail( $id );

		// STEP 2.
		if($location->trips)
		{
			$ids = array();
			$booking_ids = array();
			// STEP 3.
			foreach($location->trips as $trip) 
			{
				if($trip->tickets)
				{
					foreach($trip->tickets as $ticket) 
					{
						if($ticket->bookingdetails)
						{
							$ids = $ticket->bookingdetails
								->map(function($obj) {
									if($obj->session != null) return $obj->booking_id;
								})
								->toArray();

							$booking_ids = array_merge($booking_ids, $ids);
						}
					}
				}
			}

			$bookings = Context::get()->bookings()
				->whereIn('id', $booking_ids)
				->get(['reference', 'status']);

			$bookings = $bookings->map(function($obj){
				if($obj->status != 'cancelled') return $obj;	
			})
			->toArray();

			$bookings = array_filter($bookings, function($obj){ return !is_null($obj); });

			// STEP 4.
			if($bookings)
			{
				$logger = $this->log_service->create('Attempting to delete the location, '. $location->name);
				foreach($bookings as $obj) 
				{
					$logger->append('The location is used in the future in booking ' . '['.$obj['reference'].']' .
									'. Please either cancel the booking, or assign the trips a diffrent'.
									' location.');
				}
				throw new HttpConflict(__CLASS__ . __METHOD__,
					['The location could not be deleted as it is used in bookings in the future, '.
							'Please visit the error logs for more info on how to delete it.']);
			}

			$problem_trips = array();

			// STEP 5.
			foreach($location->trips as $obj) {
				if(sizeof($obj->locations) < 2) array_push($problem_trips, $obj);
			}
			// STEP 6.
			if(sizeof($problem_trips) > 0)
			{
				$logger = $this->log_service->create('Attempting to delete the location ' . $location->name);
				foreach($problem_trips as $obj) 
				{
					$logger->append('The trip ' . $obj->name . ' uses this as its sole location, please assign '.
									'it a diffrent location');
				}
				throw new HttpConflict(__CLASS__ . __METHOD__,
					['The location is required by some trips so it could not be deleted, '.
									  'please visit the error logs for more information']);
			}
			// STEP 7.
			foreach($location->trips as $trip) 
			{
				\DB::table('location_trip')
					->where('location_id', $location->id)
					->where('trip_id', $trip->id)
					->update(array('deleted_at' => \DB::raw('NOW()'))); 
			}
		}
		// STEP 8.
		Context::get()->locations()->detach( $id );
	}

}