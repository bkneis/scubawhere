<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;
use ScubaWhere\Context;

class RefundController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->refunds()->with('currency', 'paymentgateway')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The refund could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll($from = 0, $take = 10)
	{
		return Context::get()->refunds()->with('currency', 'paymentgateway')->skip($from)->take($take)->get();
	}

	public function getFilter()
	{
		$after  = Input::get('after', null);
		$before = Input::get('before', null);

		if(empty($after) || empty($before))
			return Response::json(['errors' => ['Both the "after" and the "before" parameters are required.']], 400); // 400 Bad Request

		return Context::get()->refunds()->with(
			// 'currency',
			'paymentgateway',
			'booking',
				'booking.lead_customer'
		)->whereBetween('received_at', [$after, $before])->get();
	}

	public function getPaymentgateways()
	{
		return Paymentgateway::all();
	}

	public function postAdd()
	{
		try
		{
			if( !Input::has('booking_id') ) throw new ModelNotFoundException();
			$booking = Context::get()->bookings()->findOrFail( Input::get('booking_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The booking could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			if( !Input::has('paymentgateway_id') ) throw new ModelNotFoundException();
			Paymentgateway::findOrFail( Input::get('paymentgateway_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The paymentgateway could not be found.')), 404 ); // 404 Not Found
		}

		// For now, just use the company's currency
		$data['currency_id']       = Context::get()->currency->id;

		// This needs to be defined AFTER $data['currency_id']!
		$data['amount']            = Input::get('amount');
		$data['paymentgateway_id'] = Input::get('paymentgateway_id');
		// $data['received_at']       = Input::get('received_at');
		$data['received_at']       = Helper::localTime()->format('Y-m-d');

		// Check that received_at date lies in the past
		/* if(!Helper::isPast($data['received_at']))
			return Response::json( array('errors' => array('The received_at date must lie in the past.')), 406 ); // 406 Not Acceptable */

		// Check if amount is higher than what needs to be refunded
		$sumPayed    = $booking->payments()->sum('amount');
		$sumRefunded = $booking->refunds()->sum('amount');
		$remaining   = $sumPayed - $sumRefunded - $booking->cancellation_fee;
		if( $data['amount'] > $remaining )
			return Response::json( array('errors' => array('The entered amount is more than the remaining refund.')), 406 ); // 406 Not Acceptable

		$refund = new Refund($data);

		if( !$refund->validate() )
		{
			return Response::json( array('errors' => $refund->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$refund = $booking->refunds()->save($refund);

		return Response::json( array('status' => 'OK. Refund added', 'refund' => $refund), 201 ); // 201 Created
	}
}
