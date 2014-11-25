<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;

class Departure extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $guarded = array('id', 'trip_id', 'created_at', 'updated_at', 'deleted_at');
	protected $fillable = array('start', 'boat_id', 'timetable_id');

	protected $table = 'sessions';

	protected $appends = array('capacity');

	public static $rules = array(
		'start'        => 'required|date',
		'boat_id'      => 'required|integer|exists:boats,id',
		'timetable_id' => 'integer|exists:timetables,id'
	);

	public function beforeSave()
	{
		//
	}

	public function getCapacityAttribute()
	{
		// TODO Also calculate utilisation by boatrooms

		return array(
			$this->bookingdetails()->whereHas('booking', function($query)
			{
				$query->where('confirmed', 1)->orWhereNotNull('reserved');
			})->count(),
			$this->boat()->withTrashed()->first()->capacity );
	}
	
	public function getTrashedAttribute()
	{
		return $this->trashed();
	}

	public function addons()
	{
		return $this->hasManyThrough('Addon', 'Bookingdetail');
	}

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail', 'session_id');
	}

	public function trip()
	{
		return $this->belongsTo('Trip')->withTrashed();
	}

	public function boat()
	{
		return $this->belongsTo('Boat')->withTrashed();
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details', 'session_id', 'booking_id')
			->withPivot('ticket_id', 'customer_id', 'is_lead', 'packagefacade_id')
			->withTimestamps();
	}

	public function timetable()
	{
		return $this->belongsTo('Timetable');
	}
}
