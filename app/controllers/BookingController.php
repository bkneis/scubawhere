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
			return Auth::user()->bookings()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->bookings()->get();
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
		 * package_id
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
			Auth::user()->customers()->findOrFail( Input::get('customer_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			if( !Input::get('ticket_id') ) throw new ModelNotFoundException();
			Auth::user()->tickets()->findOrFail( Input::get('ticket_id') );
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

		// Check if this customer is supposed to be the lead customer
		$is_lead = Input::get('is_lead');
		if( !is_bool($is_lead) )
			$is_lead = false;

		// Validate remaining capacity on session
		$departure->getCapacityAttribute();
		if( $departure[0] >= $departure[1] )
		{
			// Session/Boat already full/overbooked
			return Response::json( array('errors' => array('The session is already fully booked!')), 403 ); // 403 Forbidden
		}

		if( Input::get('package_id') && !empty($package->capacity) )
		{
			// Validate remaining package capacity on session

			// Package's capacity is *not* infinite and must be checked
			$usedUp = $departure->bookings()->wherePivot('package_id', $package->id)->count();

			if( $usedUp >= $package->capacity )
			{
				return Response::json( array('errors' => array('The package\'s capacity on this session is already reached!')), 403 ); // 403 Forbidden
			}
		}

		// Check for extra one-time packages for this session and their capacity
		// TODO

		// If all checks completed successfully, write into database
		$booking->customers()->attach( Input::get('customer_id'),
			array(
				'is_lead' => $is_lead,
				'ticket_id' => Input::get('ticket_id'),
				'session_id' => Input::get('session_id'),
				'package_id' => Input::get('package_id')
			)
		);

		return Response::json( array('status' => 'OK. Customer assigned.', 'customers' => $booking->customers()), 200 ); // 200 OK
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

		// Check if entry exists on booking_details table
		try
		{
			if( !Input::get('booking_id') ) throw new ModelNotFoundException();
			Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
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
			return Response::json( array('errors' => array('The combination of IDs has not been found. Nothing was done on the server.')), 404 ); // 404 Not Found

		if($affectedRows != 1)
		{
			// Fail because only one record should be affected. Customer and session relation should be unique
			return Response::json( array('errors' => array('This action would affect more than one record and is therefore aborted. Please check why the customer <-> session relationship is not unique.')), 400 ); // 400 Bad Request
		}

		// Execute delete
		$affectedRows = DB::table('booking_details')
			->where('booking_id', $booking->id)
			->where('customer_id', Input::get('customer_id'))
			->where('session_id', Input::get('session_id'))
			->delete();

		return array('status' => 'OK. Booking details removed.');
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
			"email" => $booking->lead_costumer()->first()->email != '',
			"phone" => $booking->lead_costumer()->first()->phone != '',
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
