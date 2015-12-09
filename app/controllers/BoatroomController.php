<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Context;

class BoatroomController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The cabin could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->boatrooms()->get();
	}

	public function postAdd()
	{
		$data = Input::only(
			'name',
			'description'
		);

		$boatroom = new Boatroom($data);

		if( !$boatroom->validate() )
		{
			return Response::json( array('errors' => $boatroom->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$boatroom = Context::get()->boatrooms()->save($boatroom);

		return Response::json( array('status' => 'OK. Cabin created', 'id' => $boatroom->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Context::get()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The cabin could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'name',
			'description'
		);

		if( !$boatroom->update($data) )
		{
			return Response::json( array('errors' => $boatroom->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Cabin updated'), 200 ); // 200 OK
	}

	/*
	public function postDeactivate()
	{

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Context::get()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The cabin could not be found.')), 404 ); // 404 Not Found
		}

		$boatroom->delete();

		return array('status' => 'OK. Cabin deactivated');
	}

	public function postRestore()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Context::get()->boatrooms()->onlyTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The cabin could not be found.')), 404 ); // 404 Not Found
		}

		$boatroom->restore();

		return array('status' => 'OK. Cabin restored');
	}
	*/

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Context::get()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The cabin could not be found.')), 404 ); // 404 Not Found
		}

		// TODO Evaluate removing this
		if( $boatroom->boats->exists() )
			return Response::json( array('errors' => array('The cabin can not be removed because it is still used in boats.')), 409); // 409 Conflict

		/*
		if( $boatroom->tickets->exists() )
			return Response::json( array('errors' => array('The cabin can not be removed because it is still used in tickets.')), 409); // 409 Conflict
		*/

		// TODO Evaluate removing this
		if( $boatroom->bookingdetails()->whereHas('departure', function($query)
		{
			return $query->where('start', '>=', Helper::localTime()->format('Y-m-d H:i:s'));
		})->exists() )
			return Response::json( array('errors' => array('The cabin can not be removed because it is booked for future sessions.')), 409); // 409 Conflict

		if( $boatroom->bookingdetails()->exists() )
			$boatroom->delete(); // softDeletes
		else
		{
			$boatroom->forceDelete();

			// Manually remove from ticketable table, as no foreign key possible
			$boatroom->tickets()->detach();
		}

		return array('status' => 'Ok. Cabin deleted');
	}
}
