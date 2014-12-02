<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class BoatroomController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boatroom could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->boatrooms()->get();
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

		$boatroom = Auth::user()->boatrooms()->save($boatroom);

		return Response::json( array('status' => 'OK. Boatroom created', 'id' => $boatroom->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Auth::user()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boatroom could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'name',
			'description'
		);

		if( !$boatroom->update($data) )
		{
			return Response::json( array('errors' => $boatroom->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Boatroom updated.'), 200 ); // 200 OK
	}

	/*
	public function postDeactivate()
	{

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Auth::user()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boatroom could not be found.')), 404 ); // 404 Not Found
		}

		$boatroom->delete();

		return array('status' => 'OK. Boatroom deactivated');
	}

	public function postRestore()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Auth::user()->boatrooms()->onlyTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boatroom could not be found.')), 404 ); // 404 Not Found
		}

		$boatroom->restore();

		return array('status' => 'OK. Boatroom restored');
	}
	*/

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Auth::user()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boatroom could not be found.')), 404 ); // 404 Not Found
		}

		if( $boatroom->boats->count() > 0 )
			return Response::json( array('errors' => array('The boatroom can not be removed because it is still used in boats.')), 409); // 409 Conflict

		if( $boatroom->tickets->count() > 0 )
			return Response::json( array('errors' => array('The boatroom can not be removed because it is still used in tickets.')), 409); // 409 Conflict

		if( $boatroom->bookingdetails()->whereHas('departure', function($query)
			{
				return $query->where('start', '>=', Helper::localTime()->format('Y-m-d H:i:s'));
			})->count() > 0 )
			return Response::json( array('errors' => array('The boatroom can not be removed because it is booked for future sessions.')), 409); // 409 Conflict

		if( $boatroom->bookingdetails->count() > 0 )
			$boatroom->delete(); // softDeletes
		else
			$boatroom->forceDelete();

		return array('status' => 'Ok. Boatroom deleted');
	}
}
