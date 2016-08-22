<?php

use LaravelBook\Ardent\Ardent;

class Credit extends Ardent {

	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at');

	public static $rules = array(
		'booking_credits'	=> 'required|integer',
		'email_credits'		=> 'required|integer',
		'renewal_date'		=> 'required|date'
	);

	public function company()
	{
		return $this->belongTo('Company');
	}

}

	
