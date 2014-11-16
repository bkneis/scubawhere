<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Company extends Ardent implements UserInterface, RemindableInterface {
	use RemindableTrait;

	protected $guarded = array('id', 'password', 'verified', 'views', 'remember_token', 'created_at', 'updated_at');

	protected $appends = array('currency', 'country', 'agencies');

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	public static $rules = array(
		'username'            => 'required|alpha_dash|between:4,64|different:name|unique:companies,username',
		'password'            => 'size:60',
		'email'               => 'required|email|unique:companies,email',
		'name'                => 'required',
		'description'         => '',
		'address_1'           => 'required',
		'address_2'           => '',
		'city'                => 'required',
		'county'              => '',
		'postcode'            => 'required',
		'country_id'          => 'required|integer|exists:countries,id',
		'currency_id'         => 'required|integer|exists:currencies,id',
		'business_email'      => 'required|email|unique:companies,business_email',
		'business_phone'      => 'required',
		'vat_number'          => '',
		'registration_number' => '',
		'phone'               => 'required',
		'contact'             => 'required',
		'website'             => 'active_url',
		'logo'                => '',
		'photo'               => '',
		'video'               => '',
		'views'               => 'integer'
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
	}

	public function getCurrencyAttribute()
	{
		return $this->currency()->first();
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
		return $this->hasMany('Accommodation');
	}

	public function addons()
	{
		return $this->hasMany('Addon');
	}

	public function agencies()
	{
		return $this->belongsToMany('Agency')->withTimestamps();
	}

	public function agents()
	{
		return $this->hasMany('Agent');
	}

	public function boats()
	{
		return $this->hasMany('Boat');
	}

	public function boatrooms()
	{
		return $this->hasMany('Boatroom');
	}

	public function bookings()
	{
		return $this->hasMany('Booking');
	}

	public function country()
	{
		return $this->belongsTo('Country');
	}

	public function currency()
	{
		return $this->belongsTo('Currency');
	}

	public function customers()
	{
		return $this->hasMany('Customer');
	}

	public function locations()
	{
		return $this->belongsToMany('Location')->withTimestamps();
	}

	public function packages()
	{
		return $this->hasMany('Package');
	}

	public function departures()
	{
		return $this->hasManyThrough('Departure', 'Trip'/*, 'company_id', 'trip_id'*/);
	}

	public function tickets()
	{
		return $this->hasMany('Ticket');
	}

	public function timetables()
	{
		return $this->hasMany('Timetable');
	}

	public function trips()
	{
		return $this->hasMany('Trip');
	}

	/**
	 * Booking relations
	 */
	/*public function booked_packages()
	{
		return $this->hasManyThrough('Package', 'Booking');
	}*/

	public function booked_sessions()
	{
		return $this->hasManyTrough('Session', 'Booking');
	}

	public function booked_tickets()
	{
		return $this->hasManyTrough('Ticket', 'Booking');
	}

	/* END Relations */

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	/**
	 * Additions with Laravel v4.1.26
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
		return 'remember_token';
	}
	/**
	 * END Additions
	 */

}
