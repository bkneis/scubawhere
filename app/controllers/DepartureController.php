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
			return Auth::user()->departures()->withTrashed()->with('trip', 'boat')->where('sessions.id', Input::get('id'))->firstOrFail();
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
		 * trip_id
		 * ticket_id
		 * package_id
		 * after
		 * before
		 */

		$data = Input::only('after', 'before', 'trip_id', 'ticket_id', 'package_id');

		// Check the integrity of the supplied parameters
		$validator = Validator::make( $data, array(
			'after'      => 'date',
			'before'     => 'date',
			'trip_id'    => 'integer|min:1',
			'ticket_id'  => 'integer|min:1', // Here, we are not testing for 'exists:trips,id', because that would open the API for bruteforce tests of ALL existing trip_ids. trip_ids are private to the owning dive center and are not meant to be known by others.
			'package_id' => 'integer|min:1' // Same goes for packages
		) );

		if( $validator->fails() )
			return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request

		// Tranform parameter strings into DateTime objects
		$data['after']  = new DateTime( $data['after'] );
		$data['before'] = new DateTime( $data['before'] );

		$options = array(
			'after'      => new DateTime(),
			'before'     => new DateTime('+ 1 month'),
			'trip_id'    => null,
			'ticket_id'  => null,
			'package_id' => null
		);

		// Join the default options and the submitted filter parameters
		$options = array_merge($options, $data);

		if( !empty( $options['trip_id'] ) )
		{
			try
			{
				$ticket = Auth::user()->trips()->findOrFail( $options['trip_id'] );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$trip = false;

		if( !empty( $options['ticket_id'] ) )
		{
			try
			{
				$ticket = Auth::user()->tickets()->findOrFail( $options['ticket_id'] );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$ticket = false;

		if( !empty( $options['package_id'] ) )
		{
			try
			{
				$package = Auth::user()->packages()->findOrFail( $options['package_id'] );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$package = false;

		/*
		  We need to navigate the relationship-tree from departure/session via trip to
		  ticket and then (conditionally) to package.
		*/
		// Someone will kill me for this someday. I'm afraid it will be me. But here it goes anyway:
		$departures = Auth::user()->departures()->with('bookings', 'boat')
		->whereHas('trip', function($query) use ($trip, $ticket, $package)
		{
			$query
			->where(function($query) use ($trip)
			{
				// Filter by trip_id
				if($trip)
				{
					$query->where('id', $trip->id);
				}
			})
			->whereHas('tickets', function($query) use ($ticket, $package)
			{
				$query
				->where(function($query) use ($ticket)
				{
					// Conditional where clause (only when ticket_id is provided)
					if($ticket)
					{
						$query->where('id', $ticket->id);
					}
				})
				->where(function($query) use ($package)
				{
					// Conditional where clause (only when package_id is provided)
					if( $package )
					{
						$query->whereHas('packages', function($query) use ($package)
						{
							$query->where('id', $package->id);
						});
					}
				});
			});
		})
		// Filter by dates
		->whereBetween('start', array(
			$options['after']->format('Y-m-d H:i:s'),
			$options['before']->format('Y-m-d H:i:s')
		))
		// ->with('trip', 'trip.tickets')
		->orderBy('start', 'ASC')
		->take(25)
		->get();

		// Conditionally filter by boat
		if( $ticket && $ticket->boats()->count() > 0 )
		{
			$boatIDs = $ticket->boats()->lists('id');
			$departures->filter(function($departure) use ($boatIDs)
			{
				return in_array($departure->boat_id, $boatIDs);
			});
		}

		// Filter by capacity/availability
		$departures = $departures->filter(function($departure) use ($package)
		{
			$boatCapacity = $departure->getCapacityAttribute();
			if( $boatCapacity[0] >= $boatCapacity[1] )
			{
				// Session/Boat already full/overbooked
				return false;
			}

			if( $package )
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
