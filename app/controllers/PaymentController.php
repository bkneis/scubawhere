<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

class PaymentController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->payments()->with('currency', 'paymentgateway')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The payment could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll($from = 0, $take = 10)
	{
		return Auth::user()->payments()->with('currency', 'paymentgateway')->orderBy('created_at', 'DESC')->skip($from)->take($take)->get();
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
			$booking = Auth::user()->bookings()->findOrFail( Input::get('booking_id') );
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
		$data['currency_id'] = Auth::user()->currency->id;

		// This needs to be defined AFTER $data['currency_id']!
		$data['amount'] = Input::get('amount');
		$data['paymentgateway_id'] = Input::get('paymentgateway_id');

		// Check if amount is higher than what needs to be paid
		$sum       = $booking->payments()->sum('amount');
		$remaining = $booking->price - $sum;

		$currency = new PhilipBrown\Money\Currency( Auth::user()->currency->code );

		$remaining = number_format(
			$remaining / $currency->getSubunitToUnit(), // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);

		if( $data['amount'] > $remaining )
			return Response::json( array('errors' => array('The entered amount is more than the remaining cost of the booking.')), 406 ); // 406 Not Acceptable

		$data['confirmed'] = true;

		$payment = new Payment($data);

		if( !$payment->validate() )
		{
			return Response::json( array('errors' => $payment->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$payment = $booking->payments()->save($payment);

		return Response::json( array('status' => 'OK. Payment added', 'payment' => $payment, 201 ); // 201 Created
	}
}
