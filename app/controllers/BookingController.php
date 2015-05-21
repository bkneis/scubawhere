<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

class BookingController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->with(
				'lead_customer',
					'lead_customer.country',
				'bookingdetails',
					'bookingdetails.customer',
						'bookingdetails.customer.country',
					'bookingdetails.session',
						'bookingdetails.session.trip',
					'bookingdetails.ticket',
					'bookingdetails.packagefacade',
						'bookingdetails.packagefacade.package',
							// 'bookingdetails.packagefacade.package.tickets',
					'bookingdetails.training_session',
						'bookingdetails.training_session.training',
					'bookingdetails.course',
					'bookingdetails.addons',
				'accommodations',
				'payments',
					// 'payments.currency',
					'payments.paymentgateway',
				'refunds',
					// 'refunds.currency',
					'refunds.paymentgateway'
			)->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		$pricedPackagefacades = [];
		$pricedCourses = [];

		$booking->bookingdetails->each(function($detail) use ($booking, &$pricedPackagefacades, &$pricedCourses)
		{
			$limitBefore = in_array($booking->status, ['reserved', 'expired', 'confirmed']) ? $detail->created_at : false;

			if($detail->packagefacade_id !== null)
			{
				if(!array_key_exists($detail->packagefacade_id, $pricedPackagefacades))
				{
					// Find the first departure datetime that is booked in this package
					// $bookingdetails = $detail->packagefacade->bookingdetails()->with('departure', 'training_session')->get();
					$firstDetail = $booking->bookingdetails->filter(function($d) use ($detail)
					{
						return $d->packagefacade_id === $detail->packagefacade_id;
					})
					->sortBy(function($detail)
					{
						if($detail->departure)
							return $detail->departure->start;
						else
							return $detail->training_session->start;
					})->first();

					if($firstDetail->departure)
						$start = $firstDetail->departure->start;
					else
						$start = $firstDetail->training_session->start;

					$firstAccommodation = $booking->accommodations->filter(function($a) use ($detail)
					{
						return $a->pivot->packagefacade_id === $detail->packagefacade_id;
					})
					->sortBy(function($accommodation)
					{
						return $accommodation->pivot->start;
					})->first();

					if(!empty($firstAccommodation))
					{
						$detailStart = new DateTime($start);
						$accommStart = new DateTime($firstAccommodation->pivot->start);

						$start = ($detailStart < $accommStart) ? $detailStart : $accommStart;

						$start = $start->format('Y-m-d H:i:s');
					}

					// Calculate the package price at this first datetime and sum it up
					$detail->packagefacade->package->calculatePrice($start, $limitBefore);

					$pricedPackagefacades[$detail->packagefacade_id] = $detail->packagefacade->package->decimal_price;
				}
				else
					$detail->packagefacade->package->decimal_price = $pricedPackagefacades[$detail->packagefacade_id];
			}
			elseif($detail->course_id !== null)
			{
				$identifier = $detail->booking_id . '-' . $detail->customer_id . '-' . $detail->course_id;

				if(!array_key_exists($identifier, $pricedCourses))
				{
					// Find the first departure or class datetime that is booked in this course
					// $bookingdetails = $detail->course->bookingdetails()->with('departure', 'training_session')->get();
					$firstDetail = $booking->bookingdetails->filter(function($d) use ($detail)
					{
						return $d->course_id === $detail->course_id;
					})
					->sortBy(function($detail)
					{
						if($detail->departure)
							return $detail->departure->start;
						else
							return $detail->training_session->start;
					})->first();

					if($firstDetail->departure)
						$start = $firstDetail->departure->start;
					else
						$start = $firstDetail->training_session->start;

					// Calculate the package price at this first departure datetime and sum it up
					$detail->course->calculatePrice($start, $limitBefore);

					$pricedCourses[$identifier] = $detail->course->decimal_price;
				}
				else
					$detail->course->decimal_price = $pricedCourses[$identifier];
			}
			else
			{
				// Sum up the ticket
				$detail->ticket->calculatePrice($detail->session->start, $limitBefore);
			}
		});

		$booking->accommodations->each(function($accommodation) use ($booking, &$pricedPackagefacades)
		{
			if(empty($accommodation->pivot->packagefacade_id))
			{
				$limitBefore = in_array($booking->status, ['reserved', 'expired', 'confirmed']) ? $accommodation->pivot->created_at : false;

				$accommodation->calculatePrice($accommodation->pivot->start, $accommodation->pivot->end, $limitBefore);
			}

			$accommodation->customer = Customer::find($accommodation->pivot->customer_id);
		});

		return $booking;
	}

	public function getAll($from = 0, $take = 20)
	{
		return Auth::user()->bookings()
			->with(
				'lead_customer',
					'lead_customer.country',
				'payments',
					'payments.paymentgateway',
				'refunds',
					'refunds.paymentgateway'
			)
			->orderBy('id', 'DESC')
			->skip($from)
			->take($take)
			->get();
	}

	public function getRecent()
	{
		return Auth::user()->bookings()
			->with(
				'lead_customer',
					'lead_customer.country',
				'payments',
					'payments.paymentgateway',
				'refunds',
					'refunds.paymentgateway'
			)
			->orderBy('id', 'DESC')
			->take(5)
			->get();
	}

	public function getToday()
	{
		$data = array(
			'date' => Helper::localTime()->format('Y-m-d'),
		);

		Request::replace($data);

		return $this->getFilter();
	}

	public function getTomorrow()
	{
		$data = array(
			'date' => Helper::localTime()->add(new DateInterval('P1D'))->format('Y-m-d'),
		);

		Request::replace($data);

		return $this->getFilter();
	}

	public function getFilter($from = 0, $take = 20)
	{
		/**
		 * Allowed input parameter
		 * reference  {string}
		 * date       {date, string}
		 * lastname   {string}
		 */

		$reference = Input::get('reference', null);
		$date      = Input::get('date', null);
		$lastname  = Input::get('lastname', null);

		if(empty($reference) && empty($date) && empty($lastname))
			return $this->getAll();

		if(!empty($date))
			$date = new DateTime($date, new DateTimeZone( Auth::user()->timezone ));

		$bookings = Auth::user()->bookings()->with(
				'lead_customer',
					'lead_customer.country',
				'payments',
					'payments.paymentgateway',
				'refunds',
					'refunds.paymentgateway'
			)
			->where(function($query) use ($reference)
			{
				if(!empty($reference))
					$query->where('reference', 'LIKE', '%'.$reference.'%');
			})
			->where(function($query) use ($date)
			{
				if(!empty($date))
					$query->whereHas('sessions', function($query) use ($date)
					{
						$start = clone $date;
						$start->setTime(0, 0, 0);

						$end = clone $start;
						$end->add(new DateInterval('P1D'));

						$query->whereBetween('start', array($start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')));
					})
					->orWhereHas('accommodations', function($query) use ($date)
					{
						$query->where('accommodation_booking.start', $date->format('Y-m-d'));
					});
			})
			->where(function($query) use ($lastname)
			{
				if(!empty($lastname))
					$query->whereHas('lead_customer', function($query) use ($lastname)
					{
						$query->where('lastname', 'LIKE', '%'.$lastname.'%');
					});
			})
			->orderBy('id', 'DESC')
			->skip($from)
			->take($take)
			->get();

		return $bookings;
	}

	public function getFilterConfirmed()
	{
		/**
		 * Allowed input parameter
		 * after     {date string}
		 * before    {date string}
		 */

		$after  = Input::get('after', false);
		$before = Input::get('before', false);

		if(empty($after) || empty($before))
			return Response::json(['errors' => ['Both the "after" and the "before" parameters are required.']], 400); // 400 Bad Request

		$afterUTC  = new DateTime( $after,  new DateTimeZone( Auth::user()->timezone ) ); $afterUTC->setTimezone(  new DateTimeZone('Europe/London') );
		$beforeUTC = new DateTime( $before, new DateTimeZone( Auth::user()->timezone ) ); $beforeUTC->setTimezone( new DateTimeZone('Europe/London') );
		$beforeUTC->add(new DateInterval('P1D'));

		$bookings = Auth::user()->bookings()
			->with(
				'agent',
				'lead_customer',
				'payments',
					'payments.paymentgateway',
				'refunds',
					'refunds.paymentgateway'
			)
			->whereIn('status', ['confirmed'])
			->whereBetween('created_at', [$afterUTC, $beforeUTC])
			->orderBy('id')
			->get();

		$TOTALS = [
			'revenue' => 0,
		];
		foreach($bookings as $booking)
		{
			if(empty($booking->source))
			{
				// By agent
				$TOTALS['revenue'] += $booking->decimal_price - $booking->real_decimal_price * ($booking->agent->commission / 100);
			}
			else
			{
				// Direct
				$TOTALS['revenue'] += $booking->decimal_price;
			}
		}

		return ['bookings' => $bookings, 'totals' => $TOTALS];
	}

	public function getFilterConfirmedOnlyDirect()
	{
		/**
		 * Allowed input parameter
		 * after     {date string}
		 * before    {date string}
		 */

		$after  = Input::get('after', false);
		$before = Input::get('before', false);

		if(empty($after) || empty($before))
			return Response::json(['errors' => ['Both the "after" and the "before" parameters are required.']], 400); // 400 Bad Request

		$afterUTC  = new DateTime( $after,  new DateTimeZone( Auth::user()->timezone ) ); $afterUTC->setTimezone(  new DateTimeZone('Europe/London') );
		$beforeUTC = new DateTime( $before, new DateTimeZone( Auth::user()->timezone ) ); $beforeUTC->setTimezone( new DateTimeZone('Europe/London') );
		$beforeUTC->add(new DateInterval('P1D'));

		$bookings = Auth::user()->bookings()
			->with(
				'lead_customer',
				'payments',
					'payments.paymentgateway',
				'refunds',
					'refunds.paymentgateway'
			)
			->whereIn('status', ['confirmed'])
			->whereNull('agent_id')
			->whereBetween('created_at', [$afterUTC, $beforeUTC])
			->orderBy('id')
			->get();

		$TOTALS = [
			'revenue' => 0,
		];
		foreach($bookings as $booking)
		{
			$TOTALS['revenue'] += $booking->decimal_price;
		}

		return ['bookings' => $bookings, 'totals' => $TOTALS];
	}

	public function getFilterConfirmedByAgent()
	{
		/**
		 * Allowed input parameter
		 * after     {date string}
		 * before    {date string}
		 * agent_ids {array of integer}
		 */

		$after     = Input::get('after', false);
		$before    = Input::get('before', false);
		$agent_ids = Input::get('agent_ids', []);

		if(empty($after) || empty($before))
			return Response::json(['errors' => ['Both the "after" and the "before" parameters are required.']], 400); // 400 Bad Request

		if(!empty($agent_ids) && !is_array($agent_ids))
			return Response::json(['errors' => ['The parameter "agent_ids" must be an array!']], 400); // 400 Bad Request

		$afterUTC  = new DateTime( $after,  new DateTimeZone( Auth::user()->timezone ) ); $afterUTC->setTimezone(  new DateTimeZone('Europe/London') );
		$beforeUTC = new DateTime( $before, new DateTimeZone( Auth::user()->timezone ) ); $beforeUTC->setTimezone( new DateTimeZone('Europe/London') );
		$beforeUTC->add(new DateInterval('P1D'));

		$bookings = Auth::user()->bookings()
			->with(
				'agent',
				'lead_customer',
				'payments',
					'payments.paymentgateway',
				'refunds',
					'refunds.paymentgateway'
			)
			->whereIn('status', ['confirmed'])
			->whereBetween('created_at', [$afterUTC, $beforeUTC])
			->whereNotNull('agent_id')
			->where(function($query) use ($agent_ids)
			{
				if(!empty($agent_ids))
					$query->whereIn('agent_id', $agent_ids);
			})
			->orderBy('id')
			->get();

		$TOTALS = [
			'revenue' => 0,
			'commission' => 0,
			'invoicable' => 0
		];
		foreach($bookings as $booking)
		{
			$TOTALS['commission'] += $booking->real_decimal_price * ($booking->agent->commission / 100);

			$TOTALS['revenue'] += $booking->decimal_price - $booking->real_decimal_price * ($booking->agent->commission / 100);

			if($booking->agent->terms === 'fullamount')
			{
				$TOTALS['invoicable'] += $booking->decimal_price - $booking->real_decimal_price * ($booking->agent->commission / 100);
			}
		}

		return ['bookings' => $bookings, 'totals' => $TOTALS];
	}

	public function postInit()
	{
		$data = Input::only('agent_id', 'source', 'agent_reference');

		if( $data['agent_id'] )
		{
			// Check if the agent belongs to the signed-in company
			try
			{
				if( empty($data['agent_id']) ) throw new ModelNotFoundException();
				Auth::user()->agents()->findOrFail( $data['agent_id'] );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The agent could not be found.')), 404 ); // 404 Not Found
			}

			// If a valid agent_id is supplied, discard source
			$data['source'] = null;
		}

		$data['price'] = 0;

		// Reserve booking for 15 min by default
		$data['reserved'] = Helper::localTime()->add( new DateInterval('PT15M') )->format('Y-m-d H:i:s');
		$data['status'] = 'initialised';

		$booking = new Booking($data);

		$booking->reference = Helper::booking_reference_number(); // The helper function already validates that the reference is unique

		if( !$booking->validate() )
			return Response::json( array('errors' => $booking->errors()->all()), 406 ); // 406 Not Acceptable

		$booking = Auth::user()->bookings()->save($booking);

		return Response::json( array('status' => 'OK. Booking created', 'id' => $booking->id, 'reference' => $booking->reference), 201 ); // 201 Created
	}

	public function postAddDetail()
	{
		/**
		 * Valid input parameters
		 * booking_id
		 * customer_id
		 * ticket_id (or nothing)
		 * session_id (or training_session_id)
		 * boatroom_id (only sometimes required)
		 * training_session_id
		 *
		 * package_id (optional)
		 * packagefacade_id (optional)
		 * course_id (optional)
		 */

		// Check if all IDs exist and belong to the signed-in company
		try
		{
			if( !Input::has('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			if( !Input::has('customer_id') ) throw new ModelNotFoundException();
			$customer = Auth::user()->customers()->findOrFail( Input::get('customer_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found
		}

		if(Input::has('ticket_id'))
		{
			try
			{
				$ticket = Auth::user()->tickets()->with('boats', 'boatrooms')->findOrFail( Input::get('ticket_id') );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$ticket = false;

		if(Input::has('session_id'))
		{
			try
			{
				$departure = Auth::user()->departures()->where('sessions.id', Input::get('session_id'))->with('boat', 'boat.boatrooms')->firstOrFail(array('sessions.*'));
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$departure = false;

		if(Input::has('training_session_id'))
		{
			try
			{
				$training_session = Auth::user()->training_sessions()->where('training_sessions.id', Input::get('training_session_id'))->firstOrFail(array('training_sessions.*'));
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$training_session = false;

		if( Input::has('packagefacade_id') )
		{
			try
			{
				$packagefacade = $booking->packagefacades()->where('packagefacades.id', Input::get('packagefacade_id'))->firstOrFail(array('packagefacades.*'));
			}
			catch(ModelNotFoundException $e)
			{
				// When the packagefacade is not found via the booking, it may be the situation where a packaged detail has been assigned but then un-assigned, thus leaving the packagefacade in the DB, but making it unaccessible from the booking.
				// We thus have to look for it via the company and supplied package_id
				try
				{
					$packagefacade = Auth::user()
						->packages()->findOrFail( Input::get('package_id') )
						->packagefacades()->findOrFail( Input::get('packagefacade_id') );
				}
				catch(ModelNotFoundException $e)
				{
					return Response::json( array('errors' => array('The packagefacade could not be found.')), 404 ); // 404 Not Found
				}
			}

			$package = $packagefacade->package()->with('tickets', 'courses')->first();
		}
		elseif( Input::has('package_id') )
		{
			try
			{
				$package = Auth::user()->packages()->with('tickets', 'courses')->findOrFail( Input::get('package_id') );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
			}

			$packagefacade = false;
		}
		else
		{
			$package = false;
			$packagefacade = false;
		}

		if( Input::has('course_id') )
		{
			try
			{
				$course = Auth::user()->courses()->findOrFail( Input::get('course_id') );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The course could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$course = false;

		// Validate that the booking is not cancelled or on hold
		if(!$booking->isEditable())
		{
			return Response::json( array('errors' => array('Cannot add details, because the booking is '.$booking->status.'.')), 403 ); // 403 Forbidden
		}

		// TODO Validate all input and their relations instead
		// Validate that either a session or a training_session has been submitted
		if(!$departure && !$training_session)
			return Response::json(['errors' => ['Either the session_id or training_session_id is required!']], 406); // 406 Not Acceptable

		// Validate that the session start date has not already passed
		if($departure && Helper::isPast($departure->start))
		{
			return Response::json( array('errors' => array('Cannot add details, because the trip has already departed!')), 403 ); // 403 Forbidden
		}

		// Validate that the training_session start date has not already passed
		if($training_session && Helper::isPast($training_session->start))
		{
			return Response::json( array('errors' => array('Cannot add details, because the class has already started!')), 403 ); // 403 Forbidden
		}

		// Validate that the customer is not already booked for this session or training_session on another booking
		$check = Auth::user()->bookings()
			->whereNotIn('id', array($booking->id))
			->whereIn('status', Booking::$counted)
			->whereHas('bookingdetails', function($query) use ($customer, $departure, $training_session)
			{
				$query
					->where('customer_id', $customer->id)
					->where(function($query) use ($departure, $training_session)
					{
						if($departure)
							$query->where('session_id', $departure->id);
						elseif($training_session)
							$query->where('training_session_id', $training_session->id);
					});
			})->exists();
		if( $check )
		{
			$model = $departure ? 'trip' : 'class';
			return Response::json( array('errors' => array('The customer is already booked on this '.$model.' in another booking!')), 403 ); // 403 Forbidden
		}

		// Validate that the ticket can be booked for this session
		if($departure)
		{
			$exists = $departure->trip->tickets()->where('id', $ticket->id)->exists();
			if(!$exists)
				return Response::json( array('errors' => array('This ticket can not be booked for this trip.')), 403 ); // 403 Forbidden

			// Validate that the ticket can be booked in the course
			if($course)
			{
				$exists = $course->tickets()->where('id', $ticket->id)->exists();
				if(!$exists)
					return Response::json(['errors' => ['This ticket can not be booked as part of this course.']], 403); // 403 Forbidden
			}

			// Validate that the ticket can be booked directly in the package
			if($package && !$course)
			{
				$exists = $package->tickets()->where('id', $ticket->id)->exists();
				if(!$exists)
					return Response::json(['errors' => ['This ticket can not be booked as part of this package.']], 403); // 403 Forbidden
			}
		}

		// Validate that the course can be booked for this training_session
		if($training_session)
		{
			if($course->training_id !== $training_session->training_id)
				return Response::json( ['errors' => ['This course can not be booked for this class.']], 403 ); // 403 Forbidden
		}

		// Validate that the course can be booked in the package
		if($course && $package)
		{
			$exists = $package->courses()->where('id', $course->id)->exists();
			if(!$exists)
				return Response::json(['error' => ['This course can not be booked as part of this package.']], 403); // 403 Forbidden
		}

		// Check if the session's boat is allowed for the ticket
		if($departure && $ticket->boats()->exists())
		{
			$boatIDs = $ticket->boats()->lists('id');
			if( !in_array($departure->boat_id, $boatIDs) )
				return Response::json( array('errors' => array('This ticket is not eligable for this trip\'s boat.')), 403 ); // 403 Forbidden
		}

		if($departure)
		{
			// Determine if we need a boatroom_id (only when the trip is overnight)
			$trip             = $departure->trip;
			$start            = new DateTime($departure->start);
			$end              = clone $start;
			$duration_hours   = floor($trip->duration);
			$duration_minutes = round( ($trip->duration - $duration_hours) * 60 );
			$end->add( new DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );
			if($start->format('Y-m-d') !== $end->format('Y-m-d'))
			{
				// The trip is overnight and we do need a boatroom_id

				$boatroom_id = false;
				$boatBoatrooms   = $departure->boat->boatrooms()->lists('id');
				$ticketBoatrooms = $ticket->boatrooms()->lists('id');

				// Check if the session's boat's boatrooms are allowed for the ticket
				if( count($ticketBoatrooms) > 0 )
				{
					$intersect = array_intersect($boatBoatrooms, $ticketBoatrooms);
					if( count($intersect) === 0 )
						return Response::json( array('errors' => array('This ticket is not eligable for this trip\'s boat\'s cabin(s).')), 403 ); // 403 Forbidden

					if( count($intersect) === 1 )
						$boatroom_id = $intersect[0];
				}

				// Just in case, check if the boat has boatrooms assigned
				if( count($boatBoatrooms) === 0 )
					return Response::json( array('errors' => array('Could not assign the customer, the boat has no cabins.')), 412 ); // 412 Precondition Failed

				// Check if the boat only has one boatroom assigned
				if( count($boatBoatrooms) === 1 )
					$boatroom_id = $boatBoatrooms[0];

				// Check if the boatroom is still not determined
				if($boatroom_id === false)
				{
					// Check if a boatroom_id got submitted
					if( !Input::has('boatroom_id') )
						return Response::json( array('errors' => array('Please select in which cabin the customer will sleep.')), 406 ); // 406 Not Acceptable

					// Check if the submitted boatroom_id is allowed
					$boatroom_id = Input::get('boatroom_id');
					if( !in_array($boatroom_id, $boatBoatrooms) || ( count($ticketBoatrooms) > 0 && !in_array($boatroom_id, $ticketBoatrooms) ) )
						return Response::json( array('errors' => array('The selected cabin cannot be booked for this session.')), 403 ); // 403 Forbidden
				}
				else
				{
					// The above checks already determined that there is only one possible boatroom to take
					// If a boatroom_id got submitted anyway, check if it is the same that we determined
					if( Input::has('boatroom_id') && Input::get('boatroom_id') != $boatroom_id )
						return Response::json( array('errors' => array('The selected cabin cannot be booked for this session.')), 403 ); // 403 Forbidden
				}
			}
			else
			{
				// The trip ends on the same day it starts and thus customers are not assigned to boatrooms
				$boatroom_id = null;
			}

			// Validate remaining capacity on session
			// The $capacity variable is needed in the next check, so don't move this check!
			$capacity = $departure->getCapacityAttribute();
			if( $capacity[0] >= $capacity[1] )
			{
				// Session/Boat already full/overbooked
				return Response::json( array('errors' => array('The session is already fully booked!')), 403 ); // 403 Forbidden
			}

			// If a boatroom is needed, validate remaining capacity of boatroom
			if($boatroom_id !== null && $capacity[2][$boatroom_id][0] >= $capacity[2][$boatroom_id][1] )
			{
				// The selected/required boatroom is already full/overbooked
				return Response::json( array('errors' => array('The selected cabin is already fully booked!')), 403 ); // 403 Forbidden
			}
		}

		// Validate remaining course capacity on session
		if($departure && $course && !empty($course->capacity))
		{
			// Course's capacity is *not* infinite and must be checked
			$usedUp = $departure->bookingdetails()->where('course_id', $course->id)->count();
			if( $usedUp >= $course->capacity )
			{
				// TODO Check for extra one-time courses for this session and their capacity

				return Response::json( array('errors' => array('The course\'s capacity on this trip is already reached!')), 403 ); // 403 Forbidden
			}
		}

		// Validate that the ticket still fits into the package
		if($ticket && $packagefacade && !$course)
		{
			// Check if the package still has space for the wanted ticket
			$bookedTicketsQuantity = $packagefacade->bookingdetails()->where('ticket_id', $ticket->id)->count();

			if($bookedTicketsQuantity >= $package->tickets()->where('id', $ticket->id)->first()->pivot->quantity)
				return Response::json(['errors' => ['The ticket cannot be assigned because the package\'s limit for the ticket is reached.']], 403 ); // 403 Forbidden
		}

		// Validate that the course still fits into the package (failsafe for when client validation fails)
		if($course && $packagefacade)
		{
			// Check if the package still has space for the wanted course
			$bookedCustomers = $packagefacade->bookingdetails()->where('course_id', $course->id)->lists('customer_id');
			$bookedCoursesQuantity = count($bookedCustomers);

			if($bookedCoursesQuantity >= $package->courses()->where('id', $course->id)->first()->pivot->quantity)
			{
				// Before we throw the error, we need to check if the new detail belongs to one of the existing courses
				if(!in_array($customer->id, $bookedCustomers))
					return Response::json(['errors' => ['The course cannot be assigned because the package\'s limit for the course is reached.']], 403 ); // 403 Forbidden
			}
		}

		// Validate that the ticket still fits into the course
		if($ticket && $course)
		{
			// Check if the course still has space for the wanted ticket
			$bookedTicketsQuantity = $course->bookingdetails()
				->where('ticket_id', $ticket->id)
				->where('customer_id', $customer->id)
				->where('booking_id', $booking->id)
				->count();

			if($bookedTicketsQuantity >= $course->tickets()->where('id', $ticket->id)->first()->pivot->quantity)
				return Response::json(['errors' => ['The ticket cannot be assigned because the course\'s limit for the ticket is reached.']], 403 ); // 403 Forbidden
		}

		// Validate that the class still fits into the course
		if($training_session && $course)
		{
			// Check if the course still has space for the wanted class
			$bookedTrainingsQuantity = $course->bookingdetails()
				->where('customer_id', $customer->id)
				->where('booking_id', $booking->id)
				->whereNotNull('training_session_id')
				->count();

			if($bookedTrainingsQuantity >= $course->training_quantity)
				return Response::json(['errors' => ['The class cannot be assigned because the course\'s limit for the class is reached.']], 403 ); // 403 Forbidden
		}

		// Check if we have to create a new packagefacade
		if($package && !$packagefacade)
		{
			$packagefacade = new Packagefacade( array('package_id' => $package->id) );
			$packagefacade->save();
		}

		/*******************
		 * CHECKS COMPLETE *
		 *******************/

		// If all checks completed successfully, write into database
		$bookingdetail = new Bookingdetail( array(
			'customer_id'         => $customer->id,
			'ticket_id'           => $ticket           ? $ticket->id           : null,
			'session_id'          => $departure        ? $departure->id        : null,
			'boatroom_id'         => $departure        ? $boatroom_id          : null,
			'packagefacade_id'    => $package          ? $packagefacade->id    : null,
			'course_id'           => $course           ? $course->id           : null,
			'training_session_id' => $training_session ? $training_session->id : null
		) );

		if(!$bookingdetail->validate())
			return Response::json(['errors' => $bookingdetail->errors()->all()], 406); // 406 Not Acceptable

		$bookingdetail = $booking->bookingdetails()->save($bookingdetail);

		// If this is the booking's first added details and there is no lead customer yet, set lead_customer_id
		if(empty($booking->lead_customer_id) && $booking->bookingdetails()->count() === 1)
			$booking->update( array('lead_customer_id' => $customer->id) );

		if($departure)
		{
			// Add compulsory addons
			$addons = Auth::user()->addons()->where('compulsory', true)->get();
			if($addons->count() > 0) {
				$addons->each(function($addon) use ($bookingdetail)
				{
					$bookingdetail->addons()->attach( $addon->id, array('quantity' => 1) );
				});
			}
		}
		else
			$addons = false;

		// Update booking price
		if($package)
		{
			// Find the first departure datetime that is booked in this package
			$bookingdetails = $packagefacade->bookingdetails()->with('departure', 'training_session')->get();
			$firstDetail = $bookingdetails->sortBy(function($detail)
			{
				if($detail->departure)
					return $detail->departure->start;
				else
					return $detail->training_session->start;
			})->first();

			if($firstDetail->departure)
				$start = $firstDetail->departure->start;
			else
				$start = $firstDetail->training_session->start;

			$firstAccommodation = $booking->accommodations()->wherePivot('packagefacade_id', $packagefacade->id)->get()
			->sortBy(function($accommodation)
			{
				return $accommodation->pivot->start;
			})->first();

			if(!empty($firstAccommodation))
			{
				$detailStart = new DateTime($start);
				$accommStart = new DateTime($firstAccommodation->pivot->start);

				$start = ($detailStart < $accommStart) ? $detailStart : $accommStart;

				$start = $start->format('Y-m-d H:i:s');
			}

			// Calculate the package price at this first datetime and sum it up
			$package->calculatePrice($start);
		}
		elseif($course)
		{
			// Find the first departure or class datetime that is booked in this course
			$bookingdetails = $course->bookingdetails()
				->where('booking_id', $booking->id)
				->where('customer_id', $customer->id)
				->with('departure', 'training_session')
				->get();

			$firstDetail = $bookingdetails->sortBy(function($detail)
			{
				if($detail->departure)
					return $detail->departure->start;
				else
					return $detail->training_session->start;
			})->first();

			if($firstDetail->departure)
				$start = $firstDetail->departure->start;
			else
				$start = $firstDetail->training_session->start;

			// Calculate the package price at this first departure datetime and sum it up
			$course->calculatePrice($start);
		}
		else
		{
			if($departure)
				$ticket->calculatePrice($departure->start);
		}

		$booking->updatePrice();

		return array(
			'status'                => 'OK. Booking details added.',
			'id'                    => $bookingdetail->id,
			'addons'                => $addons ? $addons->lists('id') : false,
			'decimal_price'         => $booking->decimal_price,

			'boatroom_id'           => $departure ? $boatroom_id : false,

			'package_decimal_price' => $package ? $package->decimal_price : false,
			'course_decimal_price'  => !$package && $course ? $course->decimal_price : false,
			'ticket_decimal_price'  => !$package && !$course && $ticket ? $ticket->decimal_price : false,

			'packagefacade_id'      => $package ? $packagefacade->id : false
		); // 200 OK
	}

	public function postRemoveDetail()
	{
		/**
		 * Valid input parameters
		 * booking_id
		 * bookingdetail_id
		 */

		// Check if booking belongs to logged-in company
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		// Check if bookingdetail belongs to booking
		try
		{
			if( !Input::get('bookingdetail_id') ) throw new ModelNotFoundException();
			$bookingdetail = $booking->bookingdetails()->with('departure', 'training_session')->findOrFail( Input::get('bookingdetail_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The bookingdetail has not been found.')), 404 ); // 404 Not Found
		}

		// Validate that the booking is not cancelled or on hold
		if($booking->status === "cancelled" || $booking->status === "on hold")
		{
			return Response::json( array('errors' => array('Cannot remove details, because the booking is '.$booking->status.'.')), 403 ); // 403 Forbidden
		}

		// Validate that the session start date has not already passed
		$start = !empty($bookingdetail->departure) ? $bookingdetail->departure->start : $bookingdetail->training_session->start;

			if(Helper::isPast($start))
			{
				return Response::json( array('errors' => array('Cannot remove details, because the trip/class has already departed/started!')), 403 ); // 403 Forbidden
			}

		// Execute delete
		$bookingdetail->delete();

		// Update booking price
		$booking->updatePrice();

		return array(
			'status'        => 'OK. Bookingdetail removed.',
			'decimal_price' => $booking->decimal_price
		); // 200 OK
	}

	public function postSetLead()
	{
		/**
		 * Valid input parameters
		 * booking_id
		 * customer_id
		 */

		// Check if booking belongs to logged-in company
		try
		{
			if( !Input::has('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		// Validate that the booking is not cancelled or on hold
		if($booking->status === "cancelled" || $booking->status === "on hold")
		{
			return Response::json( array('errors' => array('Cannot change lead customer, because the booking is '.$booking->status.'.')), 403 ); // 403 Forbidden
		}

		$customer_id = Input::get('customer_id', null);
		if( empty($customer_id) )
			$customer_id = null;

		if( $customer_id !== null)
			try
			{
				if( !Input::has('customer_id') ) throw new ModelNotFoundException();
				$customer = Auth::user()->customers()->findOrFail( Input::get('customer_id') );
				$customer_id = $customer->id;
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found
			}

		if( !$booking->update( array('lead_customer_id' => $customer_id) ) )
			return Response::json( array('errors' => $booking->errors()->all()), 406 ); // 406 Not Acceptable

		return array('status' => 'OK. Lead customer set'); // 200 OK
	}

	public function postAddAddon()
	{
		/**
		 * Required input parameters
		 * booking_id
		 * bookingdetail_id
		 * addon_id
		 *
		 * Optional input parameters
		 * quantity
		 * packagefacade_id
		 */

		// Check if the addon belongs to the company
		try
		{
			if( !Input::get('addon_id') ) throw new ModelNotFoundException();
			$addon = Auth::user()->addons()->findOrFail( Input::get('addon_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The addon could not be found.')), 404 ); // 404 Not Found
		}

		// Break, if the addon is compulsory, as those cannot be added manually
		if($addon->compulsory === 1 || $addon->compulsory === "1")
			return Response::json( array('errors' => array('The addon is compulsory and cannot be added manually.')), 403 ); // 403 Forbidden

		// Check if the booking belongs to the company
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		// Validate that the booking is not cancelled or on hold
		if($booking->status === "cancelled" || $booking->status === "on hold")
		{
			return Response::json( array('errors' => array('Cannot add addon, because the booking is '.$booking->status.'.')), 403 ); // 403 Forbidden
		}

		// Check if the bookingdetail belongs to the booking
		try
		{
			if( !Input::get('bookingdetail_id') ) throw new ModelNotFoundException();
			$bookingdetail = $booking->bookingdetails()->with('departure')->findOrFail( Input::get('bookingdetail_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The bookingdetail could not be found.')), 404 ); // 404 Not Found
		}

		// Validate that the bookingdetail is for a trip and not a class
		if(empty($bookingdetail->departure))
			return Response::json(['errors' => ['Addons can only be added to trips, not classes.']], 403); // 403 Forbidden

		// Check if trip departed more than 5 days ago
		if($this->moreThan5DaysAgo($bookingdetail->departure->start))
		{
			return Response::json( array('errors' => array('The addon cannot be added because the trip departed more than 5 days ago.')), 403 ); // 403 Forbidden
		}

		if( Input::has('packagefacade_id') )
		{
			try
			{
				$packagefacade = $booking->packagefacades()->where('packagefacades.id', Input::get('packagefacade_id'))->firstOrFail(array('packagefacades.*'));
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The packagefacade could not be found.')), 404 ); // 404 Not Found
			}

			$package = $packagefacade->package()->with('addons')->first();
		}
		else
		{
			$package = false;
			$packagefacade = false;
		}

		$quantity = Input::get('quantity', 1);

		if($package)
		{
			// Validate that the addon can be booked as part of the package
			$exists = $package->addons()->where('id', $addon->id)->exists();
			if(!$exists)
				return Response::json(['errors' => ['This addon can not be booked as part of this package.']], 403); // 403 Forbidden

			// Validate that the bookingdetail is in the same package as the addon
			if($bookingdetail->packagefacade_id !== $packagefacade->id)
				return Response::json(['errors' => ['This addon can not be booked for this trip, as the trip is not in the same package.']], 403); // 403 Forbidden

			// Check if the package still has space for the wanted addon
			$bookedAddonsQuantity = $addon->bookingdetails()
				->wherePivot('packagefacade_id', $packagefacade->id)
				->whereHas('booking', function($query) use ($booking)
				{
					$query->where('id', $booking->id);
				})
				->sum('addon_bookingdetail.quantity');

			if(($bookedAddonsQuantity + $quantity) > $package->addons()->where('id', $addon->id)->first()->pivot->quantity)
				return Response::json(['errors' => ['The addon cannot be assigned because the package\'s limit for the addon would be exceeded.']], 403 ); // Forbidden
		}

		$validator = Validator::make(array('quantity' => $quantity), array('quantity' => 'integer|min:1'));
		if( $validator->fails() )
			return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request

		$pivotData = [];

		if($package)
			$pivotData['packagefacade_id'] = $packagefacade->id;

		// Check if the addon already exists on the pivot table
		$existingAddon = $bookingdetail->addons()->wherePivot('packagefacade_id', $packagefacade ? $packagefacade->id : null)->where('id', $addon->id)->first();
		if($existingAddon)
		{
			// The addon is already assigned to the bookingdetail
			$pivotData['quantity'] = $existingAddon->pivot->quantity + $quantity;
			$bookingdetail->addons()
				->wherePivot('packagefacade_id', $packagefacade ? $packagefacade->id : null)
				->updateExistingPivot($addon->id, $pivotData);
		}
		else
		{
			$pivotData['quantity'] = $quantity;
			$bookingdetail->addons()->attach( $addon->id, $pivotData );
		}

		// Update booking price
		if(!$package)
			$booking->updatePrice(); // Only need to update if not a package, because otherwise the price doesn't change

		return array('status' => 'OK. Addon(s) added.', 'decimal_price' => $booking->decimal_price);
	}

	public function postRemoveAddon()
	{
		/**
		 * Required input parameters
		 * booking_id
		 * bookingdetail_id
		 * addon_id
		 *
		 * packagefacade_id (optional)
		 */

		// Check if the booking belongs to the company
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		// Validate that the booking is not cancelled or on hold
		if($booking->status === "cancelled" || $booking->status === "on hold")
		{
			return Response::json( array('errors' => array('Cannot remove addon, because the booking is '.$booking->status.'.')), 403 ); // 403 Forbidden
		}

		// Check if the bookingdetail belongs to the booking
		try
		{
			if( !Input::get('bookingdetail_id') ) throw new ModelNotFoundException();
			$bookingdetail = $booking->bookingdetails()->with('departure')->findOrFail( Input::get('bookingdetail_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The bookingdetail could not be found.')), 404 ); // 404 Not Found
		}

		// Validate that the bookingdetail is for a trip and not a class
		if(empty($bookingdetail->departure))
			return Response::json(['errors' => ['Addons can only be added to trips, not classes.']], 403); // 403 Forbidden

		// Check if trip departed more than 5 days ago
		if($this->moreThan5DaysAgo($bookingdetail->departure->start))
		{
			return Response::json( array('errors' => array('The addon cannot be removed because the trip departed more than 5 days ago.')), 403 ); // 403 Forbidden
		}

		$packagefacade_id = Input::get('packagefacade_id', null);

		// Check if the addon belongs to the bookingdetail
		try
		{
			if( !Input::has('addon_id') ) throw new ModelNotFoundException();
			$addon = $bookingdetail->addons()->wherePivot('packagefacade_id', $packagefacade_id)->where('id', Input::get('addon_id'))->first();
			if(!$addon) throw new ModelNotFoundException();
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The addon could not be found.')), 404 ); // 404 Not Found
		}

		if($addon->compulsory === 1 || $addon->compulsory === "1")
			return Response::json( array('errors' => array('The addon is compulsory and cannot be removed.')), 403 ); // 403 Forbidden


		$pivotData = ['packagefacade_id' => $packagefacade_id];

		Clockwork::info($addon);

		// Check the quantity the addon
		if($addon->pivot->quantity > 1)
		{
			Clockwork::info('More than 1');
			// Just substract one from the quantity
			$pivotData['quantity'] = --$addon->pivot->quantity;
			$bookingdetail->addons()
				->wherePivot('packagefacade_id', $packagefacade_id)
				->updateExistingPivot($addon->id, $pivotData);
		}
		else
		{
			Clockwork::info('Only 1');
			// Don't need to check if addon belongs to company because detaching wouldn't throw an error if it's not there in the first place.
			$bookingdetail->addons()->wherePivot('packagefacade_id', $packagefacade_id)->detach( $addon->id );
		}

		// Update booking price
		if(empty($addon->pivot->packagefacade_id))
			$booking->updatePrice(); // Only need to update if not a package, because otherwise the price doesn't change

		return array('status' => 'OK. One addon removed.', 'decimal_price' => $booking->decimal_price);
	}

	public function postAddAccommodation()
	{
		/**
		 * Required input parameters
		 *
		 * booking_id
		 * accommodation_id
		 * customer_id
		 * start
		 * end
		 *
		 * package_id (optional)
		 * packagefacade_id (optional)
		 */

		// Check if the booking belongs to the company
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		// Validate that the booking is not cancelled or on hold
		if($booking->status === "cancelled" || $booking->status === "on hold")
		{
			return Response::json( array('errors' => array('Cannot add accommodation, because the booking is '.$booking->status.'.')), 403 ); // 403 Forbidden
		}

		// Check if the accommodation belongs to the company
		try
		{
			if( !Input::get('accommodation_id') ) throw new ModelNotFoundException();
			$accommodation = Auth::user()->accommodations()->findOrFail( Input::get('accommodation_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The accommodation could not be found.')), 404 ); // 404 Not Found
		}

		// Check if the customer belongs to the company
		try
		{
			if( !Input::get('customer_id') ) throw new ModelNotFoundException();
			$customer = Auth::user()->customers()->findOrFail( Input::get('customer_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found
		}

		$start = Input::get('start');
		$end   = Input::get('end');
		$validator = Validator::make(
			array(
				'start' => $start,
				'end'   => $end
			),
			array(
				'start' => 'required|date',
				'end'   => 'required|date'
			)
		);
		if( $validator->fails() )
		{
			return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request
		}

		$start = new DateTime($start, new DateTimeZone( Auth::user()->timezone ));
		$end   = new DateTime($end,   new DateTimeZone( Auth::user()->timezone ));

		// Clockwork::info($start->diff($end)->format('%R%a'));

		if($start->diff($end)->format('%R%a') < 1)
			return Response::json(['errors' => ['The end date must be after the start date.']], 400); // 400 Bad Request

		// Validate that the start and end dates are maximum 1 days ago
		$now = Helper::localTime();
		if($start->diff($now)->format('%R%a') > 1 || $end->diff($now)->format('%R%a') > 1)
			return Response::json(['errors' => ['The start date can only be a maximum of 1 day ago.']], 400); // 400 Bad Request

		if( Input::has('packagefacade_id') )
		{
			try
			{
				$packagefacade = $booking->packagefacades()->where('packagefacades.id', Input::get('packagefacade_id'))->firstOrFail(array('packagefacades.*'));
			}
			catch(ModelNotFoundException $e)
			{
				// When the packagefacade is not found via the booking, it may be the situation where a packaged detail has been assigned but then un-assigned, thus leaving the packagefacade in the DB, but making it unaccessible from the booking.
				// We thus have to look for it via the company and supplied package_id
				try
				{
					$packagefacade = Auth::user()
						->packages()->findOrFail( Input::get('package_id') )
						->packagefacades()->findOrFail( Input::get('packagefacade_id') );
				}
				catch(ModelNotFoundException $e)
				{
					return Response::json( array('errors' => array('The packagefacade could not be found.')), 404 ); // 404 Not Found
				}
			}

			$package = $packagefacade->package()->with('addons')->first();
		}
		elseif( Input::has('package_id') )
		{
			try
			{
				$package = Auth::user()->packages()->with('addons')->findOrFail( Input::get('package_id') );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
			}

			$packagefacade = new Packagefacade( array('package_id' => $package->id) );
			$packagefacade->save();
		}
		else
		{
			$package = false;
			$packagefacade = false;
		}

		// Validate that the accommodation can be booked as part of the package
		if($package)
		{
			$exists = $package->accommodations()->where('id', $accommodation->id)->exists();
			if(!$exists)
				return Response::json(['errors' => ['This accommodation can not be booked as part of this package.']], 403); // 403 Forbidden

			// Check if the package still has space for the number of nights selected
			$numberOfNights = $start->diff($end)->format('%a');

			// TODO Sum up all nights that have been booked in this package already
			$alreadyBookedNights = 0;
			$alreadyBookedNights = $booking->accommodations()
				->wherePivot('packagefacade_id', $packagefacade->id)
				->where('id', $accommodation->id)
				->sum(DB::raw('DATEDIFF(end, start)'));

			if(($alreadyBookedNights + $numberOfNights) > $package->accommodations()->where('id', $accommodation->id)->first()->pivot->quantity)
				return Response::json(['errors' => ['The accommodation cannot be booked because the package\'s limit for the accommodation would be exceeded.']], 403 ); // Forbidden
		}

		// Check if accommodation is available for each of the selected days
		$current_date = clone $start;
		$end_date = $end;
		do
		{
			if( $accommodation->bookings()
			    ->wherePivot('start', '<=', $current_date)
			    ->wherePivot('end', '>', $current_date)
			    ->where(function($query)
			    {
			    	$query->whereIn('status', Booking::$counted);
			    })
			    ->count() >= $accommodation->capacity )
			    return Response::json( array('errors' => array('The accommodation is not available for '.$current_date->format('D, j M Y').'!')), 403 ); // 403 Forbidden

			$current_date->add( new DateInterval('P1D') );
		}
		while( $current_date < $end_date );

		$pivotData = array('customer_id' => $customer->id, 'start' => $start, 'end' => $end);
		if($packagefacade)
			$pivotData['packagefacade_id'] = $packagefacade->id;

		$booking->accommodations()->attach( $accommodation->id, $pivotData );

		// Update booking price
		if(!$package)
			$accommodation->calculatePrice($start, $end);

		$booking->updatePrice();

		return array(
			'status'                      => 'OK. Accommodation added.',

			'decimal_price'               => $booking->decimal_price,
			'accommodation_decimal_price' => !$package ? $accommodation->decimal_price : false,

			'packagefacade_id'            => $packagefacade ? $packagefacade->id : false
		);
	}

	public function postRemoveAccommodation()
	{
		/**
		 * Required input parameters
		 *
		 * booking_id
		 * accommodation_id
		 * customer_id
		 * start (date)
		 */

		if( !Input::has('booking_id') )
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found

		if( !Input::has('accommodation_id') )
			return Response::json( array('errors' => array('The accommodation could not be found.')), 404 ); // 404 Not Found

		if( !Input::has('customer_id') )
			return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found

		if( !Input::has('start') )
			return Response::json( array('errors' => array('Please provide the start date of the accommodation booking.')), 400 ); // 400 Bad Request

		try
		{
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		// Validate that the booking is not cancelled or on hold
		if($booking->status === "cancelled" || $booking->status === "on hold")
		{
			return Response::json( array('errors' => array('Cannot remove accommodation, because the booking is '.$booking->status.'.')), 403 ); // 403 Forbidden
		}

		// Don't need to check if accommodation belongs to company because detaching wouldn't throw an error if it's not there in the first place.
		$affectedRows = $booking->accommodations()
			->wherePivot('customer_id', Input::get('customer_id'))
			->wherePivot('start', Input::get('start'))
			->detach(Input::get('accommodation_id'));

		if($affectedRows === 0)
			return Response::json( array('errors' => array('The accommodation pivot model could not be found.')), 404 ); // 404 Not Found

		// Update booking price
		$booking->updatePrice();

		return array('status' => 'OK. Accommodation removed.', 'decimal_price' => $booking->decimal_price);
	}

	public function getPickUpLocations() {
		$query = Input::get('query');

		if( strlen($query) < 3 )
			return Response::json( array('errors' => 'Query string must be at least 3 characters long.'), 406 ); // 406 Not Acceptable

		return Auth::user()->bookings()
			->where('pick_up_location', 'LIKE', '%'.$query.'%')
			->whereNotNull('pick_up_location')
			->where('updated_at', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
			->groupBy('pick_up_location', 'pick_up_time')
			->orderBy('pick_up_time', 'ASC')
			->lists('pick_up_time', 'pick_up_location');
	}

	public function postEditInfo()
	{
		/**
		 * Valid input parameters
		 *
		 * booking_id
		 * pick_up_location
		 * pick_up_date
		 * pick_up_time
		 * discount
		 * comment
		 */

		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		// Validate that the booking is not cancelled or on hold
		if($booking->status === "cancelled" || $booking->status === "on hold")
		{
			return Response::json( array('errors' => array('Cannot adit info, because the booking is '.$booking->status.'.')), 403 ); // 403 Forbidden
		}

		$data = Input::only(
			'pick_up_location', // Just text
			'pick_up_date',     // Must be date
			'pick_up_time',     // Must be time
			'discount',         // Should be decimal
			'comment'           // Text
		);

		if( !( empty($data['pick_up_date']) || empty($data['pick_up_time']) ) )
		{
			$datetime = new DateTime($data['pick_up_date'].' '.$data['pick_up_time'], new DateTimeZone( Auth::user()->timezone ));
			$data['pick_up_date'] = $datetime->format('Y-m-d');
			$data['pick_up_time'] = $datetime->format('H:i:s');
		}

		if( empty($data['pick_up_location']) ) $data['pick_up_location'] = null;
		if( empty($data['pick_up_date']) ) $data['pick_up_date'] = null;
		if( empty($data['pick_up_time']) ) $data['pick_up_time'] = null;
		if( empty($data['discount']) && $data['discount'] !== 0 && $data['discount'] !== "0") $data['discount'] = null;

		$oldDiscount = $booking->discount;

		if( !$booking->update($data) )
		{
			return Response::json( array('errors' => $booking->errors()->all()), 406 ); // 406 Not Acceptable
		}

		if(!empty($data['discount']))
			$booking->updatePrice(true, $oldDiscount);

		return array('status' => 'OK. Booking information updated.', 'decimal_price' => $booking->decimal_price);
	}

	public function postReserve()
	{
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		if( in_array($booking->status, array('confirmed', 'on hold', 'cancelled')) )
			return Response::json( array('errors' => array('The booking cannot be reserved, as it is ' . $booking->status . '.')), 403 ); // 403 Forbidden

		$data = Input::only('reserved');
		$data['status'] = 'reserved';

		if( !$booking->update($data) )
		{
			return Response::json( array('errors' => $booking->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return array('status' => 'OK. Booking reserved');
	}

	public function postSave()
	{
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		if( in_array($booking->status, array('reserved', 'expired', 'confirmed', 'on hold', 'cancelled')) )
			return Response::json( array('errors' => array('The booking cannot be saved, as it is ' . $booking->status . '.')), 403 ); // 403 Forbidden

		if( !$booking->update( array('status' => 'saved') ) )
		{
			return Response::json( array('errors' => $booking->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return array('status' => 'OK. Booking saved');
	}

	public function postCancel()
	{
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		// Bookings that have not been reserved, confirmed, cancelled or are on hold can be safely deleted
		if($booking->status === null || in_array($booking->status, ['saved', 'initialised', 'expired']))
		{
			$booking->delete();
			return array('status' => 'OK. Booking cancelled.');
		}

		if($booking->status === 'cancelled')
			return Response::json( array('errors' => array('The booking is already cancelled.')), 403 ); // 403 Forbidden

		if($this->moreThan5DaysAgo($booking->last_return_date))
		{
			return Response::json( array('errors' => array('The booking can not be cancelled anymore because it ended more than 5 days ago.')), 403 ); // 403 Forbidden
		}

		if( !$booking->update( array('status' => 'cancelled', 'cancellation_fee' => Input::get('cancellation_fee')) ) )
		{
			return Response::json( array('errors' => $booking->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return array('status' => 'OK. Booking cancelled.');
	}

	public function postConfirm()
	{
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		if($booking->agent_id === null)
			return Response::json( array('errors' => array('The confirmation method is only allowed for bookings by a travel agent.')), 403 ); // 403 Forbidden

		if($booking->status === 'cancelled')
			return Response::json( array('errors' => array('The booking cannot be confirmed, as it is cancelled.')), 409 ); // 409 Conflict

		if(Helper::isPast($booking->arrival_date))
			return Response::json( array('errors' => array('Cannot confirm booking because it already started.')), 403 ); // 403 Forbidden

		if( !$booking->update( array('status' => 'confirmed') ) )
		{
			return Response::json( array('errors' => $booking->errors()->all()), 406 ); // 406 Not Acceptable
		}

		Helper::sendBookingConfirmation($booking->id);

		return array('status' => 'OK. Booking confirmed.');
	}

	public function getValidate()
	{
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		$booking->lead_customer = $booking->lead_customer()->first();

		$values = array(
			"email"      => $booking->lead_customer->email,
			"phone"      => $booking->lead_customer->phone,
			"country_id" => $booking->lead_customer->country_id
		);

		$rules = array(
			"email"      => 'required',
			"phone"      => 'required',
			"country_id" => 'required'
		);
		$messages = array(
			'required' => 'The lead customer\'s :attribute is missing.',
		);

		$validator = Validator::make($values, $rules, $messages);

		if( $validator->fails() )
			return Response::json( array('errors' => $validator->errors()->all()), 406 ); // 406 Not Acceptable

		return array('status' => 'This booking is valid.');
	}

	public function getPayments()
	{
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		return $booking->payments()->with('paymentgateway')->get();
	}

	public function getRefunds()
	{
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		return $booking->refunds()->with('paymentgateway')->get();
	}

	private function moreThan5DaysAgo($date) {
		$local_time = Helper::localTime();
		$test_date = new DateTime($date, new DateTimeZone( Auth::user()->timezone ));

		if($local_time->diff($test_date)->format('%R%a') < -5)
			return true;

		return false;
	}
}
