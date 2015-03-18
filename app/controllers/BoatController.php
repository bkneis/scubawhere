<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

class BoatController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->boats()->with('boatrooms')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boat could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->boats()->with('boatrooms')->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->boats()->withTrashed()->with('boatrooms')->get();
	}

	public function postAdd()
	{
		$data = Input::only(
			'name',
			'description',
			'capacity'
		);

		$boat = new Boat($data);

		if( !$boat->validate() )
		{
			return Response::json( array('errors' => $boat->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$boat = Auth::user()->boats()->save($boat);

		// Boat has been created, let's connect it with its boatrooms
		// Input must be of type <input name="boatrooms[1][capacity]" value="20">
		//                                boatroom_id --^   capacity value --^
		if( Input::has('boatrooms') )
		{
			$boatrooms = Input::get('boatrooms');

			// TODO Validate that boatrooms belong to logged in user
			$boat->boatrooms()->sync( $boatrooms );
		}

		return Response::json( array('status' => '<b>OK</b> Boat created', 'id' => $boat->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boat = Auth::user()->boats()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boat could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'name',
			'description',
			'capacity'
		);

		if( Input::has('boatrooms') )
		{
			$oldBoatrooms = array();
			$boat->boatrooms()->get()->each(function($boatroom) use (&$oldBoatrooms)
			{
				$oldBoatrooms[$boatroom->id] = array('capacity' => $boatroom->pivot->capacity);
			});

			// Input must be of type <input name="boatrooms[1][capacity]" value="20">
			//                                boatroom_id --^   capacity value --^
			$newBoatrooms = Input::get('boatrooms');

			// Foreach edited boatroom:
			// 1. If removed, check if the boatroom is booked for future bookings
			// 2. If capacity got smaller, check if the capacity is still enough for future bookings

			$removedBoatrooms = array_diff_key($oldBoatrooms, $newBoatrooms);

			$keptBoatrooms = array_intersect_key($oldBoatrooms, $newBoatrooms);
			$editedBoatrooms = array();
			foreach($keptBoatrooms as $id => $boatroom)
			{
				if((int) $oldBoatrooms[$id]['capacity'] > (int) $newBoatrooms[$id]['capacity'])
					$editedBoatrooms[$id] = $boatroom;
			}

			// 1. case
			foreach($removedBoatrooms as $id => $null)
			{
				$boatroom = Boatroom::find($id);
				if( $boatroom->bookingdetails()->whereHas('departure', function($query)
				{
					return $query->where('start', '>=', Helper::localTime()->format('Y-m-d H:i:s'));
				})->count() > 0 )
					return Response::json( array('errors' => array('The cabin "' . $boatroom->name . '" can not be removed because it is booked for future sessions.')), 409); // 409 Conflict
			}

			// 2. case
			foreach($editedBoatrooms as $id => $null)
			{
				$boatroom = Boatroom::find($id);
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

			// TODO Validate that boatrooms belong to logged in user
			$boat->boatrooms()->sync( $newBoatrooms );
		}
		else {
			// Remove all boatrooms from the boat
			$boat->boatrooms()->detach();
		}

		if( !$boat->update($data) )
		{
			return Response::json( array('errors' => $boat->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => '<b>OK</b> Boat updated'), 200 ); // 200 OK
	}

	/*
	public function postDeactivate()
	{

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boat = Auth::user()->boats()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boat could not be found.')), 404 ); // 404 Not Found
		}

		$boat->delete();

		return array('status' => 'OK. Boat deactivated');
	}

	public function postRestore()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boat = Auth::user()->boats()->onlyTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boat could not be found.')), 404 ); // 404 Not Found
		}

		$boat->restore();

		return array('status' => 'OK. Boat restored');
	}
	*/

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boat = Auth::user()->boats()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boat could not be found.')), 404 ); // 404 Not Found
		}

		// Need to check this before attempting the delete, because the relationship is not protected by a foreign key in the database
		/*
		if( $boat->tickets()->exists() )
			return Response::json( array('errors' => array('The boat can not be removed because it is still used in active tickets.')), 409); // 409 Conflict
		*/

		try
		{
			$boat->forceDelete();

			// Manually remove from ticketable table, as no foreign key possible
			$boat->tickets()->detach();
		}
		catch(QueryException $e)
		{
			// Deletion can only fail when the boat is used in sessions

			// Check if the boat is only used in past sessions
			// Gets latest session's start date and compares to user's local time
			$isPast = Helper::isPast( $boat->departures()->withTrashed()->orderBy('start', 'DESC')->first()->start );
			if( !$isPast )
				return Response::json( array('errors' => array('The boat can not be removed because it is still used in future trips.')), 409); // 409 Conflict

			// Need to recreate the Boat object, because otherwise it will try to execute the forceDelete SQL query
			// TODO Is there a better way?
			// Edit: No, not really, since the ->forcing property is private to the Eloquent object and thus not changeable from the outside.
			$boat = Auth::user()->boats()->find( Input::get('id') );
			$boat->delete(); // Soft delete
		}

		return array('status' => 'Ok. Boat deleted');
	}
}
