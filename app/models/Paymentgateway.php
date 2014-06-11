<?php

use LaravelBook\Ardent\Ardent;

class Paymentgateway extends Ardent {
	protected $fillable = array();
	protected $guarded = array('*');

	public static $rules = array();

	public function beforeSave()
	{
		//
	}

	public function payments()
	{
		return $this->hasMany('Payment');
	}

}
