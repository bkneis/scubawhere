<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;

class Departure extends Ardent {
	// Superseeded by traits as of the update to Laravel 4.2 (http://laravel.com/docs/upgrade#upgrade-4.2)
	// protected $softDelete = true;
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
		return $this->belongsToMany('Booking', 'booking_details', 'session_id', 'booking_id')->withPivot('ticket_id', 'customer_id', 'is_lead', 'package_id');
	}

	public function timetable()
	{
		return $this->belongsTo('Timetable');
	}
}
