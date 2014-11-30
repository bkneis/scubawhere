<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

class BookingController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()
			->with(
				'lead_customer',
				'bookingdetails',
					'bookingdetails.customer',
						'bookingdetails.customer.country',
					'bookingdetails.session',
						'bookingdetails.session.trip',
					'bookingdetails.ticket',
					'bookingdetails.packagefacade',
						'bookingdetails.packagefacade.package',
					'bookingdetails.addons',
				'accommodations',
				'payments',
					'payments.currency',
					'payments.paymentgateway'
			)
			->findOrFail( Input::get('id') );

			$booking->bookingdetails->each(function($detail)
			{
				$detail->ticket->calculatePrice( $detail->session->start );
			});

			$booking->accommodations->each(function($accommodation)
			{
				$accommodation->calculatePrice( $accommodation->pivot->start, $accommodation->pivot->end );

				$accommodation->customer = Customer::where('id', $accommodation->pivot->customer_id)->first();
			});

			return $booking;
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll($from = 0, $take = 10)
	{
		return Auth::user()->bookings()
			->with('lead_customer', 'lead_customer.country', 'payments', 'payments.paymentgateway')
			->orderBy('updated_at', 'DESC')
			->skip($from)
			->take($take)
			->get();
	}

	public function postInit()
	{
		$data = Input::only('agent_id', 'source');

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

		$booking = new Booking($data);

		// Generate a reference number and check whether it is unique
		// TODO This is OK, because the reference number is not validated in the ruleset, but it may create an unreasonable amount of database queries in the future
		// MONITOR
		do
		{
			$booking->reference = Helper::booking_reference_number();
		}
		while( Booking::where('reference', $booking->reference)->count() >= 1 );

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
		 * ticket_id
		 * session_id
		 *
		 * package_id (optional)
		 * packagefacade_id (optional)
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

		try
		{
			if( !Input::has('ticket_id') ) throw new ModelNotFoundException();
			$ticket = Auth::user()->tickets()->with('boats', 'boatrooms')->findOrFail( Input::get('ticket_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			if( !Input::has('session_id') ) throw new ModelNotFoundException();
			$departure = Auth::user()->departures()->where('sessions.id', Input::get('session_id'))->with('boat', 'boat.boatrooms')->firstOrFail();
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}

		if( Input::has('packagefacade_id') )
		{
			try
			{
				$packagefacade = $booking->packagefacades()->findOrFail( Input::get('packagefacade_id') );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The packagefacade could not be found.')), 404 ); // 404 Not Found
			}

			$package = $packagefacade->package();
		}
		elseif( Input::has('package_id') )
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
		else
			$package = false;

		// Validate that the customer is not already booked for this session on another booking
		$check = Auth::user()->bookings()
			->whereNotIn('id', array($booking->id))
			->whereHas('bookingdetails', function($query) use ($customer, $departure)
			{
				$query
					->where('customer_id', $customer->id)
					->where('session_id', $departure->id);
			})->count();
		if( $check > 0 )
			return Response::json( array('errors' => array('The customer is already booked on this session in another booking!')), 403 ); // 403 Forbidden

		// Validate that the ticket/package can be booked for this session
		try
		{
			$departure->trip->tickets()->where(function($query) use ($package)
			{
				if( $package )
				{
					$query->whereHas('packages', function($query) use ($package)
					{
						$query->where('id', $package->id);
					});
				}
			})->findOrFail( $ticket->id );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('This ticket/package can not be booked for this session\'s trip.')), 403 ); // 403 Forbidden
		}

		// Check if the session's boat is allowed for the ticket
		if( $ticket->boats()->count() > 0 )
		{
			$boatIDs = $ticket->boats()->lists('id');
			if( !in_array($departure->boat_id, $boatIDs) )
				return Response::json( array('errors' => array('This ticket is not eligable for this session\'s boat.')), 403 ); // 403 Forbidden
		}

		// Check if the session's boat's boatrooms are allowed for the ticket
		if( $ticket->boatrooms()->count() > 0)
		{
			$boatroomIDs = $ticket->boatrooms()->lists('id');
			if( count( array_intersect($departure->boat->boatrooms()->lists('id'), $boatroomIDs) ) == 0 )
				return Response::json( array('errors' => array('This ticket is not eligable for this session\'s boat\'s boatrooms.')), 403 ); // 403 Forbidden
		}

		// Validate remaining capacity on session
		$capacity = $departure->getCapacityAttribute();
		if( $capacity[0] >= $capacity[1] )
		{
			// Session/Boat already full/overbooked
			return Response::json( array('errors' => array('The session is already fully booked!')), 403 ); // 403 Forbidden
		}

		// Validate remaining package capacity on session
		if( isset($package) && !empty($package->capacity) )
		{
			// Package's capacity is *not* infinite and must be checked
			$usedUp = $departure->bookingdetails()->whereHas('packagefacade', function($query) use ($package)
			{
				$query->where('package_id', $package->id);
			})->count();
			if( $usedUp >= $package->capacity )
			{
				// TODO Check for extra one-time packages for this session and their capacity

				return Response::json( array('errors' => array('The package\'s capacity on this session is already reached!')), 403 ); // 403 Forbidden
			}
		}

		// If there is a package, but no packagefacade yet, create a new packagefacade
		if( $package && !isset($packagefacade) )
		{
			$packagefacade = new Packagefacade( array('package_id' => $package->id) );
			$packagefacade->save();
		}

		// If all checks completed successfully, write into database
		$bookingdetail = new Bookingdetail( array(
			'customer_id'      => $customer->id,
			'ticket_id'        => $ticket->id,
			'session_id'       => $departure->id,
			'packagefacade_id' => $package ? $packagefacade->id : null
		) );
		$bookingdetail = $booking->bookingdetails()->save($bookingdetail);

		// If this is the booking's first added details, set lead_customer_id
		if($booking->bookingdetails()->count() === 1)
			$booking->update( array('lead_customer_id' => $customer->id) );

		// Update booking price
		$ticket->calculatePrice($departure->start);

		$booking->updatePrice();

		return array(
			'status'               => 'OK. Booking details added.',
			'id'                   => $bookingdetail->id,
			'decimal_price'        => $booking->decimal_price,
			'ticket_decimal_price' => $ticket->decimal_price,
			'packagefacade_id'     => $package ? $packagefacade->id : false
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
			$bookingdetail = $booking->bookingdetails()->findOrFail( Input::get('bookingdetail_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The bookingdetail has not been found.')), 404 ); // 404 Not Found
		}

		// Execute delete
		$bookingdetail->delete();

		// Update booking price
		$booking->updatePrice();

		return array('status' => 'OK. Bookingdetail removed.', 'decimal_price' => $booking->decimal_price); // 200 OK
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

		// Check if the bookingdetail belongs to the booking
		try
		{
			if( !Input::get('bookingdetail_id') ) throw new ModelNotFoundException();
			$bookingdetail = $booking->bookingdetails()->findOrFail( Input::get('bookingdetail_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}


		$quantity = Input::get('quantity', 1);
		$validator = Validator::make(
			array('quantity' => $quantity),
			array('quantity' => 'integer|min:1')
		);

		if( $validator->fails() )
			return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request

		$bookingdetail->addons()->attach( $addon->id, array('quantity' => $quantity) );

		// Update booking price
		$booking->updatePrice();

		return array('status' => 'OK. Addon added.', 'decimal_price' => $booking->decimal_price);
	}

	public function postRemoveAddon()
	{
		/**
		 * Required input parameters
		 * booking_id
		 * bookingdetail_id
		 * addon_id
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

		// Check if the bookingdetail belongs to the booking
		try
		{
			if( !Input::get('bookingdetail_id') ) throw new ModelNotFoundException();
			$bookingdetail = $booking->bookingdetails()->findOrFail( Input::get('bookingdetail_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}

		// Don't need to check if addon belongs to company because detaching wouldn't throw an error if it's not there in the first place.
		$bookingdetail->addons()->detach( $addon->id );

		// Update booking price
		$booking->updatePrice();

		return array('status' => 'OK. Addon removed.', 'decimal_price' => $booking->decimal_price);
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
		$end = Input::get('end');
		$validator = Validator::make(
			array(
				'start' => $start,
				'end'   => $end
			),
			array(
				'start' => 'required|date|after:'.date('Y-m-d', strtotime('2 days ago')),
				'end'   => 'required|date|after:'.date('Y-m-d', strtotime('2 days ago'))
			)
		);

		if( $validator->fails() )
		{
			return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request
		}

		// Check if accommodation is available for the selected days
		$current_date = new DateTime($start);
		$end_date = new DateTime($end);
		do
		{
			if( $accommodation->bookings()
				->wherePivot('start', '<=', $current_date)
				->wherePivot('end', '>', $current_date)
				->where('confirmed', 1)
				->orWhereNotNull('reserved')
				->count() >= $accommodation->capacity )
				return Response::json( array('errors' => array('The accommodation is not available for the '.$current_date->format('Y-m-d').'!')), 403 ); // 403 Forbidden

			$current_date->add( new DateInterval('P1D') );
		}
		while( $current_date < $end_date );

		$booking->accommodations()->attach( $accommodation->id, array('customer_id' => $customer->id, 'start' => $start, 'end' => $end) );

		// Update booking price
		$accommodation->calculatePrice($start, $end);

		$booking->updatePrice();

		return array(
			'status'                      => 'OK. Accommodation added.',
			'decimal_price'               => $booking->decimal_price,
			'accommodation_decimal_price' => $accommodation->decimal_price
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
		 */

		if( !Input::has('booking_id') )
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found

		if( !Input::has('accommodation_id') )
			return Response::json( array('errors' => array('The accommodation could not be found.')), 404 ); // 404 Not Found

		if( !Input::has('customer_id') )
			return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found

		try
		{
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		// Don't need to check if addon belongs to company because detaching wouldn't throw an error if it's not there in the first place.
		$booking->accommodations()
			->where('id', Input::get('accommodation_id') )
			->wherePivot('customer_id', Input::get('customer_id'))
			->detach();

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

		$data = Input::only(
			'pick_up_location', // Just text
			'pick_up_date',     // Must be date
			'pick_up_time',     // Must be time
			'discount',         // Should be decimal
			'comment'           // Text
		);

		if( !( empty($data['pick_up_date']) || empty($data['pick_up_time']) ) )
		{
			$datetime = new DateTime($data['pick_up_date'].' '.$data['pick_up_time']);
			$data['pick_up_date'] = $datetime->format('Y-m-d');
			$data['pick_up_time'] = $datetime->format('H:i:s');
		}

		if( empty($data['pick_up_location']) ) $data['pick_up_location'] = null;
		if( empty($data['pick_up_date']) ) $data['pick_up_date'] = null;
		if( empty($data['pick_up_time']) ) $data['pick_up_time'] = null;
		if( empty($data['discount']) ) $data['discount'] = null;

		if( !$booking->update($data) )
		{
			return Response::json( array('errors' => $booking->errors()->all()), 406 ); // 406 Not Acceptable
		}

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

		$data = Input::only('reserved');

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

		if( !$booking->update( array('saved' => true) ) )
		{
			return Response::json( array('errors' => $booking->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return array('status' => 'OK. Booking saved');
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

		return $booking->payments();
	}
}
