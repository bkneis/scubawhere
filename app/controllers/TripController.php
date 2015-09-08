<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class TripController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->trips()->withTrashed()->with(
				array(
					'locations',
					'tags',
					'tickets',
				)
			)->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->trips()->with(
			array(
				'locations',
				'tags',
			)
		)->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->trips()->withTrashed()->with(
			array(
				'locations',
				'tags',
			)
		)->get();
	}

	public function getTags()
	{
		return Tag::where('for_type', 'Trip')->orderBy('name')->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'duration');

		// Check optional fields
		if( Input::has('boat_required') )
		{
			$data['boat_required'] = Input::get('boat_required'); // If not present in input array, defaults to TRUE
		}
		if( Input::has('photo') )
		{
			$data['photo'] = Input::get('photo');
		}
		if( Input::has('video') )
		{
			$data['video'] = Input::get('video');
		}

		// Validate that locations and tags are provided
		$locations = Input::get('locations');
		$tags      = Input::get('tags');
		if( !$locations || empty($locations) || !is_numeric( $locations[0] ) )
		{
			return Response::json( array('errors' => array('At least one location is required.')), 406 ); // 406 Not Acceptable
		}
		if( !$tags || empty($tags) || !is_numeric( $tags[0] ) )
		{
			return Response::json( array('errors' => array('At least one tag is required.')), 406 ); // 406 Not Acceptable
		}

		$trip = new Trip($data);

		if( !$trip->validate() )
		{
			// The validator failed
			return Response::json( array('errors' => $trip->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Input has been validated, save the model
		$trip = Auth::user()->trips()->save($trip);

		// Trip has been created, let's connect it
		// Connect locations
		try
		{
			$trip->locations()->sync($locations);
		}
		catch(Exception $e)
		{
			return Response::json( array('errors' => array('Could not assign locations to trip, \'locations\' array is propably erroneous.')), 400 ); // 400 Bad Request
		}

		// Connect tags
		try
		{
			$trip->tags()->sync($tags);
		}
		catch(Exeption $e)
		{
			return Response::json( array('errors' => array('Could not assign tags to trip, \'tags\' array is propably erroneous.')), 400 ); // 400 Bad Request
		}

		// When no problems occur, we return a success response
		return Response::json( array('status' => 'OK. Trip created', 'id' => $trip->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		$data = Input::only('name', 'description', 'duration');

		// Check optional fields
		if( Input::has('photo') )
		{
			$data['photo'] = Input::get('photo');
		}
		if( Input::has('video') )
		{
			$data['video'] = Input::get('video');
		}

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$trip = Auth::user()->trips()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('Can\'t find the trip with the submitted ID!')), 404 ); // 404 Not Found
		}

		// Validate that locations and tags are provided
		$locations = Input::get('locations');
		$tags      = Input::get('tags');
		if( !$locations || empty($locations) || !is_numeric( $locations[0] ) )
		{
			return Response::json( array('errors' => array('At least one location is required.')), 406 ); // 406 Not Acceptable
		}
		if( !$tags || empty($tags) || !is_numeric( $tags[0] ) )
		{
			return Response::json( array('errors' => array('At least one tag is required.')), 406 ); // 406 Not Acceptable
		}

		if( !$trip->update($data) )
		{
			// When validation fails
			return Response::json( array('errors' => $trip->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Trip has been updated, let's reconnect it

		// Connect locations
		if( !$trip->locations()->sync( $locations ) )
		{
			return Response::json( array('errors' => array('Could not assign locations to trip, \'locations\' array is propably erroneous.')), 400 ); // 400 Bad Request
		}

		// Connect tags
		if( !$trip->tags()->sync( $tags ) )
		{
			return Response::json( array('errors' => array('Could not assign tags to trip, \'tags\' array is propably erroneous.')), 400 ); // 400 Bad Request
		}

		// When no problems occur, we return a success response
		return Response::json( array('status' => 'OK. Trip updated'), 200 ); // 200 OK
	}

	/*public function postDeactivate()
	{

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$trip = Auth::user()->trips()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		$trip->delete();

		return array('status' => 'OK. Trip deactivated');
	}

	public function postRestore()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$trip = Auth::user()->trips()->onlyTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		$trip->restore();

		return array('status' => 'OK. Trip restored');
	}*/

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$trip = Auth::user()->trips()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			$trip->forceDelete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('The trip can not be removed currently because it has tickets or active trips assigned to it.'/*.' Try deactivating it instead.'*/)), 409); // 409 Conflict
		}

		return array('status' => 'Ok. Trip deleted');
	}

}
