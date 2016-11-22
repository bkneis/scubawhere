<?php

namespace Scubawhere\Services;

use Scubawhere\Helper;
use Scubawhere\Entities\Trip;
use Scubawhere\Entities\Ticket;
use Scubawhere\Exceptions\Http\HttpConflict;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;
use Scubawhere\Repositories\TripRepoInterface;

class TripService {

	/** @var \Scubawhere\Repositories\TripRepo */
	protected $trip_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 *
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;

	public function __construct(TripRepoInterface $trip_repo, LogService $log_service) 
	{
		$this->trip_repo = $trip_repo;
		$this->log_service = $log_service;
	}

	/**
     * Get an trip for a company from its id
	 *
     * @param int $id ID of the trip
	 *
     * @return \Scubawhere\Entities\Trip
     */
	public function get($id) {
		return $this->trip_repo->get($id, ['locations', 'tags']);
	}

	/**
     * Get all trips for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->trip_repo->all(['locations', 'tags']);
	}

	/**
     * Get all trips for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->trip_repo->allWithTrashed(['locations', 'tags']);
	}

	/**
	 * Validate, create and save the trip and prices to the database
	 *
	 * @param array $data Data to autofill trip model
	 * @param array $locations
	 * @param array $tags
	 * 
	 * @return Trip
	 * @throws HttpNotAcceptable
	 */
	public function create(array $data, array $locations, array $tags)
	{
		// Until we implement functionality to upload and associate photos / videos to a trip these are nulled
		$data['photo'] = null;
		$data['video'] = null;
		if (empty($locations)) {
			throw new HttpNotAcceptable(__CLASS__.__METHOD__, ['At least one location is required.']);
		}
		if (empty($tags)) {
			throw new HttpNotAcceptable(__CLASS__.__METHOD__, ['At least one tag is required.']);
		}
		$trip = $this->trip_repo->create($data, $locations, $tags);
		return $trip;
	}

	/**
	 * Validate, update and save the trip and prices to the database
	 *
	 * @param  int   $id           ID of the trip
	 * @param  array $data         Information about trip
	 *
	 * @throws \ScubaWhere\Exceptions\Http\HttpNotAcceptable
	 *
	 * @return \Scubawhere\Entities\Trip
	 */
	public function update($id, $data, $locations, $tags) 
	{
		// Until we implement functionality to upload and associate photos / videos to a trip these are nulled
		$data['photo'] = null;
		$data['video'] = null;
		if (empty($locations) || is_null($locations)) {
			throw new HttpNotAcceptable(__CLASS__.__METHOD__, ['At least one location is required.']);
		}
		if (empty($tags) || is_null($tags)) {
			throw new HttpNotAcceptable(__CLASS__.__METHOD__, ['At least one tag is required.']);
		}
		$trip = $this->trip_repo->update($id, $data, $locations, $tags);
		return $trip;
	}

	/**
	 * Remove the trip from the database.
	 *
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the trip, and the booking ids are then logged.
	 *
	 * @param int $id ID of the trip
	 *
	 * @throws \Scubawhere\Exceptions\Http\HttpConflict
	 * @throws \Exception
	 */
	public function delete($id)
	{
		/**
		 * 1. Get the trip model with any tickets that it's in and any future departures.
		 * 2. Check for any tickets that require the trip (as a ticket must have atleast one trip)
		 * (3). If any tickets require the trip, log the ticket names and return a conflict
		 * 4. Check if there are any future departures of the trip, if so return a conflict
		 * 5. Unassign any tickets to the trip
		 * 6. Delete the trip
		 */

        $trip = Trip::onlyOwners()
			->with(['tickets',
			'departures' => function($query) {
				return $query->where('start', '>=', Helper::localTime()->format('Y-m-d H:i:s'));
			}])
			->findOrFail($id);

        $problem_tickets = array();

        // Check for any tickets that require the trip (the tickets only trip)
        foreach($trip->tickets as $obj) 
        {
            $ticket = Ticket::onlyOwners()
				->with('trips')
				->where('id', '=', $obj->id)
				->first();

            if(sizeof($ticket->trips) == 1) {
                array_push($problem_tickets, $obj);
            }
        }
        // Check for any problems, if so log them and return 409 error
        if(sizeof($problem_tickets) > 0)
        {
            $logger = $this->log_service->create('Attempting to delete the trip ' . $trip->name);
            foreach($problem_tickets as $prob) 
            {
                $logger->append('The ticket ' . $prob->name . ' uses soley this trip, please assign the ticket a diffrent trip to delete');
            }
            throw new HttpConflict(__CLASS__.__METHOD__, [
				'The trip could not be deleted as it has tickets that require it,',
				'Please <a href="#troubleshooting?id='. $logger->getId() .'">click here</a> for more information on how to delete it.'
			]);
        }
        // Check if the trip is scheduled for future departures
        elseif(sizeof($trip->departures) > 0)
        {
            $logger = $this->log_service->create('Attempting to delete the trip ' . $trip->name);
            foreach($trip->departures as $obj) 
            {
                $logger->append('The trip is scheduled to depart on ' . $obj->start . ', please delete the departure in scheduleing or edit it to use a diffrent trip');
            }
            throw new HttpConflict(__CLASS__.__METHOD__, [
				'The trip could not be deleted as it is scheduled for departure in the future, '.
				'please <a href="#troubleshooting?id='. $logger->getId() .'">click here</a>for more information on how to delete it.'
			]);
        }
        // If no problems, unassign the trips to the tickets
        else 
        {
            foreach($trip->tickets as $obj) 
            {
                \DB::table('ticket_trip')
                    ->where('trip_id', $trip->id)
                    ->where('ticket_id', $obj->id)
                    ->update(array('deleted_at' => \DB::raw('NOW()'))); 
            }
        }

        $trip->delete();	
	}

}