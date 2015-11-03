<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;
use ScubaWhere\Context;

class LocationController extends Controller {

	public function getAll()
	{
		return Context::get()->locations()->with('tags')->get();
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
			$location = Context::get()->locations()->findOrFail( Input::get('location_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The location could not be found!')), 404 ); // 404 Not Found
		}

		$description = Helper::sanitiseBasicTags(Input::get('description'));

		Context::get()->locations()->updateExistingPivot($location->id, ['description' => $description]);

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

		Context::get()->locations()->attach( Input::get('location_id') );

		return Response::json( array('status' => 'The location has been attached to your profile.'), 200 ); // 200 OK
	}

	public function postDetach()
	{
		try
		{
			if( !Input::get('location_id') ) throw new ModelNotFoundException();
			Context::get()->locations()->findOrFail( Input::get('location_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The location could not be found!')), 404 ); // 404 Not Found
		}

		// Check if location is currently used in a trip and if so, restrict detaching
		$check = Context::get()->trips()->whereHas('locations', function($query)
		{
			$query->where('id', Input::get('location_id'));
		})->limit(1)->count(); // limit(1) makes MySQL abort as soon as the first record is found, which is what we need (saves resources)

		if($check > 0)
			return Response::json( array('errors' => array('The location cannot be removed! You are still using it for trips.')), 409 ); // 409 Conflict

		Context::get()->locations()->detach( Input::get('location_id') );

		return Response::json( array('status' => 'The location has been detached from your profile.'), 200 ); // 200 OK
	}

}
