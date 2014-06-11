<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Payment extends Ardent {
	protected $fillable = array('amount', 'currency', 'paymentgateway_id');

	public static $rules = array(
		'amount'            => 'required|numeric|min:0',
		'currency'          => 'required|alpha|size:3',
		'paymentgateway_id' => 'required|integer|exists:paymentgateways,id'
	);

	public function beforeSave()
	{
		$this->currency = Helper::currency($this->currency);
	}

	public function bookings()
	{
		return $this->belongsTo('Booking');
	}

	public function paymentgateways()
	{
		return $this->belongsTo('Paymentgateway');
	}

}
