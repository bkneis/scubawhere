<?php
use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationController extends Controller {

    protected $log_service;

    public function __construct(LogService $log_service)
    {
        $this->log_service = $log_service;
    }

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
			$location = Context::get()->locations()->with('trips')->findOrFail( Input::get('location_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The location could not be found!')), 404 ); // 404 Not Found
        }

        $problem_trips = array();

        if($location->getDeleteableAttribute()) {
		    Context::get()->locations()->detach( Input::get('location_id') );
        }
        else {
            foreach($location->trips as $obj) {
                $trip_locations = Context::get()->trips()
                                                ->with('locations')
                                                ->where('id', '=',  $obj->id)
                                                ->first()
                                                ->locations;
                if(sizeof($trip_locations) == 1) {
                    array_push($problem_trips, $obj);
                }
            }
            if(sizeof($problem_trips) > 0) {
                $logger = $this->log_service->create('Attempting to delete the location ' . $location->name);
                foreach($problem_trips as $prob) 
                {
                    $logger->append('The location could not be deleted as the trip ' . $prob->name . ' uses this as its sole location. Please remove add another location to the trip');
                }
			return Response::json( array('errors' => array('The location could not be deleted as trips are using it, please visit the error logs for more information on how to fix this')), 409 ); // 404 Not Found
            }
            else {
                foreach($location->trips as $trip) 
                {
                    DB::table('location_trip')
                        ->where('location_id', $location->id)
                        ->where('trip_id', $trip->id)
                        ->update(array('deleted_at' => DB::raw('NOW()'))); 
                }
		        Context::get()->locations()->detach( Input::get('location_id') );
            }
        }
        return Response::json(array('status' => 'Success. Location deleted.'), 200);
	}

    /*
     *
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
     */

}
