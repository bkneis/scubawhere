<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class Payment extends Ardent {

	protected $fillable = array('amount', 'currency_id', 'paymentgateway_id', 'received_at');

	public static $rules = array(
		'amount'            => 'required|numeric|min:0.01',
		'currency_id'       => 'required|integer',
		'paymentgateway_id' => 'required|integer',
		'received_at'       => 'required|date',
	);

	public function setAmountAttribute($value)
	{
		$currency = Currency::find( $this->currency_id );
		$currency = new \PhilipBrown\Money\Currency( $currency->code );
		$this->attributes['amount'] = (int) round( $value * $currency->getSubunitToUnit() );
	}

	public function getAmountAttribute($value)
	{
		$currency = new \PhilipBrown\Money\Currency( $this->currency->code );

		return number_format(
			$value / $currency->getSubunitToUnit(), // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);
	}

	public function booking()
	{
		return $this->belongsTo('\Scubawhere\Entities\Booking');
	}

	public function paymentgateway()
	{
		return $this->belongsTo('\Scubawhere\Entities\Paymentgateway');
	}

	public function currency()
	{
		return $this->belongsTo('\Scubawhere\Entities\Currency');
	}

}
