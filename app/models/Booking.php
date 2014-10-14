<?php

use LaravelBook\Ardent\Ardent;

class Booking extends Ardent {
	protected $fillable = array(
		'agent_id',
		'source',
		// 'price',
		'currency',
		'discount',
		// 'confirmed',
		'reserved',
		'pick_up_location',
		'pick_up_time',
		'comment'
	);

	public static $rules = array(
		'agent_id'         => 'integer|exists:agents,id|required_without:source',
		'source'           => 'alpha|required_without:agent_id|in:telephone,email,facetoface'/*,frontend,widget,other'*/,
		'price'            => 'integer|min:0',
		'currency'         => 'alpha|size:3|valid_currency',
		'discount'         => 'integer|min:0',
		'confirmed'        => 'integer|in:0,1',
		'reserved'         => 'date|after:now',
		'pick_up_location' => '',
		'pick_up_time'     => 'date|after:now',
		'comment'          => ''
	);

	public function beforeSave()
	{
		if( isset($this->pick_up) )
			$this->pick_up = Helper::sanitiseString($this->pick_up);

		if( isset($this->drop_off) )
			$this->drop_off = Helper::sanitiseString($this->drop_off);

		if( isset($this->comments) )
			$this->comments = Helper::sanitiseString($this->comments);
	}

	public function customers()
	{
		return $this->belongsToMany('Customer', 'booking_details')->withPivot('ticket_id', 'session_id', 'package_id', 'is_lead')->withTimestamps();
	}

	public function lead_customer()
	{
		return $this->belongsToMany('Customer', 'booking_details')->wherePivot('is_lead', 1)->withPivot('ticket_id', 'session_id', 'package_id', 'is_lead');
	}

	public function addons()
	{
		return $this->hasManyThrough('Addon', 'Bookingdetail');
	}

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail');
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function packages()
	{
		return $this->belongsToMany('Package', 'booking_details')->withPivot('customer_id', 'is_lead', 'ticket_id', 'session_id');
	}

	public function tickets()
	{
		return $this->belongsToMany('Ticket', 'booking_details');
	}

	public function payments()
	{
		return $this->hasMany('Payment');
	}


	public function updatePrice()
	{
		$packagesSum = $this->packages()->distinct()->sum('price');

		$ticketsSum = $this->tickets()->wherePivot('package_id', null)->sum('price');

		$this->price = $packagesSum + $ticketsSum;
		$this->currency = $this->tickets()->first()->currency;

		$this->save();
	}
}
