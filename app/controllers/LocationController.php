<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getAll()
	{
		return Auth::user()->locations()->get();
	}

	public function postAttach()
	{
		try
		{
			if( !Input::get('location_id') ) throw new ModelNotFoundException();
			Location::findOrFail( Input::get('location_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The location could not be found!')), 404 ); // 404 Not Found
		}

		Auth::user()->locations()->attach( Input::get('location_id') );

		return Response::json( array('status' => 'The location has been attached to your profile.'), 200 ); // 200 OK
	}

	public function postDetach()
	{
		try
		{
			if( !Input::get('location_id') ) throw new ModelNotFoundException();
			Auth::user()->locations()->findOrFail( Input::get('location_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The location could not be found!')), 404 ); // 404 Not Found
		}

		Auth::user()->locations()->detach( Input::get('location_id') );

		return Response::json( array('status' => 'The location has been detached from your profile.'), 200 ); // 200 OK
	}

}
