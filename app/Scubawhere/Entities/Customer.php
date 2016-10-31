<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

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
		'country_id',
		'phone',
		'last_dive',
		'number_of_dives',
		'chest_size',
		'shoe_size',
		'height'
	);

	public static $rules = array(
		'email'           => 'email',
		'firstname'       => 'required',
		'lastname'        => 'required',
		'birthday'        => 'sometimes|date',
		'gender'          => 'integer|between:1,3',
		'address_1'       => '',
		'address_2'       => '',
		'city'            => '',
		'county'          => '',
		'postcode'        => '',
		'country_id'      => 'sometimes|integer|exists:countries,id',
		'phone'           => '',
		'last_dive'       => 'date',
		'number_of_dives' => 'integer|min: 0',
		'chest_size'      => '',
		'shoe_size'       => '',
		'height'          => ''
	);

	public $appends = array('unsubscribed');

	public function beforeSave()
	{
		if( isset($this->firstname) )
			$this->firstname = Helper::sanitiseString($this->firstname);

		if( isset($this->lastname) )
			$this->lastname = Helper::sanitiseString($this->lastname);

		if( isset($this->birthday) )
			if(empty($this->birthday)) $this->birthday = null;

		if( isset($this->address_1) )
			$this->address_1 = Helper::sanitiseString($this->address_1);

		if( isset($this->address_2) )
			$this->address_2 = Helper::sanitiseString($this->address_2);

		if( isset($this->city) )
			$this->city = Helper::sanitiseString($this->city);

		if( isset($this->county) )
			$this->county = Helper::sanitiseString($this->county);

		if( empty($this->country_id) )
			$this->country_id = null;

		if( isset($this->postcode) )
			$this->postcode = Helper::sanitiseString($this->postcode);

		if( isset($this->phone) )
			$this->phone = Helper::sanitiseString($this->phone);

		if(empty($this->last_dive))
			$this->last_dive = null;

		if(empty($this->number_of_dives))
			$this->number_of_dives = null;

		if( isset($this->chest_size) )
			$this->chest_size = Helper::sanitiseString($this->chest_size);

		if( isset($this->shoe_size) )
			$this->shoe_size = Helper::sanitiseString($this->shoe_size);

		if( isset($this->height) )
			$this->height = Helper::sanitiseString($this->height);
	}

	public function getUnsubscribedAttribute()
	{
		if(isset($this->crmSubscription()->first()->subscribed))
		{
			return ($this->crmSubscription()->first()->subscribed == 0) ? true : false;
		}
		else
		{
			return false;
		}
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function accommodations()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Accommodation', 'accommodation_booking')->withPivot('booking_id', 'date', 'nights')->withTimestamps();
	}

	public function addons()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Addon', '\Scubawhere\Entities\Bookingdetail');
	}

	public function bookingdetails()
	{
		return $this->hasMany('\Scubawhere\Entities\Bookingdetail');
	}

	public function bookings()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Booking', 'booking_details')->withPivot('ticket_id', 'session_id', 'package_id', 'is_lead');
	}

	public function certificates()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Certificate')->withTimestamps();
	}

	public function country()
	{
		return $this->belongsTo('\Scubawhere\Entities\Country');
	}
    
    public function tokens()
    {
        return $this->hasMany('\Scubawhere\Entities\CrmToken');
    }
    
    public function crmSubscription()
    {
        return $this->hasOne('\Scubawhere\Entities\CrmSubscription');
    }
}
