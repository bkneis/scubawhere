<?php

use LaravelBook\Ardent\Ardent;

class Bookingdetail extends Ardent {

	protected $fillable = array();

	protected $table = 'booking_details';

	protected $hidden = array('id');

	public static $rules = array();

	public function beforeSave()
	{
		//
	}

	public function addons()
	{
		return $this->belongsToMany('Addon')->withTrashed()->withPivot('quantity')->withTimestamps();
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
		return $this->belongsTo('Ticket')->withTrashed();
	}

	public function session()
	{
		return $this->belongsTo('Departure')->withTrashed();
	}

	public function packagefacade()
	{
		return $this->belongsTo('Packagefacade');
	}

	/*
	public function package()
	{
		return $this->hasManyThrough('Package', 'Packagefacade');
	}
	*/
}
