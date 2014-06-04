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

		if( !$booking->validate() )
		{
			return Response::json( array('errors' => $booking->errors()->all()), 400 ); // 400 Bad Request
		}

		$booking = Auth::user()->bookings()->save($booking);

		return Response::json( array('status' => 'OK. Booking created', 'id' => $booking->id, 'reference' => $booking->reference), 201 ); // 201 Created
	}

	public function postAttachCustomer()
	{
		$data = Input::only('booking_id', 'customer_id');

		// Check if both IDs exist and belong to the signed-in company
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

		$booking->customers()->attach( Input::get('customer_id')/*, array('chief' => is_this_user_chief?)*/ );

		return Response::json( array('status' => 'OK. Customer assigned.', 'customers' => $booking->customers()), 200 ); // 200 OK
	}

	public function postDetachCustomer()
	{
		$data = Input::only('booking_id', 'customer_id');

		// Check if both IDs exist and belong to the signed-in company
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

		$booking->customers()->detach( Input::get('customer_id'));

		return Response::json( array('status' => 'OK. Customer assigned.', 'customers' => $booking->customers()), 200 ); // 200 OK
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

		return array(
			'customer' => $booking->costumers()->first()->email != ''
		);
	}

	/*
	public function postAdd()
	{
		$data = Input::only(
			'name',
			'website',
			'branch_name',
			'branch_address',
			'branch_phone',
			'branch_email',
			'billing_address',
			'billing_phone',
			'billing_email',
			'commission',
			'terms'
		);

		$agent = new Agent($data);

		if( !$agent->validate() )
		{
			return Response::json( array('errors' => $agent->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$agent = Auth::user()->agents()->save($agent);

		return Response::json( array('status' => 'OK. Agent created', 'id' => $agent->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$agent = Auth::user()->agents()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The agent could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'name',
			'website',
			'branch_name',
			'branch_address',
			'branch_phone',
			'branch_email',
			'billing_address',
			'billing_phone',
			'billing_email',
			'commission',
			'terms'
		);

		if( !$agent->update($data) )
		{
			return Response::json( array('errors' => $agent->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Agent updated.'), 200 ); // 200 OK
	}
	*/
}
