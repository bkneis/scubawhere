<?php

use LaravelBook\Ardent\Ardent;

class Bookingdetail extends Ardent {

	protected $fillable = array('customer_id', 'is_lead', 'ticket_id', 'session_id', 'boatroom_id', 'packagefacade_id');

	protected $table = 'booking_details';

	public static $rules = array();

	public function beforeSave()
	{
		//
	}

	public function addons()
	{
		return $this->belongsToMany('Addon')->withTrashed()->withPivot('quantity')->withTimestamps();
	}

	public function boatroom()
	{
		return $this->belongsTo('Boatroom');
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

	public function departure()
	{
		return $this->belongsTo('Departure', 'session_id')->withTrashed();
	}

	public function session()
	{
		return $this->belongsTo('Departure', 'session_id')->withTrashed();
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
