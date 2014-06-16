<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

class DepartureController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->departures()->withTrashed()->with('trip', 'boat')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The departure could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->departures()->withTrashed()->get();
	}

	public function getFilter()
	{
		/**
		 * Valid input parameter
		 * ticket_id
		 * package_id
		 * after
		 * before
		 */

		try
		{
			if( !Input::get('ticket_id') ) throw new ModelNotFoundException();
			$ticket = Auth::user()->tickets()->findOrFail( Input::get('ticket_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}

		if( Input::get('package_id') )
		{
			try
			{
				$package = Auth::user()->packages()->findOrFail( Input::get('package_id') );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
			}
		}

		// Someone will kill me for this someday. I'm afraid it will be me. But here it goes anyway:
		$departures = Auth::user()->departures()->with('bookings', 'boat')
		->whereHas('trip', function($query)
		{
			$query->whereHas('tickets', function($query)
			{
				$query
				->where('id', Input::get('ticket_id'))
				->where(function($query)
				{
					// Conditional where clause (only when package_id is provided)
					if( Input::get('package_id') )
					{
						$query->whereHas('packages', function($query)
						{
							$query->where('id', Input::get('package_id'));
						});
					}
				});
			});
		})
		// Fetch sessions and conditionally filter by given dates
		->where(function($query)
		{
			if( Input::get('after') && !Input::get('before') )
				$query->where('start', '>=', Input::get('after'));

			elseif( !Input::get('after') && Input::get('before') )
				$query->where('start', '<=', Input::get('before'));

			elseif( Input::get('after') && Input::get('before') )
				$query
				->whereBetween('start', array(Input::get('after'), Input::get('before')));
		})->get();

		// Conditionally filter by boat
		if( $ticket->boats()->count() > 0 )
		{
			$boatIDs = $ticket->boats()->lists('id');
			$departures->filter(function($departure)
			{
				return in_array($departure->boat_id, $boatIDs);
			});
		}

		// Filter by capacity/availability
		$departures = $departures->filter(function($departure)
		{
			$boatCapacity = $departure->getCapacityAttribute();
			if( $boatCapacity[0] >= $boatCapacity[1] )
			{
				// Session/Boat already full/overbooked
				return false;
			}

			if( Input::get('package_id') )
			{
				$usedUp = $departure->bookings()->wherePivot('package_id', $package->id)->count();
				if( $usedUp >= $package->capacity )
				{
					return false;
				}
			}

			return true;
		});

		return $departures;
	}

	public function postAdd()
	{
		$data = Input::only('start', 'boat_id');

		try
		{
			if( !Input::get('trip_id') ) throw new ModelNotFoundException();
			$trip = Auth::user()->trips()->findOrFail( Input::get('trip_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		// Check if the boat_id exists and belongs to the logged in company
		try
		{
			if( !Input::get('boat_id') ) throw new ModelNotFoundException();
			Auth::user()->boats()->findOrFail( Input::get('boat_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boat could not be found.')), 404 ); // 404 Not Found
		}

		$departure = new Departure($data);

		if( !$departure->validate() )
		{
			return Response::json( array('errors' => $departure->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$departure = $trip->departures()->save($departure);

		return Response::json( array('status' => 'OK. Departure created', 'id' => $departure->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Auth::user()->departures()->where('sessions.id', Input::get('id'))->firstOrFail();
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The departure could not be found.')), 404 ); // 404 Not Found
		}
		// $id0 = Input::get('id'); // correct

		/**
		 * The above query works, it is just not assigning the correct model (or ID)
		 * We therefore get the model simply without authentication
		 *
		 * TODO Make this work with the correct way (above)
		 */
		$departure = Departure::find( Input::get('id') );

		// $id1 = $departure->id; // not correct, = 1 (?!)

		$departure->boat_id = Input::get('boat_id');

		$capacity = $departure->getCapacityAttribute();

		if($capacity[0] > $capacity[1])
			return Response::json( array('errors' => array('The boat could not be changed. The new boat\'s capacity is too small.')), 406 ); // 406 Not Acceptable

		if($capacity[0] > 0 && Input::get('start') && Input::get('start') != $departure->start) {
			return Response::json( array('errors' => array('The departure cannot be moved. It has already been booked.')), 409 ); // 409 Conflict
		}
		// $id2 = $departure->id;

		$data = Input::only('start', 'boat_id');
		// $id3 = $departure->id;

		if( !$departure->update($data) )
		{
			return Response::json( array('errors' => $departure->errors()->all()), 400 ); // 400 Bad Request
		}
		// $id4 = $departure->id;

		return Response::json( array('status' => 'OK. Departure updated.'/*, 'id' => $id0.','.$id1.','.$id2.','.$id3.','.$id4*/), 200 ); // 200 OK
	}

	public function postDeactivate()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Auth::user()->departures()->where('sessions.id', Input::get('id'))->firstOrFail();
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The departure could not be found.')), 404 ); // 404 Not Found
		}

		// $departure->delete(); // Soft delete

		// We made sure that the record exists and belongs to the logged-in user, so it's save to softDelete manually
		$now = date("Y-m-d H:i:s");
		DB::table('sessions')->where('id', Input::get('id'))->update(array('deleted_at' => $now, 'updated_at' => $now));

		return Response::json( array('status' => 'OK. Departure deactivated'), 200 ); // 200 OK
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Auth::user()->departures()->withTrashed()->where('sessions.id', Input::get('id'))->firstOrFail();
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The departure could not be found.')), 404 ); // 404 Not Found
		}

		// $departure->forceDelete(); // Doesn't work/doesn't do anything

		// We made sure that the record exists and belongs to the logged-in user, so it's save to delete manually
		DB::table('sessions')->where('id', Input::get('id'))->delete();

		// Try to find the record again to see if it worked
		try
		{
			Auth::user()->departures()->where('sessions.id', Input::get('id'))->firstOrFail();
		}
		catch(ModelNotFoundException $e)
		{
			// Record not found, all is good
			return Response::json( array('status' => 'OK. Departure deleted'), 200 ); // 200 OK
		}

		return Response::json( array('errors' => array('Cannot delete departure. It has already been booked!')), 409 ); // 409 Conflict
	}

}
