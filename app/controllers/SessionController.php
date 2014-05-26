<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

class SessionController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->sessions()->with('trip', 'boat')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->sessions()->get();
	}

	public function postAdd()
	{
		$data = Input::only('start', 'boat_id', 'timetable_id');

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

		$session = new Session($data);

		if( !$session->validate() )
		{
			return Response::json( array('errors' => $session->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$session = $trip->sessions()->save($session);

		return Response::json( array('status' => 'OK. Session created', 'id' => $session->id), 201 ); // 201 Created
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
			$session = Auth::user()->sessions()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}

		$session->delete(); // Soft delete

		return Response::json( array('status' => 'OK. Session deactivated'), 200 ); // 200 OK
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$session = Auth::user()->sessions()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}

		if( !$session->forceDelete() )
		{
			return Response::json( array('errors' => array('Cannot delete session. It has already been booked!')), 409 ); // 409 Conflict
		}

		return Response::json( array('status' => 'OK. Trip deleted'), 200 ); // 200 OK
	}

}
