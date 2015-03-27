<?php

use LaravelBook\Ardent\Ardent;

class Bookingdetail extends Ardent {

	protected $fillable = array('customer_id', 'is_lead', 'ticket_id', 'session_id', 'boatroom_id', 'packagefacade_id', 'course_id', 'training_session_id');

	protected $table = 'booking_details';

	public static $rules = array(
		'customer_id'         => 'required|integer|min:1',
		'ticket_id'           => 'integer|min:1|required_with:session_id|required_without:course_id',
		'session_id'          => 'integer|min:1|required_with:ticket_id|required_without:training_session_id',
		'boatroom_id'         => 'integer|min:1',
		'packagefacade_id'    => 'integer|min:1',
		'course_id'           => 'integer|min:1|required_with:training_session_id|required_without:ticket_id',
		'training_session_id' => 'integer|min:1|required_without:session_id'
	);

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
