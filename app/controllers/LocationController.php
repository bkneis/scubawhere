<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

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

	public function postUpdate()
	{
		try
		{
			if( !Input::get('location_id') ) throw new ModelNotFoundException();
			$location = Auth::user()->locations()->findOrFail( Input::get('location_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The location could not be found!')), 404 ); // 404 Not Found
		}

		$description = Helper::sanitiseBasicTags(Input::get('description'));

		Auth::user()->locations()->updateExistingPivot($location->id, ['description' => $description]);

		return ['status' => 'OK. Location updated.'];
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
		})->limit(1)->count(); // limit(1) makes MySQL abort as soon as the first record is found, which is what we need (saves resources)

		if($check > 0)
			return Response::json( array('errors' => array('The location cannot be removed! You are still using it for trips.')), 409 ); // 409 Conflict

		Auth::user()->locations()->detach( Input::get('location_id') );

		return Response::json( array('status' => 'The location has been detached from your profile.'), 200 ); // 200 OK
	}

}
