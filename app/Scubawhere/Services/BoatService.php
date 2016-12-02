<?php

namespace Scubawhere\Services;

use Scubawhere\Helper;
use Scubawhere\Exceptions\ConflictException;
use Scubawhere\Repositories\BoatRepoInterface;
use Scubawhere\Repositories\BoatroomRepoInterface;

class BoatService {

	/** @var \Scubawhere\Repositories\BoatRepo */
	protected $boat_repo;

	/** @var \Scubawhere\Repositories\BoatroomRepo */
	protected $boatroom_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * 
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;


	public function __construct(BoatRepoInterface $boat_repo,
								LogService $log_service,
								BoatroomRepoInterface $boatroom_repo) 
	{
		$this->boat_repo     = $boat_repo;
		$this->log_service   = $log_service;
		$this->boatroom_repo = $boatroom_repo;
	}

	/**
     * Get an boat for a company from its id
	 *
     * @param int $id ID of the boat
	 *
     * @return \Scubawhere\Entities\Boat
     */
	public function get($id) {
		return $this->boat_repo->get($id, ['boatrooms']);
	}

	/**
     * Get all boats for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->boat_repo->all(['boatrooms']);
	}

	/**
     * Get all boats for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->boat_repo->allWithTrashed(['boatrooms']);
	}

	/**
	 * Validate, create and save the boat and prices to the database
	 *
	 * @param array $data
	 * @param array $boatrooms
	 *
	 * @return \Scubawhere\Entities\Boat
	 */
	public function create($data, $boatrooms) 
	{
		$boat = $this->boat_repo->create($data);
		if(!empty($boatrooms)) {
			$boat->syncBoatrooms($boatrooms);
		}
		return $boat;
	}

	/**
	 * Validate, update / save the boat to the database and update its associated boatrooms
	 *
	 * @todo I feel that this should be broken down abit more, some of the functionality
	 * could belong to the Boat model and maybe some to the repo aswell
	 *
	 * @param int   $id           ID of the boat
	 * @param array $data         Information about boat
	 * @param array $boatrooms    List of boatroom ID's to associate to the boat
	 *
	 * @throws ConflictException
	 *
	 * @return \Scubawhere\Entities\Boat
	 */
	public function update($id, array $data, array $boatrooms)
	{
    	$boat = $this->boat_repo->update($id, $data);
    	if(!empty($boatrooms))
    	{
            $oldBoatrooms = array();
            $boat->boatrooms()->get()->each(function($boatroom) use (&$oldBoatrooms)
            {
                $oldBoatrooms[$boatroom->id] = array('capacity' => $boatroom->pivot->capacity);
            });

            $newBoatrooms = $boatrooms;

            // Foreach edited boatroom:
            // 1. If removed, check if the boatroom is booked for future bookings
            // 2. If capacity got smaller, check if the capacity is still enough for future bookings

            $removedBoatrooms = array_diff_key($oldBoatrooms, $newBoatrooms);
            $keptBoatrooms    = array_intersect_key($oldBoatrooms, $newBoatrooms);
            $editedBoatrooms  = array();
            
            foreach($keptBoatrooms as $id => $boatroom)
            {
                if((int) $oldBoatrooms[$id]['capacity'] > (int) $newBoatrooms[$id]['capacity'])
                    $editedBoatrooms[$id] = $boatroom;
            }

            // 1. case
            foreach($removedBoatrooms as $id => $null)
            {
                //$boatroom = Boatroom::find($id);
                $boatroom = $this->boatroom_repo->get($id);
                if( $boatroom->bookingdetails()->whereHas('departure', function($query)
                {
                    return $query->where('start', '>=', Helper::localTime()->format('Y-m-d H:i:s'));
                })->count() > 0 )
                	throw new ConflictException(['The cabin "' . $boatroom->name . '" can not be removed because it is booked for future sessions.']);
            }

            // 2. case
            foreach($editedBoatrooms as $id => $null)
            {
            	$boatroom = $this->boatroom_repo->get($id);
                //$boatroom = Boatroom::find($id);
                $groupedSessions = $boatroom->bookingdetails()->whereHas('departure', function($query)
                {
                    return $query->where('start', '>=', Helper::localTime()->format('Y-m-d H:i:s'));
                })->orderBy('session_id')->get();

                foreach($groupedSessions as $sessions)
                {
                    if(count($sessions) > (int) $newBoatrooms[$id]['capacity'] )
                    	throw new ConflictException(['The capacity of "' . $boatroom->name . '" can not be reduced below ' . count($sessions) . '.']);
                }
            }

            // TODO Validate that boatrooms belong to logged in user
            $boat->boatrooms()->sync( $newBoatrooms );

    	} else {
			// Remove all boatrooms from the boat
			$boat->boatrooms()->detach();
		}

		return $boat;
	}

	/**
	 * Remove the boat from the database.
	 *
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the boat, and the booking ids are then logged.
	 *
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * @throws \Exception
	 *
	 * @param int $id ID of the boat
	 */
	public function delete($id)
	{
		//$boat = Context::get()->boats()->with('futureDepartures', 'tickets', 'boatrooms')->findOrFail($id);
		$boat = $this->boat_repo->get($id);
		$boat->futureDepartures();
		$boat->tickets();
		$boat->boatrooms();

		if(!$boat->deleteable)
		{
			$logger = $this->log_service->create('Attempting to delete the boat ' . $boat->name);

			$timetable_trips = array();
			foreach($boat->futureDepartures as $obj) 
			{
				if($obj->timetable_id != null) {
					$timetable_trips[$obj->timetable_id][] = $obj;
				}
				else {
					$logger->append('The boat cannot be deleted as it has a departure scheduled for ' 
						. $obj->start . '. Please assign a diffrent boat or delete the session to delete the boat.');	
				}
			}
			foreach($timetable_trips as $trip) 
			{
				$logger->append('The boat cannot be deleted as it has a timetabled trip associated with it. The next trip on the '.
					'timetable is ' . $trip[0]->start . '. Please assign a diffrent boat to the timetable or delete the timetable in order to delete the boat.');	
			}
			throw new ConflictException(
				['The boat has future departures associated to it, please <a href="#troubleshooting?id='. $logger->getId() .'">click here</a> to see more information on how to delete it.']);
		}
		else
		{
			foreach($boat->tickets as $obj) 
			{
				\DB::table('ticketables')
					->where('ticketable_type', 'Boat')
					->where('ticketable_id', $boat->id)
					->where('ticket_id', $obj->id)
					->update(array('deleted_at' => \DB::raw('NOW()')));    
			}	
			foreach($boat->boatrooms as $obj) 
			{
				\DB::table('boat_boatroom')
					->where('boat_id', $boat->id)
					->where('boatroom_id', $obj->id)
					->update(array('deleted_at' => \DB::raw('NOW()')));    
			}
		}

		$boat->delete();	
	}

}
