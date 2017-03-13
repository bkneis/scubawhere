<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class Company extends Ardent {

	private $currency;

	protected $guarded = array('id', 'verified', 'views', 'created_at', 'updated_at');

	protected $appends = array('currency', 'country', 'agencies');

	public static $rules = array(
		'name'                => 'required',
		'description'         => '',
		'address_1'           => '', //'required',
		'address_2'           => '',
		'city'                => '', //'required',
		'county'              => '',
		'postcode'            => '', //'required',
		'country_id'          => 'integer', //'required|integer',
		'currency_id'         => 'integer|exists:currencies,id', // required
		'business_email'      => 'email', //'email|unique:companies,business_email', // required
		'business_phone'      => '', //'required',
		'vat_number'          => '',
		'registration_number' => '',
		'latitude'            => 'numeric|between:-90,90', // required
		'longitude'           => 'numeric|between:-180,180', // required
		'timezone'            => '', //'required',
		'contact'             => 'required',
		'website'             => '', //active_url
		'logo'                => '',
		'photo'               => '',
		'video'               => '',
		'views'               => 'integer',
		'source'              => '',
		'reference_base'      => 'size:3|unique:companies,reference_base',
		'fileExt'             => ''
	);

	public function beforeSave()
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->address_1) )
			$this->address_1 = Helper::sanitiseString($this->address_1);

		if( isset($this->address_2) )
			$this->address_2 = Helper::sanitiseString($this->address_2);

		if( isset($this->postcode) )
			$this->postcode = Helper::sanitiseString($this->postcode);

		if( isset($this->city) )
			$this->city = Helper::sanitiseString($this->city);

		if( isset($this->county) )
			$this->county = Helper::sanitiseString($this->county);

		if( isset($this->business_phone) )
			$this->business_phone = Helper::sanitiseString($this->business_phone);

		if( isset($this->vat_number) )
			$this->vat_number = Helper::sanitiseString($this->vat_number);

		if( isset($this->registration_number) )
			$this->registration_number = Helper::sanitiseString($this->registration_number);

		if( isset($this->phone) )
			$this->phone = Helper::sanitiseString($this->phone);

		if( isset($this->contact) )
			$this->contact = Helper::sanitiseString($this->contact);

		if( isset($this->logo) )
			$this->logo = Helper::sanitiseString($this->logo);

		if( isset($this->photo) )
			$this->photo = Helper::sanitiseString($this->photo);

		if( isset($this->video) )
			$this->video = Helper::sanitiseString($this->video);

		if( isset($this->phone_ext) )
			$this->phone_ext = Helper::sanitiseString($this->phone_ext);

		if( isset($this->business_phone_ext) )
			$this->business_phone_ext = Helper::sanitiseString($this->business_phone_ext);
	}

	public function getCurrencyAttribute()
	{
		if(!$this->currency)
			$this->currency = $this->currency()->first();

		return $this->currency;
	}

	public function getCountryAttribute()
	{
		return $this->country()->first();
	}

	public function getAgenciesAttribute()
	{
		return $this->agencies()->get();
	}

	public function accommodations()
	{
		return $this->hasMany('\Scubawhere\Entities\Accommodation');
	}

	public function addons()
	{
		return $this->hasMany('\Scubawhere\Entities\Addon');
	}

	public function agencies()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Agency')->withTimestamps();
	}

	public function agents()
	{
		return $this->hasMany('\Scubawhere\Entities\Agent');
	}

	public function courses()
	{
		return $this->hasMany('\Scubawhere\Entities\Course');
	}

	public function credits()
	{
		return $this->hasOne('\Scubawhere\Entities\Credit');
	}

	public function boats()
	{
		return $this->hasMany('\Scubawhere\Entities\Boat');
	}

	public function boatrooms()
	{
		return $this->hasMany('\Scubawhere\Entities\Boatroom');
	}

	public function bookings()
	{
		return $this->hasMany('\Scubawhere\Entities\Booking');
	}

	public function bookingdetails()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Bookingdetail', '\Scubawhere\Entities\Booking');
	}

	public function campaigns()
	{
		return $this->hasMany('\Scubawhere\Entities\CrmCampaign');
	}

	public function country()
	{
		return $this->belongsTo('\Scubawhere\Entities\Country');
	}

	public function currency()
	{
		return $this->belongsTo('\Scubawhere\Entities\Currency');
	}

	public function customers()
	{
		return $this->hasMany('\Scubawhere\Entities\Customer');
	}
    
    public function crmSubscriptions()
    {
        return $this->hasMany('\Scubawhere\Entities\CrmSubscription');
    }

	public function crmGroups()
	{
		return $this->hasMany('\Scubawhere\Entities\CrmGroup');
	}

	public function crmGroupRules()
	{
		return $this->hasMany('\Scubawhere\Entities\CrmGroupRule');
	}
    
    public function crmLinks()
    {
        return $this->hasMany('\Scubawhere\Entities\CrmLink');
    }

	public function departures()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Departure', '\Scubawhere\Entities\Trip');
	}
    
    /*public function equipment()
    {
        return $this->hasMany('\Scubawhere\Entities\Equipment');
    }
    
    public function equipmentCategories()
    {
        return $this->hasMany('\Scubawhere\Entities\EquipmentCategory');
    }*/

	public function locations()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Location')->withPivot('description')->withTimestamps();
	}

    public function logs() // Needed to create seperate namespace for logs as its reseved for laravel facade
    {
        return $this->hasMany('Scubawhere\Entities\Log');
    }

	public function packages()
	{
		return $this->hasMany('\Scubawhere\Entities\Package');
	}

	public function pick_ups()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\PickUp', '\Scubawhere\Entities\Booking');
	}

	public function payments()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Payment', '\Scubawhere\Entities\Booking');
	}

	public function refunds()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Refund', '\Scubawhere\Entities\Booking');
	}

	public function schedules()
	{
		return $this->hasMany('\Scubawhere\Entities\Schedule');
	}
    
    public function templates()
	{
		return $this->hasMany('\Scubawhere\Entities\CrmTemplate');
	}

	public function tickets()
	{
		return $this->hasMany('\Scubawhere\Entities\Ticket');
	}

	public function timetables()
	{
		return $this->hasMany('\Scubawhere\Entities\Timetable');
	}

	public function trainings()
	{
		return $this->hasMany('\Scubawhere\Entities\Training');
	}

	public function training_sessions()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\TrainingSession', '\Scubawhere\Entities\Training');
	}

	public function trips()
	{
		return $this->hasMany('\Scubawhere\Entities\Trip');
	}

	public function users()
	{
		return $this->hasMany('\Scubawhere\Entities\User');
	}
}
