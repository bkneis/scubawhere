<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Payment extends Ardent {
	protected $fillable = array('amount', 'currency_id', 'paymentgateway_id');

	public static $rules = array(
		'amount'            => 'required|numeric|min:0',
		'currency_id'       => 'required|integer|exists:currencies,id',
		'paymentgateway_id' => 'required|integer|exists:paymentgateways,id'
	);

	public function beforeSave()
	{
		if( isset($this->amount) )
		{
			$currency = new Currency( $this->currency );
			$this->amount = (int) round( $this->amount * $currency->getSubunitToUnit() );
		}
	}

	public function bookings()
	{
		return $this->belongsTo('Booking');
	}

	public function paymentgateways()
	{
		return $this->belongsTo('Paymentgateway');
	}
	
	public function currency()
	{
		return $this->belongsTo('Currency');
	}

}
