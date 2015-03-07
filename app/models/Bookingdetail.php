<?php

use LaravelBook\Ardent\Ardent;

class Bookingdetail extends Ardent {

	protected $fillable = array('customer_id', 'is_lead', 'ticket_id', 'session_id', 'boatroom_id', 'packagefacade_id', 'course_id', 'training_session_id');

	protected $table = 'booking_details';

	public static $rules = array();

	public function beforeSave()
	{
		//
	}

	public function addons()
	{
		return $this->belongsToMany('Addon')->withPivot('quantity', 'packagefacade_id')->withTimestamps()->withTrashed();
	}

	public function boatroom()
	{
		return $this->belongsTo('Boatroom');
	}

	public function booking()
	{
		return $this->belongsTo('Booking');
	}

	public function course()
	{
		return $this->belongsTo('Course')->withTrashed();
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

	public function training_session()
	{
		return $this->belongsTo('TrainingSession')->withTrashed();
	}

	public function packagefacade()
	{
		return $this->belongsTo('Packagefacade');
	}
}
