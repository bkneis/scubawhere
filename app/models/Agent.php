<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Agent extends Ardent {
	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at');

	public static $rules = array(
		'name'            => 'required',
		'website'         => 'active_url',
		'branch_name'     => 'required',
		'branch_address'  => 'required',
		'branch_phone'    => '',
		'branch_email'    => 'email',
		'billing_address' => '',
		'billing_phone'   => '',
		'billing_email'   => 'email',
		'commission'      => 'required|numeric|between:0,100',
		'terms'           => 'required|in:fullamount,deposit,banned'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->branch_name) )
			$this->branch_name = Helper::sanitiseString($this->branch_name);

		if( isset($this->branch_address) )
			$this->branch_address = Helper::sanitiseString($this->branch_address);

		if( isset($this->branch_phone) )
			$this->branch_phone = Helper::sanitiseString($this->branch_phone);

		if( isset($this->billing_address) )
			$this->billing_address = Helper::sanitiseString($this->billing_address);

		if( isset($this->billing_phone) )
			$this->billing_phone = Helper::sanitiseString($this->billing_phone);
	}

	public function bookings()
	{
		return $this->hasMany('Booking');
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function customers()
	{
		return $this->hasManyThrough('Customer', 'Booking');
	}
}
