<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Company extends Ardent implements UserInterface, RemindableInterface {
	use RemindableTrait;

	protected $guarded = array('id', 'password', 'verified', 'views', 'created_at', 'updated_at');

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	public static $rules = array(
		'username'    => 'required|alpha_dash|between:4,64|different:name|unique:companies,username',
		'password'    => 'size:60',
		'email'       => 'required|email|unique:companies,email',
		'name'        => 'required',
		'description' => '',
		'address_1'   => 'required',
		'address_2'   => '',
		'city'        => 'required',
		'county'      => '',
		'postcode'    => 'required',
		'country_id'  => 'required|integer|exists:countries,id',
		'business_email' => 'required',
		'business_phone' => 'required',
		'vat_number'  => 'required',
		'registration_number' => 'required',
		'latitude'    => 'numeric|between:-90,90',
		'longitude'   => 'numeric|between:-180,180',
		'phone'       => 'required',
		'contact'     => '',
		'website'     => 'active_url',
		'agency'      => 'digits:1',
		'logo'        => '',
		'photo'       => '',
		'video'       => '',
		'views'       => 'integer'
	); // add additonal fields to rules

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

	public function accommodations()
	{
		return $this->hasMany('Accommodation');
	}

	public function agents()
	{
		return $this->hasMany('Agent');
	}
	
	public function addons()
	{
		return $this->hasMany('Addon');
	}

	public function boats()
	{
		return $this->hasMany('Boat');
	}

	public function bookings()
	{
		return $this->hasMany('Booking');
	}

	public function country()
	{
		return $this->belongsTo('Country');
	}

	public function customers()
	{
		return $this->hasMany('Customer');
	}

	public function locations()
	{
		return $this->belongsToMany('Location');
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
	public function booked_packages()
	{
		return $this->hasManyThrough('Package', 'Booking');
	}

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
