<?php

use LaravelBook\Ardent\Ardent;

class Session extends Ardent {
	protected $guarded = array('id', 'trip_id', 'created_at', 'updated_at', 'deleted_at');

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
		return $this->belongsToMany('Booking', 'booking_details')->withPivot('ticket_id', 'package_id');
	}

	public function timetable()
	{
		return $this->belongsTo('Timetable');
	}
}
