<?php

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Entities\Booking;
use Scubawhere\Entities\Timetable;
use Scubawhere\Entities\Departure;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepartureController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->departures()->withTrashed()->with('trip', 'boat')->where('sessions.id', Input::get('id'))->firstOrFail(array('sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}
	}

	/**
	 * Get the session, trip, boat and customer info for a session to generate the Passenger Manifest
	 *
	 * @param integer id The session ID to get the info for
	 */
	public function getManifest()
	{
		// First, we get the departure/session for which the manifest is and check if it exists
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Context::get()->departures()->withTrashed()->where('sessions.id', Input::get('id'))->with('trip', 'boat')->firstOrFail(array('sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		// Then, we get the associated customers through the bookingdetails, because we need to be able to filter by booking->status
		$details = Context::get()->bookingdetails()
			->where('session_id', Input::get('id'))
			->whereHas('booking', function($query)
			{
				$query->whereIn('status', Booking::$counted);
			})
			->with('booking.payments', 'booking.refunds', 'customer', 'addons')
			->get();

		// Now, we build an array of customers
		$customers = [];
		$details->each(function($detail) use (&$customers)
		{
			$customer = $detail->customer;

			// The front-end expects the customer->pivot object to be filled, so we assign it the bookingdetail, which we conveniently already have.
			$customer->pivot = $detail;

			// Also add the booking reference to display it in the manifest (the booking_id for linking is already in the pivot object)
			$customer->pivot->reference = $detail->booking->reference;

            $customer->pivot->notes = $detail->booking->comment;

			$customer->pivot->status = $detail->booking->status;

			$customer->pivot->absolute_price = $detail->booking->absolute_price;

			$customer->pivot->decimal_price = $detail->booking->decimal_price;

			$customer->pivot->payments = $detail->booking->payments;

			$customer->pivot->refunds = $detail->booking->refunds;

			$customer->pivot->addons = $detail->addons;

			// Just need to unset the customer from the bookingdetail/pivot so we do not transfer redundant data
			unset($customer->pivot->customer);
			unset($customer->pivot->booking);

			$customers[] = $customer;
		});

		// Assign and return
		$departure->customers = $customers;
		return $departure;
	}

	public function getAll()
	{
		return Context::get()->departures()->withTrashed()->get();
	}

	public function getToday()
	{
		$data = array(
			'with_full' => true,
			'after'     => Helper::localTime()->setTime(0, 0)->format('Y-m-d H:i:s'),
			'before'    => Helper::localTime()->setTime(23, 59)->format('Y-m-d H:i:s'),
		);

		Request::replace($data);

		return $this->getFilter();
	}

	public function getTomorrow()
	{
		$data = array(
			'with_full' => true,
			'after'     => Helper::localTime()->add(new DateInterval('P1D'))->setTime(0, 0)->format('Y-m-d H:i:s'),
			'before'    => Helper::localTime()->add(new DateInterval('P1D'))->setTime(23, 59)->format('Y-m-d H:i:s'),
		);

		Request::replace($data);

		return $this->getFilter();
	}

	public function getFilter()
	{
		/**
		 * Valid input parameter
		 * trip_id
		 * ticket_id
		 * package_id
		 * course_id
		 * after
		 * before
		 * with_full
		 */

		$data = Input::only('after', 'before', 'trip_id', 'ticket_id', 'package_id', 'course_id');

		$data['with_full'] = Input::get('with_full', true);

		// Transform parameter strings into DateTime objects
		$data['after'] = new \DateTime( $data['after'], new \DateTimeZone( Context::get()->timezone ) ); // Defaults to NOW, when parameter is NULL
		if( empty( $data['before'] ) )
		{
			/*
			if( $data['after'] > new DateTime('now', new DateTimeZone( Context::get()->timezone )) )
			{
				// If the submitted `after` date lies in the future, move the `before` date to return 1 month of results
				$data['before'] = clone $data['after']; // Shallow copies without reference to cloned object
				$data['before']->add( new DateInterval('P1M') ); // Extends the date 1 month into the future
			}
			else
			{
				// If 'after' date lies in the past or is NOW, return results up to 1 month into the future
				$data['before'] = new DateTime('+1 month', new DateTimeZone( Context::get()->timezone ));
			}
			*/

			// If no end date is specified, delete the variable to not mess up the validator
			unset($data['before']);
		}
		else
		{
			// If a 'before' date is submitted, simply use it
			$data['before'] = new \DateTime( $data['before'], new \DateTimeZone( Context::get()->timezone ) );
		}

		if( isset($data['before']) && $data['after'] > $data['before'] )
		{
			return Response::json( array('errors' => array('The supplied \'after\' date is later than the given \'before\' date.')), 400 ); // 400 Bad Request
		}

		// Check the integrity of the supplied parameters
		$validator = Validator::make( $data, array(
			'after'      => 'date|required_with:before',
			'before'     => 'date',
			'trip_id'    => 'integer|min:1',
			'ticket_id'  => 'integer|min:1|required_with:package_id', // Here, we are not testing for 'exists:trips,id', because that would open the API for bruteforce tests of ALL existing trip_ids. trip_ids are private to the owning dive center and are not meant to be known by others.
			'package_id' => 'integer|min:1', // Same goes for packages
			'course_id'  => 'integer|min:1',
			'with_full'  => 'boolean'
		) );

		if( $validator->fails() )
			return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request

		$options = $data;

		if( !empty( $options['trip_id'] ) )
		{
			try
			{
				$trip = Context::get()->trips()->findOrFail( $options['trip_id'] );
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
				$ticket = Context::get()->tickets()->findOrFail( $options['ticket_id'] );
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
				$package = Context::get()->packages()->findOrFail( $options['package_id'] );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$package = false;

		if( !empty( $options['course_id'] ) )
		{
			try
			{
				$course = Context::get()->courses()->findOrFail( $options['course_id'] );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The course could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$course = false;

		// Find if a *_available_for daterange restricts the result
		$available_for_from = false;
		$available_for_until = false;
		$ticket_available_for_from = false;
		$ticket_available_for_until = false;
		$package_available_for_from = false;
		$package_available_for_until = false;
		if($ticket)
		{
			$ticket_available_for_from  = $ticket->available_for_from ?: false;
			$ticket_available_for_until = $ticket->available_for_until ?: false;
		}
		if($package)
		{
			$package_available_for_from  = $package->available_for_from ?: false;
			$package_available_for_until = $package->available_for_until ?: false;
		}
		// ...
		if($ticket_available_for_from && $package_available_for_from)
			$available_for_from = $ticket_available_for_from >= $package_available_for_from ? $ticket_available_for_from : $package_available_for_from;
		else if($ticket_available_for_from && !$package_available_for_from)
			$available_for_from = $ticket_available_for_from;
		else if($package_available_for_from)
			$available_for_from = $package_available_for_from;

		if($ticket_available_for_until && $package_available_for_until)
			$available_for_until = $ticket_available_for_until <= $package_available_for_until ? $ticket_available_for_until : $package_available_for_until;
		else if($ticket_available_for_until && !$package_available_for_until)
			$available_for_until = $ticket_available_for_until;
		else if($package_available_for_until)
			$available_for_until = $package_available_for_until;

		if($available_for_from)  $available_for_from  = $available_for_from  . ' 00:00:00';
		if($available_for_until) $available_for_until = $available_for_until . ' 23:59:59';

		// Set the number of results to fetch
		$take = isset($options['before']) ? PHP_INT_MAX : 10;

		/*
		  We need to navigate the relationship-tree from departure/session via trip to
		  ticket and then (conditionally) to package.
		*/
		// Someone will kill me for this someday. I'm afraid it will be me. But here it goes anyway:
		$departures = Context::get()->departures()->withTrashed()->with(/*'bookings', */'boat', 'boat.boatrooms', 'trip')
		->whereHas('trip', function($query) use ($trip, $ticket, $package, $course)
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
			->where(function($query) use ($ticket, $package, $course)
			{
				if($ticket || $package || $course)
				{
					$query->whereHas('tickets', function($query) use ($ticket, $package, $course)
					{
						$query->where(function($query) use ($ticket)
						{
							// Conditional where clause (only when ticket_id is provided)
							if( $ticket )
							{
								$query->where('id', $ticket->id);
							}
						})
						->where(function($query) use ($package, $course)
						{
							// Conditional where clause (only when package_id is provided and course_id NOT)
							if( $package && !$course )
							{
								$query->whereHas('packages', function($query) use ($package)
								{
									$query->where('id', $package->id);
								});
							}
						})->where(function($query) use ($package, $course)
						{
							// Conditional where clause (only when course_id is provided)
							if( $course )
							{
								$query->whereHas('courses', function($query) use ($package, $course)
								{
									$query
									->where('id', $course->id)
									->where(function($query) use ($package)
									{
										// Conditional where clause (only when package AND course are provided)
										if($package)
										{
											$query->whereHas('packages', function($query) use ($package)
											{
												$query->where('id', $package->id);
											});
										}
									});
								});
							}
						});
					});
				}
			});
		})
		// Filter by dates
		->where(function($query) use ($options)
		{
			if(isset($options['before'])) {
				$query->whereBetween('start', array(
					$options['after']->format('Y-m-d H:i:s'),
					$options['before']->format('Y-m-d H:i:s')
				));
				$query->orWhereBetween(\DB::raw("ADDTIME(start, CONCAT(CEIL(trips.duration), ':', LPAD(FLOOR(trips.duration*60 % 60),2,'0')))"), array(
					$options['after']->format('Y-m-d H:i:s'),
					$options['before']->format('Y-m-d H:i:s')
				));
			} else {
				$query->where('start', '>=', $options['after']->format('Y-m-d H:i:s'));
			}
		})
		// Filter by available_for dates
		->where(function($query) use ($available_for_from)
		{
			if($available_for_from)
				$query->where('start', '>=', $available_for_from);
		})
		->where(function($query) use ($available_for_until)
		{
			if($available_for_until)
				$query->where('start', '<=', $available_for_until);
		})
		// ->with('trip', 'trip.tickets')
		->orderBy('start', 'ASC')
		->take($take)
		->get();

		// Conditionally filter by boat
		if( $ticket && $ticket->boats()->exists() )
		{
			$boatIDs = $ticket->boats()->lists('id');
			$departures = $departures->filter(function($departure) use ($boatIDs)
			{
				return $departure->boat_id === null || in_array($departure->boat_id, $boatIDs);
			});
		}

		// Conditionally filter by boatrooms
		if( $ticket && $ticket->boatrooms()->exists())
		{
			$boatroomIDs = $ticket->boatrooms()->lists('id');
			$departures = $departures->filter(function($departure) use ($boatroomIDs)
			{
				return $departure->boat_id === null || count( array_intersect($departure->boat->boatrooms()->lists('id'), $boatroomIDs) ) > 0;
			});
		}

		// Filter by capacity/availability
		if( !$options['with_full'] )
		{
			$departures = $departures->filter(function($departure) use ($course)
			{
				$capacity = $departure->getCapacityAttribute();
				if( $departure->boat_id !== null && $capacity[0] >= $capacity[1] )
				{
					// Session/Boat full/overbooked
					return false;
				}

				if( $course && !empty($course->capacity) )
				{
					$usedUp = $departure->bookingdetails()->whereHas('course', function($query) use ($course)
					{
						$query->where('id', $course->id);
					})->count();
					if( $usedUp >= $course->capacity )
					{
						return false;
					}
				}

				return true;
			});
		}

		return $departures;
	}

	public function postAdd()
	{
		$data = Input::only('start', 'boat_id');

		/*$isPast = Helper::isPast( $data['start'] );
		if( $isPast )
			return Response::json( array('errors' => array('Trips cannot be scheduled in the past.')), 403 ); // 403 Forbidden
		*/

		try
		{
			if( !Input::has('trip_id') ) throw new ModelNotFoundException();
			$trip = Context::get()->trips()->findOrFail( Input::get('trip_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		if($trip->boat_required)
		{
			// Check if the boat_id exists and belongs to the logged in company
			try
			{
				if( !Input::has('boat_id') ) throw new ModelNotFoundException();
				$boat = Context::get()->boats()->findOrFail( Input::get('boat_id') );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The boat could not be found.')), 404 ); // 404 Not Found
			}
		}

		$departure = new Departure($data);

		if($trip->boat_required)
		{
			if($this->isBoatAvailable(null, $boat->id, $data['start'], $trip->duration))
				return Response::json( array('errors' => array('The boat is already being used at this time.')), 406); // 406 Not Acceptable

			// Check if trip is overnight and if so, check if boat has boatrooms
			if($departure->isOvernight($trip) && $boat->boatrooms()->count() === 0)
				return Response::json( array('errors' => array('The boat cannot be used for this trip. It does not have cabins, which are required for overnight trips.')), 403 ); // 403 Forbidden
		}

		if( !$departure->validate() )
		{
			return Response::json( array('errors' => $departure->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$departure = $trip->departures()->save($departure);

		return Response::json( array('status' => 'OK. Trip scheduled', 'id' => $departure->id), 201 ); // 201 Created
	}

	private function isBoatAvailable($departure_id, $boat_id, $start, $duration)
	{	
		// Check if the boat is already being used during the submitted time
		$tripStart = new \DateTime( $start, new \DateTimeZone( Context::get()->timezone ) );
		$tripEnd   = clone $tripStart;

		$duration_hours   = floor($duration);
		$duration_minutes = round( ($duration - $duration_hours) * 60 );
		$tripEnd->add( new DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );

		$tripStart = $tripStart->format('Y-m-d H:i:s');
		$tripEnd   = $tripEnd->format('Y-m-d H:i:s');

		$overlappingSessions = Context::get()->departures()
			->where(function($q) use ($departure_id) {
				if($departure_id !== null) $q->where('sessions.id', '!=', $departure_id);
			})
			//->where('sessions.id', '!=', $departure_id)
			->with('trip')
			->where('boat_id', $boat_id)
			->where(function($q) use ($tripStart, $tripEnd) {
				$q->where(function($query) use ($tripStart, $tripEnd) {
					$query->where('start', '<=', $tripStart)
				  	  	  ->where(DB::raw("ADDTIME(start, CONCAT(CEIL(trips.duration), ':', LPAD(FLOOR(trips.duration*60 % 60),2,'0')))"), '>=', $tripEnd);
				})
				->orWhere(function($query) use ($tripStart, $tripEnd) {
					$query->where('start', '>=', $tripStart)
				  		  ->where('start', '<=', $tripEnd);
				})
				->orWhere(function($query) use ($tripStart, $tripEnd) {
					$query->where(DB::raw("ADDTIME(start, CONCAT(CEIL(trips.duration), ':', LPAD(FLOOR(trips.duration*60 % 60),2,'0')))"), '>=', $tripStart)
				  		  ->where(DB::raw("ADDTIME(start, CONCAT(CEIL(trips.duration), ':', LPAD(FLOOR(trips.duration*60 % 60),2,'0')))"), '<=', $tripEnd);
				});
			})
			->exists();

		return $overlappingSessions;	
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Context::get()->departures()->with('trip')->where('sessions.id', Input::get('id'))->firstOrFail(array('sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		/*$isPast = Helper::isPast( $departure->start );
		if( !empty($departure->deleted_at) || $isPast )
			return Response::json( array('errors' => array('Past or deactivated trips cannot be updated.')), 412 ); // 412 Precondition Failed
		*/

		if( empty($departure->timetable_id) )
		{
			if( Input::has('start') )
				$departure->start = Input::get('start');

			$capacity = $departure->capacity;
			if($capacity[0] > 0 && Input::has('start') && Input::get('start') != $departure->start)
			{
				return Response::json( array('errors' => array('The trip cannot be moved. It has already been booked.')), 409 ); // 409 Conflict
			}

			if( Input::has('boat_id') )
			{
				// Check if the boat_id exists and belongs to the logged in company
				try
				{
					$boat = Context::get()->boats()->findOrFail( Input::get('boat_id') );
				}
				catch(ModelNotFoundException $e)
				{
					return Response::json( array('errors' => array('The boat could not be found.')), 404 ); // 404 Not Found
				}
				$departure->boat_id = $boat->id;

				$capacity = $departure->capacity;

				// TODO This next conditional is not checking if any tickets have been booked for the session that require a certain accomodation. It needs to be checked if this accomodation is also present on the new boat.
				if($capacity[0] > $capacity[1])
					return Response::json( array('errors' => array('The boat could not be changed. The new boat\'s capacity is too small.')), 406 ); // 406 Not Acceptable
			}

		}
		// If the session is part of a timetable and has been changed, check if request sent instructions on what to do
		elseif((Input::get('start') && Input::get('start') !== $departure->start) || Input::get('boat_id'))
		{
			switch( Input::get('handle_timetable') )
			{
				case 'only_this':
					if(Input::get('boat_id'))
					{
						if(!$departure->update(array('boat_id' => Input::get('boat_id'))))
						{
							return Response::json(array('errors' => $departure->errors()->all()), 406);
						}
						$departure->timetable_id = null;
					}
					if(Input::get('start') != $departure->start)
					{
						// Remove this event from the timetable and set new time
						$capacity = $departure->getCapacityAttribute();
						if($capacity[0] > 0)
							return Response::json( array('errors' => array('The trip cannot be moved. It has already been booked.')), 409 ); // 409 Conflict

						$departure->timetable_id = null;
						$departure->start        = Input::get('start');
					}
				break;
				case 'following':
					// TODO Differenciate between "Yes, move everything anyway and notify customers" and "Clone booked sessions and deactivate old ones"
					if(Input::get('boat_id')) $boat_id = Input::get('boat_id');
					else					  $boat_id = $departure->boat_id;

					$start_dates = array();
					$departures = Timetable::where('id', $departure->timetable_id)
											->with('departures')
											->get()
											->each(function($obj) use (&$start_dates) {
												foreach($obj->departures as $session) 
												{
													$date = new \DateTime($session->start,
																new \DateTimeZone(Context::get()->timezone));
													array_push($start_dates, $date);
												}
											});

					//if($this->isBoatAvailable($boat_id, Input::get('start'), $departure->trip->duration))
					if(!$this->isBoatAvailableTimetable($departure->timetable_id, $boat_id, $start_dates, $departure->trip->duration))
					{
						return Response::json( array('errors' => 
							array('The boat is already being used at this time.')), 406); // 406 Not Acceptable
					}

					// First, replicate the timetable
					$timetable = $departure->timetable()->first()->replicate();
					$timetable->save();

					$start = new \DateTime( Input::get('start') );

					// Update all following session with new time and timetable_id
					// First, calculate offset between old_time and new_time
					$offset    = new \DateTime($departure->start);
					$offset    = $offset->diff($start);
					$offsetSQL = $offset->format('%h:%i'); // hours:minutes

					// Single-Query MagicTM
					DB::update(
						"UPDATE `sessions` SET `timetable_id`=?,
						`start`=DATE_ADD(`start`, INTERVAL ? HOUR_MINUTE),
						`boat_id`=?
						WHERE `start`>=? AND `timetable_id`=?",
						array( $timetable->id, $offsetSQL, $boat_id, $departure->start, $departure->timetable_id )
					);

					return array('status' => 'OK. All trips updated.');
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

		if($this->isBoatAvailable($departure->id, $departure->boat_id, $departure->start, $departure->trip->duration))
			return Response::json( array('errors' => array('The boat is already being used at this time.')), 406); // 406 Not Acceptable

		// Check if trip is overnight and if so, check if boat has boatrooms
		if($departure->isOvernight($departure->trip) && $departure->boat->boatrooms()->count() === 0)
			return Response::json( array('errors' => array('The boat cannot be used for this trip. It does not have cabins, which are required for overnight trips.')), 403 ); // 403 Forbidden

		if( !$departure->save() )
		{
			return Response::json( array('errors' => $departure->errors()->all()), 400 ); // 400 Bad Request
		}

		return array('status' => 'OK. Trip updated.');
	}

	private function isBoatAvailableTimetable($timetable_id, $boat_id, $start_dates, $duration)
	{	
		// Check if the boat is already being used during the submitted time

		$duration_hours   = floor($duration);
		$duration_minutes = round( ($duration - $duration_hours) * 60 );
		$end_dates = array();
		foreach($start_dates as &$obj) 
		{
			$end_date = new \DateTime($obj->format('Y-m-d H:i:s'));
			$end_date->add( new DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );
			$end_date->format('Y-m-d H:i:s');
			array_push($end_dates, $end_date);
		}

		usort($start_dates, function($a, $b) 
		{
			if($a == $b) return 0;
			return $a < $b ? -1 : 1;
		});

		usort($end_dates, function($a, $b) 
		{
			if($a == $b) return 0;
			return $a < $b ? -1 : 1;
		});

		$overlappingSessions = Context::get()->departures()
			->where('timetable_id', '!=', $timetable_id)
			->with('trip')
			->where('boat_id', '=', $boat_id)
			->where('start', '>=', $start_dates[0]->format('Y-m-d H:i:s'))
			->where('start', '<=', $end_dates[count($end_dates) - 1]->format('Y-m-d H:i:s'))
			->get();

		$available = true;
		$overlappingSessions->each(function($obj) use ($start_dates, $end_dates, &$available)
		{
			$duration_hours   = floor($obj->trip->duration);
			$duration_minutes = round( ($obj->trip->duration - $duration_hours) * 60 );
			$start = new \DateTime($obj->start);
			$end = clone $start;
			$end->add(new DateInterval('PT' . $duration_hours . 'H' . $duration_minutes . 'M'));

			// perform checks to see if they clash	
			for($i = 0; $i < count($start_dates); $i++)
			{
				if($start <= $start_dates[$i] && $end >= $end_dates[$i]) 
				{
					$available = false;
					return false; // @todo remove false, no need
				}
			}

			for($i = 0; $i < count($start_dates); $i++)
			{
				if($start >= $start_dates[$i] && $start <= $end_dates[$i])
				{
					$available = false;
					return false;
				}
			}

			for($i = 0; $i < count($start_dates); $i++)
			{
				if($end >= $start_dates[$i] && $end <= $end_dates[$i])
				{
					$available = false;
					return false;
				}
			}
		});

		return $available;	
	}

	public function postDeactivate()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Context::get()->departures()->where('sessions.id', Input::get('id'))->firstOrFail(array('sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		$isPast = Helper::isPast( $departure->start );
		if( $isPast )
			return Response::json( array('errors' => array('Past trips cannot be deactivated.')), 412 ); // 412 Precondition Failed


		$departure->delete(); // SoftDelete

		return array('status' => 'OK. Trip deactivated');
	}

	public function postRestore()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Context::get()->departures()->onlyTrashed()->where('sessions.id', Input::get('id'))->firstOrFail(array('sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		$isPast = Helper::isPast( $departure->start );
		if( $isPast )
			return Response::json( array('errors' => array('Past trips cannot be restored.')), 412 ); // 412 Precondition Failed

		$departure->restore();

		return array('status' => 'OK. Trip restored');
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Context::get()->departures()->withTrashed()->where('sessions.id', Input::get('id'))->firstOrFail(array('sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		$isPast = Helper::isPast( $departure->start );
		if( $isPast )
			return Response::json( array('errors' => array('Past trips cannot be deleted.')), 412 ); // 412 Precondition Failed

		if( $departure->timetable_id )
		{
			switch( Input::get('handle_timetable') )
			{
				case 'only_this': break;
				case 'following':

					// Get all affected sessions
					$sessions = Context::get()->departures()
						->where('start', '>=', $departure->start)
						->where('timetable_id', $departure->timetable_id)
						->with('bookingdetails.booking')
						->get();

					$sessions->each( function($session) {
						if($session->bookingdetails()->count() === 0) {
							$session->forceDelete();
						} else {
							$session->delete();
						}
					});

					return array('status' => 'OK. All trips either deleted or deactivated.');
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
			return Response::json( array('errors' => array('Cannot delete trip. It has already been booked!')), 409 ); // 409 Conflict
		}

		return array('status' => 'OK. Trip deleted');
	}

}
