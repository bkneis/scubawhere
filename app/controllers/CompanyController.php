<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

/**
 * This controller lets you retrieve and save all data concerning companies. It will only return data for the logged in company. The methods in this controller require authentication.
 *
 * For documentation, please refer to http://scubawhere.com/docs
 */
class CompanyController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
        return Auth::user();
	}

	public function getBoats()
	{
		$boats =  Auth::user()->boats()->with('accommodations')->get();
		$accommodations = Auth::user()->accommodations()->get();

		return Response::json( array( 'boats' => $boats->toArray(), 'accommodations' => $accommodations->toArray() ) );
	}

	public function postBoats()
	{
		// Doing the accommodations first, because the boats rely on their correct IDs
		$presentRooms = array_flip( Auth::user()->accommodations()->lists('id') );
		$inputRooms = Input::get('accommodations');

		// Find out what accommodations got deleted
		$diffRooms = array_diff_key($presentRooms, $inputRooms);
		// Remove these deleted accommodations from the database
		if( count($diffRooms) > 0 && !Auth::user()->accommodations()->whereIn( 'id', array_keys($diffRooms) )->delete() )
		{
			return Response::json( array('errors' => array("Some accommodations could not be deleted because they are still assigned to boats. Please reload the page to see the current state.")), 409 ); // 409 Conflict
		}

		// Find all already present accommodations
		$intersectRooms = array_intersect_key($inputRooms, $presentRooms);
		// Update these present accommodations
		foreach($intersectRooms as $id => $details)
		{
			// Clean the Input array from unwanted fields
			$reducedDetails = array_intersect_key( $details, array_flip( array('name', 'description', 'photo') ) );

			$room = Auth::user()->accommodations()->find($id)->update( $reducedDetails );
		}

		// Find all new accommodations
		$newRooms = array_diff_key($inputRooms, $presentRooms);
		$keyPartners = array();
		foreach( $newRooms as $id => $details)
		{
			// Clean the Input array from unwanted fields
			$reducedDetails = array_intersect_key( $details, array_flip( array('name', 'description', 'photo') ) );

			$room = new Accommodation($reducedDetails);
			$room->company_id = Auth::user()->id;
			if( !$room->validate() )
			{
				$errors = $room->errors()->all();
				$errors[] = "Something was wrong with the submitted data. The new accomodation '" . Helper::sanitiseString($details['name']) . "' could not be created.";
				return Response::json( array('errors' => $errors), 406 ); // 406 Not Acceptable
			}

			// Save the relation between the old ID and the official ID in an array for later
			$room = Auth::user()->accommodations()->save($room);
			$keyPartners[$id] = $room->id;
		}

		// ############## Now on to the boats ###############

		// First, remove deleted boats from database
		$presentBoats = array_flip( Auth::user()->boats()->lists('id') );
		$inputBoats = Input::get('boats');

		// Find out what boats got deleted
		$diffBoats = array_diff_key($presentBoats, $inputBoats);
		// Remove these deleted boats from the database
		if( count($diffBoats) > 0 && !Auth::user()->boats()->whereIn( 'id', array_keys($diffBoats) )->delete() )
		{
			return Response::json( array('errors' => array('Some boats could not be deleted because they are still assigned to tickets or sessions. Please reload the page to see the current state.')), 409 ); // 409 Conflict
		}

		// Taking care of new and existing boats:
		foreach( Input::get('boats') as $id => $details )
		{
			// Only allow 'whitelisted' fields on the $details array
			$reducedDetails = array_intersect_key( $details, array_flip( array('name', 'description', 'capacity', 'photo') ) );

			try
			{
				// If the boat allready exists, we can just update its details
				if( !$id ) throw new ModelNotFoundException();
				$boat = Auth::user()->boats()->findOrFail($id);
				$boat->update($reducedDetails);
			}
			catch(ModelNotFoundException $e)
			{
				// In case the boat doesn't exist yet, we create it
				$boat = new Boat($reducedDetails);
				$boat = Auth::user()->boats()->save($boat);
			}

			if( isset( $details['accommodations'] ) )
			{
				// Construct the accommodations array to sync to the pivot table (needs to include the 'capacity' field for the pivot table)
				$sync = array();
				$utilisation = 0;
				foreach( $details['accommodations'] as $id => $capacity )
				{
					// Replace old IDs with their official ID
					if( array_key_exists($id, $keyPartners) )
						$id = $keyPartners[$id];

					$validator = Validator::make(
						array(
							'id' => $id,
							'capacity' => $capacity
						),
						array(
							'id' => 'integer',
							'capacity' => 'required|integer'
						)
					);

					if( $validator->fails() )
					{
						return Response::json( array('errors' => $validator->messages()->all()), 406 ); // 406 Not Acceptable
					}

					// Test whether the capacity of the room exceeds the overall capacity of the boat
					$utilisation += $capacity;
					if($utilisation > $boat->capacity)
					{
						// Silently jump all following accomodations and continue after the loop
						break;
					}

					$sync[$id] = array('capacity' => $capacity);
				}

				$boat->accommodations()->sync( $sync );
			}
		}

		// If all went well and no exeption was thrown, return OK
		return Response::json( array('status' => 'All went down OK.') );
	}

	public function getAccommodations()
	{
		return Auth::user()->accommodations()->get();
	}

	public function getTrips()
	{
		return Auth::user()->trips()->with('location', 'locations', 'triptypes'/*, 'tickets'*/)->get();
	}

	public function getTriptypes()
	{
		return Triptype::all();
	}

	public function postAddTrip()
	{
		$data = Input::only('name', 'description', 'duration', 'location_id');

		// Check optional fields
		if( Input::get('photo') )
		{
			$data['photo'] = Input::get('photo');
		}
		if( Input::get('video') )
		{
			$data['video'] = Input::get('video');
		}

		// Validate that locations and triptypes are provided
		$locations = Input::get('locations');
		$triptypes = Input::get('triptypes');
		if( !$locations || empty($locations) || !is_numeric( $locations[0] ) )
		{
			return Response::json( array('errors' => array('At least one location is required.')), 406 ); // 406 Not Acceptable
		}
		if( !$triptypes || empty($triptypes) || !is_numeric( $triptypes[0] ) )
		{
			return Response::json( array('errors' => array('At least one triptype is required.')), 406 ); // 406 Not Acceptable
		}

		$trip = new Trip($data);

		if( !$trip->validate() )
		{
			// The validator failed
			return Response::json( array('errors' => $trip->errors()->all()), 406 ); // 406 Not Acceptable
		}
		else
		{
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

			// Connect triptypes
			try
			{
				$trip->triptypes()->sync($triptypes);
			}
			catch(Exeption $e)
			{
				return Response::json( array('errors' => array('Could not assign triptypes to trip, \'triptypes\' array is propably erroneous.')), 400 ); // 400 Bad Request
			}

			// When no problems occur, we return a success response
			return Response::json( array('status' => 'OK. Trip Created', 'id' => $trip->id), 201 ); // 201 Created
		}
	}

	public function postEditTrip()
	{
		$data = Input::only('name', 'description', 'duration', 'location_id', 'photo', 'video');

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$trip = Auth::user()->trips()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('Can\'t find the trip with the submitted ID!')), 404 ); // 404 Not Found
		}

		if( !$trip->update($data) )
		{
			// When validation fails
			return Response::json( array('errors' => $trip->errors()->all()), 406 ); // 406 Not Acceptable
		}
		else
		{
			// Trip has been updated, let's reconnect it

			// Connect locations
			$locations = Input::get('locations');
			if( $locations && !empty($locations) && is_numeric( $locations[0] ) )
			{
				if( $trip->locations()->sync( $locations ) )
				{
					return Response::json( array('errors' => array('Could not assign locations to trip, \'locations\' array is propably erroneous.')), 400 ); // 400 Bad Request
				}
			}

			// Connect triptypes
			$triptypes = Input::get('triptypes');
			if( $triptypes && !empty($triptypes) && is_numeric( $triptypes[0] ) )
			{
				if( !$trip->triptypes()->sync( Input::get('triptypes') ) )
				{
					return Response::json( array('errors' => array('Could not assign triptypes to trip, \'triptypes\' array is propably erroneous.')), 400 ); // 400 Bad Request
				}
			}

			// When no problems occur, we return a success response
			return Response::json( array('status' => 'OK. Trip Updated'), 200 ); // 200 OK
		}
	}

	public function postDeleteTrip()
	{
		$id = Input::get('id');

		try
		{
			if( !$id ) throw new ModelNotFoundException();
			$trip = Auth::user()->trips()->findOrFail($id);
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('Cannot find the trip with the submitted ID!')), 404 ); // 404 Not Found
		}

		if( !$trip->delete() )
		{
			return Response::json( array('errors' => array('Cannot delete trip. It may still be connected to a booking!')), 409 ); // 409 Conflict
		}

		return Response::json( array('status' => 'OK. Trip deleted'), 200 ); // 200 OK
	}

	public function getLocations()
	{
		$area = Input::get('area'); // Structure: [north, west, south, east]
		if( is_array($area) )
		{
			$north = $area[0];
			$west  = $area[1];
			$south = $area[2];
			$east  = $area[3];

			// Cater for the southern hemisphere
			if( $north > $south )
			{
				$north = $area[2];
				$south = $area[0];
			}

			$locations = Location::whereBetween('latitude',  array($north, $south))
			                     ->whereBetween('longitude', array($west, $east))
			                     ->get();
		}
		else
		{
			$data = Input::only('latitude', 'longitude', 'limit');

			$validator = Validator::make( $data, array(
				'latitude'  => 'required|numeric|between:-90,90',
				'longitude' => 'required|numeric|between:-180,180',
				'limit'     => 'integer|min:1'
			) );

			if( $validator->fails() )
				return Response::json( array('errors' => $validator->messages()->all()), 406 ); // 406 Not Acceptable

			$lat   = $data['latitude'];
			$lon   = $data['longitude'];
			$limit = empty($data['limit']) ? 5 : $data['limit'];

			$timer = microtime(true);
			$locations = DB::table('locations')
			                 ->select(DB::raw('*, ((ACOS(SIN('.$lat.' * PI() / 180) * SIN(latitude * PI() / 180) + COS('.$lat.' * PI() / 180) * COS(latitude * PI() / 180) * COS(('.$lon.' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance'))
			                 ->orderBy('distance', 'asc')
			                 ->take($limit)
			                 ->get();
			$timer = round( ( microtime(true) - $timer ) * 1000, 3 );

			Log::info('The locations query took '.$timer.' ms to execute.('.$lat.', '.$lon.', '.$limit.')');

		}

		return $locations;
	}

	public function postAddLocation()
	{
		$data = Input::only('name', 'description', 'latitude', 'longitude', 'tags');

		$location = new Location($data);

		if( !$location->save() )
		{
			return Response::json( array('errors' => $location->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Location created', 'id' => $location->id), 201 ); // 201 Created
	}
}
