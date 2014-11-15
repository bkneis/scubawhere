<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

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
		// TODO Validate input
		// Input must be of type <input name="boatrooms[1][capacity]" value="2">
		//                                boatroom_id --^   capacity value --^
		if( Input::has('boatrooms') )
		{
			$boatrooms = Input::get('boatrooms');
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

		if( !$boat->update($data) )
		{
			return Response::json( array('errors' => $boat->errors()->all()), 406 ); // 406 Not Acceptable
		}

		if( Input::has('boatrooms') )
		{
			$boatrooms = Input::get('boatrooms');
			$boat->boatrooms()->sync( $boatrooms );
		}
		else {
			// Remove all boatrooms from the boat
			$boat->boatrooms()->detach();
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
			// $boat->forceDelete();
			$boat->delete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('The boat can not be removed because it is still used in sessions.')), 409); // 409 Conflict
		}

		return array('status' => 'Ok. Boat deleted');
	}
}
