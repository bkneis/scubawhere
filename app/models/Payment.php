<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
//use PhilipBrown\Money\Currency;

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
			$db_currency = Currency::find($this->currency_id);
			$currency = new PhilipBrown\Money\Currency($db_currency->code);
			$this->amount = (int) round( $this->amount * $currency->getSubunitToUnit() );
		}
	}

	public function booking()
	{
		return $this->belongsTo('Booking');
	}

	public function paymentgateway()
	{
		return $this->belongsTo('Paymentgateway');
	}
	
	public function currency()
	{
		return $this->belongsTo('Currency');
	}

}
