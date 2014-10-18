<?php

use LaravelBook\Ardent\Ardent;

class Bookingdetail extends Ardent {

	protected $fillable = array();

	protected $table = 'booking_details';

	public static $rules = array();

	public function beforeSave()
	{
		//
	}

	public function booking()
	{
		return $this->belongsTo('Booking');
	}

	public function customer()
	{
		return $this->belongsTo('Customer');
	}

	public function company()
	{
		return $this->hasManyThrough('Company', 'Booking');
	}

	public function ticket()
	{
		return $this->belongsTo('Ticket');
	}

	public function session()
	{
		return $this->belongsTo('Session');
	}

	public function packagefacade()
	{
		return $this->belongsTo('Packagefacade')->withTimestamps();
	}

	public function package()
	{
		return $this->hasManyThrough('Package', 'Packagefacade')->first();
	}

	public function addons()
	{
		return $this->belongsToMany('Addon')->withPivot('quantity')->withTimestamps();
	}
}
