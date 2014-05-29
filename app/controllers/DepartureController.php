<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

class DepartureController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->departures()->withTrashed()->with('trip', 'boat')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The departure could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->departures()->withTrashed()->get();
	}

	public function postAdd()
	{
		$data = Input::only('start', 'boat_id');

		try
		{
			if( !Input::get('trip_id') ) throw new ModelNotFoundException();
			$trip = Auth::user()->trips()->findOrFail( Input::get('trip_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		// Check if the boat_id exists and belongs to the logged in company
		try
		{
			if( !Input::get('boat_id') ) throw new ModelNotFoundException();
			Auth::user()->boats()->findOrFail( Input::get('boat_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boat could not be found.')), 404 ); // 404 Not Found
		}

		$departure = new Departure($data);

		if( !$departure->validate() )
		{
			return Response::json( array('errors' => $departure->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$departure = $trip->departures()->save($departure);

		return Response::json( array('status' => 'OK. Departure created', 'id' => $departure->id), 201 ); // 201 Created
	}

	/*
	public function postEdit()
	{
		//
	}
	*/

	public function postDeactivate()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Auth::user()->departures()->where('sessions.id', Input::get('id'))->firstOrFail();
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The departure could not be found.')), 404 ); // 404 Not Found
		}

		// $departure->delete(); // Soft delete

		// We made sure that the record exists and belongs to the logged-in user, so it's save to delete manually
		$now = date("Y-m-d H:i:s");
		DB::table('sessions')->where('id', Input::get('id'))->update(array('deleted_at' => $now, 'updated_at' => $now));

		return Response::json( array('status' => 'OK. Departure deactivated'), 200 ); // 200 OK
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Auth::user()->departures()->withTrashed()->where('sessions.id', Input::get('id'))->firstOrFail();
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The departure could not be found.')), 404 ); // 404 Not Found
		}

		// $departure->forceDelete(); // Doesn't work/doesn't do anything

		// We made sure that the record exists and belongs to the logged-in user, so it's save to delete manually
		//DB::table('sessions')->where('id', Input::get('id'))->delete();

		// Try to find the record again to see if it worked
		try
		{
			Auth::user()->departures()->where('sessions.id', Input::get('id'))->firstOrFail();
		}
		catch(ModelNotFoundException $e)
		{
			// Record not found, all is good
			return Response::json( array('status' => 'OK. Departure deleted'), 200 ); // 200 OK
		}

		return Response::json( array('errors' => array('Cannot delete departure. It has already been booked!')), 409 ); // 409 Conflict
	}

}
