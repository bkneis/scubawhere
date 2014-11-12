<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Customer extends Ardent {
	protected $fillable = array(
		'email',
		'firstname',
		'lastname',
		'birthday',
		'gender',
		'address_1',
		'address_2',
		'city',
		'county',
		'postcode',
		// 'region_id',
		'country_id',
		'phone',
		'last_dive'
	);

	public static $rules = array(
		'email'          => 'email',
		'firstname'      => 'required',
		'lastname'       => 'required',
		'birthday'       => 'date',
		'gender'         => 'integer|between:1,3',
		'address_1'      => '',
		'address_2'      => '',
		'city'           => '',
		'county'         => '',
		'postcode'       => '',
		'country_id'     => 'integer|exists:countries,id',
		'phone'          => '',
		'last_dive'      => 'date'
	);

	public function beforeSave()
	{
		if( isset($this->firstname) )
			$this->firstname = Helper::sanitiseString($this->firstname);

		if( isset($this->lastname) )
			$this->lastname = Helper::sanitiseString($this->lastname);

		if( isset($this->address_1) )
			$this->address_1 = Helper::sanitiseString($this->address_1);

		if( isset($this->address_2) )
			$this->address_2 = Helper::sanitiseString($this->address_2);

		if( isset($this->city) )
			$this->city = Helper::sanitiseString($this->city);

		if( isset($this->county) )
			$this->county = Helper::sanitiseString($this->county);

		if( isset($this->postcode) )
			$this->postcode = Helper::sanitiseString($this->postcode);

		if( isset($this->phone) )
			$this->phone = Helper::sanitiseString($this->phone);
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function accommodations()
	{
		return $this->belongsToMany('Accommodation', 'accommodation_booking')->withPivot('booking_id', 'date', 'nights')->withTimestamps();
	}

	public function addons()
	{
		return $this->hasManyThrough('Addon', 'Bookingdetail');
	}

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail');
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details')->withPivot('ticket_id', 'session_id', 'package_id', 'is_lead');
	}

	public function certificates()
	{
		return $this->belongsToMany('Certificate')->withTimestamps();
	}

	public function country()
	{
		return $this->belongsTo('Country');
	}
}
