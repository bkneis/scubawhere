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
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
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
		 * with_full
		 */

		$data = Input::only('after', 'before', 'trip_id', 'ticket_id', 'package_id');

		$data['with_full'] = Input::get('with_full', false);

		// Transform parameter strings into DateTime objects
		$data['after']  = new DateTime( $data['after'] ); // Defaults to NOW, when parameter is NULL
		if( empty( $data['before'] ) )
		{
			if( $data['after'] > new DateTime('now') )
			{
				// If the submitted `after` date lies in the future, move the `before` date to return 1 month of results
				$data['before'] = clone $data['after']; // Shallow copies without reference to cloned object
				$data['before']->add( new DateInterval('P1M') ); // Extends the date 1 month into the future
			}
			else
			{
				// If 'after' date lies in the past or is NOW, return results up to 1 month into the future
				$data['before'] = new DateTime('+1 month');
			}
		}
		else
		{
			// If a 'before' date is submitted, simply use it
			$data['before'] = new DateTime( $data['before'] );
		}

		if( $data['after'] > $data['before'] )
		{
			return Response::json( array('errors' => array('The supplied \'after\' date is later than the given \'before\' date.')), 400 ); // 400 Bad Request
		}

		// Check the integrity of the supplied parameters
		$validator = Validator::make( $data, array(
			'after'      => 'date|required_with:before',
			'before'     => 'date',
			'trip_id'    => 'integer|min:1',
			'ticket_id'  => 'integer|min:1', // Here, we are not testing for 'exists:trips,id', because that would open the API for bruteforce tests of ALL existing trip_ids. trip_ids are private to the owning dive center and are not meant to be known by others.
			'package_id' => 'integer|min:1', // Same goes for packages
			'with_full'  => 'boolean'
		) );

		if( $validator->fails() )
			return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request

		$options = $data;

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
		$departures = Auth::user()->departures()->withTrashed()->with(/*'bookings', */'boat')
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
		// ->take(25)
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
		$departures = $departures->filter(function($departure) use ($package, $options)
		{
			$boatCapacity = $departure->getCapacityAttribute();
			if( $boatCapacity[0] >= $boatCapacity[1] )
			{
				// Session/Boat full/overbooked
				if( !$options['with_full'] )
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

		return Response::json( array('status' => 'OK. Session created', 'id' => $departure->id), 201 ); // 201 Created
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
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}

		if( empty($departure->timetable_id) )
		{
			// TODO Check if boat belongs to logged in company
			if( Input::get('start') )
				$departure->start   = Input::get('start');

			if( Input::get('boat_id') )
			{
				$departure->boat_id = Input::get('boat_id');

				$capacity = $departure->getCapacityAttribute();

				// TODO This next conditional is not checking if any tickets have been booked for the session that require a certain accomodation. It needs to be checked if this accomodation is also present on the new boat.
				if($capacity[0] > $capacity[1])
					return Response::json( array('errors' => array('The boat could not be changed. The new boat\'s capacity is too small.')), 406 ); // 406 Not Acceptable

				if($capacity[0] > 0 && Input::get('start') && Input::get('start') != $departure->start) {
					return Response::json( array('errors' => array('The session cannot be moved. It has already been booked.')), 409 ); // 409 Conflict
				}
			}
		}
		// If the session is part of a timetable and has been changed, check if request sent instructions on what to do
		elseif( Input::get('start') && Input::get('start') !== $departure->start)
		{
			switch( Input::get('handle_timetable') )
			{
				case 'only_this':
					// Remove this event from the timetable and set new time
					$capacity = $departure->getCapacityAttribute();
					if($capacity[0] > 0)
						return Response::json( array('errors' => array('The session cannot be moved. It has already been booked.')), 409 ); // 409 Conflict

					$departure->timetable_id = null;
					$departure->start        = Input::get('start');
				break;
				case 'following':

					// TODO Differenciate between "Yes, move everything anyway and notify customers" and "Clone booked sessions and deactivate old ones"

					// First, replicate the timetable
					$timetable = $departure->timetable()->first()->replicate();
					$timetable->save();

					$start = new DateTime( Input::get('start') );

					// Update all following session with new time and timetable_id
					// First, calculate offset between old_time and new_time
					$offset    = new DateTime($departure->start);
					$offset    = $offset->diff($start);
					$offsetSQL = $offset->format('%h:%i'); // hours:minutes

					// Single-Query MagicTM
					DB::update(
						"UPDATE `sessions` SET `timetable_id`=?, `start`=DATE_ADD(`start`, INTERVAL ? HOUR_MINUTE) WHERE `start`>=? AND `timetable_id`=?",
						array( $timetable->id, $offsetSQL, $departure->start, $departure->timetable_id )
					);

					return array('status' => 'OK. All sessions updated.');
				break;
				default:
					return Response::json( array('errors' => array('`handle_timetable` parameter is required.')), 400 ); // 400 Bad Request
				break;
			}
		}
		else
		{
			// Do nothing
			return array('status' => 'Nothing updated.');
		}

		if( !$departure->save() )
		{
			return Response::json( array('errors' => $departure->errors()->all()), 400 ); // 400 Bad Request
		}

		return array('status' => 'OK. Session updated.');
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
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}

		if( $departure->timetable_id )
		{
			switch( Input::get('handle_timetable') )
			{
				case 'only_this': break;
				case 'following':

					// Get all affected sessions
					$sessions = Auth::user()->departures()
						->where('start', '>=', $departure->start)
						->where('timetable_id', $departure->timetable_id)
						->with('bookingdetails')
						->get();

					$sessions->each( function($session)
					{
						if( $session->bookingdetails()->count() == 0 )
							$session->forceDelete();
						else
							$session->delete(); // SoftDelete
					});

					return array('status' => 'OK. All sessions either deleted or deactivated.');
				break;
				default:
					return Response::json( array('errors' => array('`handle_timetable` parameter is required.')), 400 ); // 400 Bad Request
				break;
			}
		}

		$departure->delete(); // SoftDelete

		return array('status' => 'OK. Session deactivated');
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
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}

		if( $departure->timetable_id )
		{
			switch( Input::get('handle_timetable') )
			{
				case 'only_this': break;
				case 'following':

					// Get all affected sessions
					$sessions = Auth::user()->departures()
						->where('start', '>=', $departure->start)
						->where('timetable_id', $departure->timetable_id)
						->with('bookingdetails')
						->get();

					$sessions->each( function($session)
					{
						if( $session->bookingdetails()->count() == 0 )
							$session->forceDelete();
						else
							$session->delete(); // SoftDelete
					});

					return array('status' => 'OK. All sessions either deleted or deactivated.');
				break;
				default:
					return Response::json( array('errors' => array('`handle_timetable` parameter is required.')), 400 ); // 400 Bad Request
				break;
			}
		}

		try
		{
			$departure->forceDelete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('Cannot delete session. It has already been booked!')), 409 ); // 409 Conflict
		}

		return array('status' => 'OK. Session deleted');
	}

}
