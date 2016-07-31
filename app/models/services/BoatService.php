<?php
namespace Services\Boat;

use Repositories\Boat\BoatRepository;
use ScubaWhere\Repositories\Boat\BoatRoomRepository;

class BoatService
{
	protected $repo;
	protected $boatRoomRepo;

	public function __construct(BoatRepository $boatRepo, BoatRoomRepository $boatRoomRepo)
	{
		$this->repo = $boatRepo;
		$this->boatRoomRepo = $boatRoomRepo;
	}

	public function all()
	{
		return $this->repo->all();
	}

	public function allWithTrashed()
	{
		return $this->repo->allWithTrashed();
	}

	public function get($id)
	{
		return $this->repo->get($id);
	}

	public function createWithBoatrooms($data, $boatrooms)
	{
		$boat = $this->repo->create($data);

		// Boat has been created, let's connect it with its boatrooms
		if($boatrooms)
		{
			// TODO Validate that boatrooms belong to logged in user
			$boat->boatrooms()->sync( $boatrooms );
		}

		return $boat;
	}

	private function getNewBoatrooms($boat, $boatrooms)
	{
		$oldBoatrooms = array();
		$boat->boatrooms()
			->get()
			->each(function($boatroom) use (&$oldBoatrooms)
			{
				$oldBoatrooms[$boatroom->id] = array('capacity' => $boatroom->pivot->capacity);
			});

		$newBoatrooms = $boatrooms;
		$removedBoatrooms = array_diff_key($oldBoatrooms, $newBoatrooms);

		$keptBoatrooms = array_intersect_key($oldBoatrooms, $newBoatrooms);
		$editedBoatrooms = array();
		
		foreach($keptBoatrooms as $id => $boatroom)
		{
			if((int) $oldBoatrooms[$id]['capacity'] > (int) $newBoatrooms[$id]['capacity'])
				$editedBoatrooms[$id] = $boatroom;
		}

		// 1. If removed, check if the boatroom is booked for future bookings
		foreach($removedBoatrooms as $id => $null)
		{
			$boatroom = $this->boatRoomRepo->get($id);
			if( $boatroom->bookingdetails()->whereHas('departure', function($query)
				{
					return $query->where('start', '>=', Helper::localTime()->format('Y-m-d H:i:s'));
				})->count() > 0 )
				return Response::json( array('errors' => array('The cabin "' . $boatroom->name . '" can not be removed because it is booked for future sessions.')), 409); // 409 Conflict
		}

		// 2. If capacity got smaller, check if the capacity is still enough for future bookings
		foreach($editedBoatrooms as $id => $null)
		{
			$boatroom = $this->boatRoomRepo->get($id);
			$groupedSessions = $boatroom->bookingdetails()->whereHas('departure', function($query)
			{
				return $query->where('start', '>=', Helper::localTime()->format('Y-m-d H:i:s'));
			})->orderBy('session_id')->get();

			foreach($groupedSessions as $sessions)
			{
				if(count($sessions) > (int) $newBoatrooms[$id]['capacity'] )
					return Response::json( array('errors' => array('The capacity of "' . $boatroom->name . '" can not be reduced below ' . count($sessions) . '.')), 409); // 409 Conflict
			}
		}

		return $newBoatrooms;
	}

	public function update($id, $data, $boatrooms)
	{
		$boat = $this->repo->get($id);

		if($boatrooms)
		{
			$newBoatRooms = $this->getNewBoatrooms($boat, $boatrooms);

			// TODO Validate that boatrooms belong to logged in user
			$boat->boatrooms()->sync( $newBoatRooms );
		}
		else {
			// Remove all boatrooms from the boat
			$boat->boatrooms()->detach();
		}

		if( !$boat->update($data) ) throw new InvalidInputException($boat->errors()->all());

		return $boat;

	}
}