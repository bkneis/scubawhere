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
		/**
		 * 1. Get the location model with any trips associated to it and any future sessions related to
		 * 	  the location. i.e. if a ticket is used in a future booking, that contains a trip that uses this location
		 * 2. Check if there are any trips associated to the location
		 * (3). If not, then skip to 9
		 * 4. Check if any of the trips contain tickets that are booked in the future
		 * (5). If so, log the booking references and return a conflict
		 * 6. Check if any of the trips rely on this location (each trip needs atleast 1 location)
		 * (7). If so, log the trip names and return a conflict
		 * 8. Detach any trips from the location
		 * 9. Detach the location from the company and return OK 
		 */

		try
		{
			if( !Input::get('location_id') ) throw new ModelNotFoundException();
			$location = Context::get()->locations()
									  ->with(['trips.locations',
									  'trips.tickets.bookingdetails.session' => function($q) {
									      $q->where('start', '>=', Helper::localtime());
									  }])
									  ->findOrFail( Input::get('location_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The location could not be found!')), 404 ); // Not Found
        }

		if($location->trips)
		{
			$ids = array();
			$booking_ids = array();

			foreach($location->trips as $trip) 
			{
				if($trip->tickets)
				{
					foreach($trip->tickets as $ticket) 
					{
						if($ticket->bookingdetails)
						{
							$ids = $ticket->bookingdetails
										  ->map(function($obj) {
										      if($obj->session != null) return $obj->booking_id;
										  })
										  ->toArray();

							$booking_ids = array_merge($booking_ids, $ids);
						}
					}
				}
			}

			$bookings = Context::get()->bookings()
									  ->whereIn('id', $booking_ids)
									  ->get(['reference', 'status']);

			$bookings = $bookings->map(function($obj){
				if($obj->status != 'cancelled') return $obj;	
			})->toArray();

			$bookings = array_filter($bookings, function($obj){ return !is_null($obj); });

			if($bookings)
			{
				$logger = $this->log_service->create('Attempting to delete the location, '
													. $location->name);
				foreach($bookings as $obj) 
				{
					$logger->append('The location is used in the future in booking ' . $obj['reference'] .
									'. Please either cancel the booking, or assign the trips a diffrent'.
									' location.');
				}

				return Response::json(
					array('errors' => 
						array('The location could not be deleted as it is used in bookings in the future, '.
							'Please visit the error logs for more info on how to delete it.')
					), 409); // Conflict
			}

			$problem_trips = array();

			foreach($location->trips as $obj) 
			{
				if(sizeof($obj->locations) < 2) array_push($problem_trips, $obj);
			}

			if(sizeof($problem_trips) > 0)
			{
				$logger = $this->log_service->create('Attempting to delete the location ' . $location->name);
				foreach($problem_trips as $obj) 
				{
					$logger->append('The trip ' . $obj->name . ' uses this as its sole location, please assign '.
									'it a diffrent location');
				}
				return Response::json(
							array('errors' => 
								array('The location is required by some trips so it could not be deleted, '.
									  'please visit the error logs for more information')
							), 409); // Conflict
			}

			foreach($location->trips as $trip) 
			{
				DB::table('location_trip')
					->where('location_id', $location->id)
					->where('trip_id', $trip->id)
					->update(array('deleted_at' => DB::raw('NOW()'))); 
			}
		}

		Context::get()->locations()->detach( Input::get('location_id') );

		return Response::json( array('status' => 'Ok. Location deleted.'), 200);
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
