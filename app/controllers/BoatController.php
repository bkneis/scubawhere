<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

class BoatController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

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

		return Response::json( array('status' => 'OK. Boat created', 'id' => $boat->id), 201 ); // 201 Created
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
			// Input must be of type <input name="boatrooms[1][capacity]" value="20">
			//                                boatroom_id --^   capacity value --^
			$boatrooms = Input::get('boatrooms');

			// TODO Validate that boatrooms belong to logged in user
			$boat->boatrooms()->sync( $boatrooms );
		}
		else {
			// Remove all boatrooms from the boat
			$boat->boatrooms()->detach();
		}

		if( !$boat->update($data) )
		{
			return Response::json( array('errors' => $boat->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Boat updated.'), 200 ); // 200 OK
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

		try
		{
			$boat->forceDelete();
		}
		catch(QueryException $e)
		{
			if( $boat->tickets()->count() > 0 )
				return Response::json( array('errors' => array('The boat can not be removed because it is still used in tickets.')), 409); // 409 Conflict

			// Check if the boat is only used in past sessions
			// Gets latest session's start date and compares to user's local time
			$isPast = Helper::isPast( $boat->departures()->withTrashed()->orderBy('start', 'DESC')->first()->start );
			if( gettype($isPast) === 'object' ) // Is error Response
				return $isPast;
			if( !$isPast )
				return Response::json( array('errors' => array('The boat can not be removed because it is still used in future sessions.')), 409); // 409 Conflict

			// Need to recreate the Boat object, because otherwise it will try to execute the forceDelete SQL query
			// TODO Is there a better way?
			$boat = Auth::user()->boats()->find( Input::get('id') );
			$boat->delete(); // Soft delete
		}

		return array('status' => 'Ok. Boat deleted');
	}
}
