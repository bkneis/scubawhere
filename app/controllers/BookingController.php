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
			return Auth::user()->bookings()->with('customers')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->bookings()->with('customers')->get();
	}

	public function postInit()
	{
		$data = Input::only('agent_id', 'source');

		if( $data['agent_id'] )
		{
			// Check if the agent belongs to the signed-in company
			try
			{
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

		$booking = Auth::user()->bookings()->save($booking);

		return Response::json( array('status' => 'OK. Booking created', 'id' => $booking->id, 'reference' => $booking->reference), 201 ); // 201 Created
	}

	public function postAddDetails()
	{
		/**
		 * Valid input parameters
		 * booking_id
		 * customer_id
		 * is_lead
		 * ticket_id
		 * session_id
		 * package_id (optional)
		 * packagefacade_id (optional)
		 */

		// Check if all IDs exist and belong to the signed-in company
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			if( !Input::get('customer_id') ) throw new ModelNotFoundException();
			$customer = Auth::user()->customers()->findOrFail( Input::get('customer_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			if( !Input::get('ticket_id') ) throw new ModelNotFoundException();
			$ticket = Auth::user()->tickets()->findOrFail( Input::get('ticket_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			if( !Input::get('session_id') ) throw new ModelNotFoundException();
			$departure = Auth::user()->departures()->where('sessions.id', Input::get('session_id'))->firstOrFail();
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}

		if( Input::get('packagefacade_id') )
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
		elseif( Input::get('package_id') )
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

		// Check if this customer is supposed to be the lead customer
		$is_lead = Input::get('is_lead');
		if( !is_bool($is_lead) )
			$is_lead = false;

		// Validate remaining capacity on session
		$capacity = $departure->getCapacityAttribute();
		if( $capacity[0] >= $capacity[1] )
		{
			// Session/Boat already full/overbooked
			return Response::json( array('errors' => array('The session is already fully booked!'), 'capacity' => $capacity), 403 ); // 403 Forbidden
		}

		if( isset($package) && !empty($package->capacity) )
		{
			// Validate remaining package capacity on session

			// Package's capacity is *not* infinite and must be checked
			$usedUp = $departure->bookingdetails()->whereHas('packagefacade', function($q) use ($package)
			{
				$q->where('package_id', $package->id);
			})->count();

			if( $usedUp >= $package->capacity )
			{

				// Check for extra one-time packages for this session and their capacity
				// TODO
				return Response::json( array('errors' => array('The package\'s capacity on this session is already reached!')), 403 ); // 403 Forbidden
			}
		}

		// If all checks completed successfully, write into database
		if( isset($package) && !isset($packagefacade) )
		{
			$packagefacade = new Packagefacade( array('package_id' => $package->id) );
			$packagefacade->save();
		}

		$booking->customers()->attach( $customer->id,
			array(
				'is_lead'          => $is_lead,
				'ticket_id'        => $ticket->id,
				'session_id'       => $departure->id,
				'packagefacade_id' => isset($package) ? $packagefacade->id : null
			)
		);

		// Update booking price
		$booking->updatePrice();

		return array('status' => 'OK. Booking details added.', 'customers' => $booking->customers()->get(), 'price' => $booking->decimal_price()); // 200 OK
	}

	public function postRemoveDetails()
	{
		/**
		 * Valid input parameters
		 * booking_id
		 * customer_id
		 * session_id
		 */

		$data = Input::only('booking_id', 'customer_id', 'session_id');

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

		$affectedRows = DB::table('booking_details')
			->where('booking_id', $booking->id)
			->where('customer_id', Input::get('customer_id'))
			->where('session_id', Input::get('session_id'))
			->count();

		if($affectedRows == 0)
			return Response::json( array('errors' => array('The combination of IDs has not been found. Nothing was changed in the database.')), 404 ); // 404 Not Found

		if($affectedRows > 1)
		{
			// Fail because only one record should be affected. Customer and session relation should be unique
			return Response::json( array('errors' => array('This action would affect more than one record and has therefore been aborted. Please check why the customer <-> session relationship is not unique.')), 400 ); // 400 Bad Request
		}

		// Execute delete
		$affectedRows = DB::table('booking_details')
			->where('booking_id', $booking->id)
			->where('customer_id', Input::get('customer_id'))
			->where('session_id', Input::get('session_id'))
			->delete();

		// Update booking price
		$booking->updatePrice();

		return array('status' => 'OK. Booking details removed.', 'customers' => $booking->customers()->get(), 'price' => $booking->decimal_price()); // 200 OK
	}

	public function postAddAddon()
	{
		/**
		 * Required input parameters
		 * booking_id
		 * session_id
		 * customer_id
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

		if( Input::get('quantity') )
		{
			$quantity = Input::get('quantity');
			$validator = Validator::make(
				array('quantity' => $quantity),
				array('quantity' => 'integer|min:1')
			);

			if( $validator->fails() )
			{
				return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request
			}
		}
		else
		{
			$quantity = 1;
		}

		try
		{
			if( !Input::get('booking_id') )  throw new ModelNotFoundException();
			if( !Input::get('session_id') )  throw new ModelNotFoundException();
			if( !Input::get('customer_id') ) throw new ModelNotFoundException();

			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
			$bookingdetail = $booking->bookingdetails()
				->where('session_id', Input::get('session_id'))
				->where('customer_id', Input::get('customer_id'))
				->first();
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('This combination of IDs could not be found.')), 404 ); // 404 Not Found
		}

		$bookingdetail->addons()->attach( $addon->id, array('quantity' => $quantity) );

		// Update booking price
		$booking->updatePrice();

		return array('status' => 'OK. Addon added.', 'price' => $booking->decimal_price());
	}

	public function postRemoveAddon()
	{
		/**
		 * Required input parameters
		 *
		 * booking_id
		 * session_id
		 * customer_id
		 * addon_id
		 */

		try
		{
			if( !Input::get('booking_id') )  throw new ModelNotFoundException();
			if( !Input::get('session_id') )  throw new ModelNotFoundException();
			if( !Input::get('customer_id') ) throw new ModelNotFoundException();

			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
			$bookingdetails = $booking->bookingdetails()
				->where('session_id', Input::get('session_id'))
				->where('customer_id', Input::get('customer_id'))
				->first();
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('This combination of IDs could not be found.')), 404 ); // 404 Not Found
		}

		// Don't need to check if addon belongs to company because detaching wouldn't throw an error if it's not there in the first place.
		$bookingdetails->detach( $addon->id );

		// Update booking price
		$booking->updatePrice();

		return array('status' => 'OK. Addon removed.', 'price' => $booking->decimal_price());
	}

	public function postEditInfo()
	{
		/**
		 * Valid input parameters
		 *
		 * booking_id
		 * pick_up_location
		 * pick_up_time
		 * discount
		 * reserved
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
			'pick_up_time',     // Must be datetime
			'discount',         // Should be decimal
			'reserved',         // Must be datetime
			'comment');         // Text

		// Convert discount to subunit
		if( !empty($data['discount']) )
		{
			$currency = new Currency( Auth::user()->currency->code );
			$data['discount'] = (int) round( $data['discount'] * $currency->getSubunitToUnit() );
		}


		if( !$booking->update($data) )
		{
			return Response::json( array('errors' => $booking->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Booking information updated.', 'booking' => $booking), 200 ); // 200 OK

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

		// "Validation" rules
		return array(
			"email" => !empty( $booking->lead_customer()->email ),
			"phone" => !empty( $booking->lead_customer()->phone ),
		);
	}

	public function getPayments()
	{
		/**
		 * Valid input parameters
		 * booking_id
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

		return $booking->payments();
	}
}
