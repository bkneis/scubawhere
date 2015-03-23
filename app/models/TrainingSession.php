<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;

class TrainingSession extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('start', 'schedule_id');

	// protected $appends = array('capacity');

	public static $rules = array(
		'start'       => 'required|date',
		'schedule_id' => 'integer'
	);

	public function beforeSave()
	{
		//
	}

	public function getCapacityAttribute()
	{
		$boat = $this->boat()->with('boatrooms')->withTrashed()->first();

		$result = array();

		$result[0] = $this->bookingdetails()
		    ->whereHas('booking', function($query)
		    {
		    	$query->whereIn('status', Booking::$counted);
		    })->count();

		// $result[1] = $boat->capacity;

		return $result;
	}

	/* public function getTrashedAttribute()
	{
		return $this->trashed();
	} */

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail');
	}

	public function customers()
	{
		return $this->belongsToMany('Customer', 'booking_details')->withTimestamps();
	}

	public function training()
	{
		return $this->belongsTo('Training')->withTrashed();
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details')
			/* ->withPivot('ticket_id', 'customer_id', 'is_lead', 'packagefacade_id') */
			->withTimestamps();
	}

	/* public function schedule()
	{
		return $this->belongsTo('Schedule');
	} */
}
