<?php

use LaravelBook\Ardent\Ardent;

class Departure extends Ardent {
	protected $guarded = array('id', 'trip_id', 'created_at', 'updated_at', 'deleted_at');
	protected $fillable = array('start', 'boat_id', 'timetable_id');

	protected $table = 'sessions';

	protected $appends = array('capacity');

	protected $softDelete = true;

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
		return array( $this->bookings()->count(), $this->boat()->first()->capacity );
	}

	public function trip()
	{
		return $this->belongsTo('Trip');
	}

	public function boat()
	{
		return $this->belongsTo('Boat');
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details', 'session_id', 'booking_id')->withPivot('ticket_id', 'package_id');
	}

	public function timetable()
	{
		return $this->belongsTo('Timetable');
	}
}
