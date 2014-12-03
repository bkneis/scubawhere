<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getAll()
	{
		return Auth::user()->locations()->with('tags')->get();
	}

	public function getTags()
	{
		return Tag::where('for_type', 'Location')->orderBy('name')->get();
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

		// Check if location is currently used in a trip and if so, restrict detaching
		$check = Auth::user()->trips()->whereHas('locations', function($query)
		{
			$query->where('id', Input::get('location_id'));
		})->count();

		if($check > 0)
			return Response::json( array('errors' => array('The location cannot be removed! You are still using it for trips.')), 409 ); // 409 Conflict

		Auth::user()->locations()->detach( Input::get('location_id') );

		return Response::json( array('status' => 'The location has been detached from your profile.'), 200 ); // 200 OK
	}

}
