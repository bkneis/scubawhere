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
		'boat_id'      => 'integer|min:1',
		'timetable_id' => 'integer|exists:timetables,id'
	);

	public function beforeSave()
	{
		//
	}

	public function getCapacityAttribute()
	{
		$boat = $this->boat()->with('boatrooms')->withTrashed()->first();

		// First, calculate the overall utilisation
		$result = array();

		$result[0] = $this->bookingdetails()
		    ->whereHas('booking', function($query)
		    {
		    	$query->whereIn('status', Booking::$counted);
		    })->count();

		if($this->trip->boat_required)
		{
			$result[1] = $boat->capacity;

			// Second, calculate the utilisation per boatroom
			$result[2] = array();

			$boat->boatrooms->each(function($boatroom) use (&$result)
			{
				$result[2][$boatroom->id] = array();

				$result[2][$boatroom->id][0] = $this->bookingdetails()
				    ->whereHas('booking', function($query)
				    {
				    	$query->whereIn('status', Booking::$counted);
				    })
				    ->where('boatroom_id', $boatroom->id)
				    ->count();

				$result[2][$boatroom->id][1] = $boatroom->pivot->capacity;
			});
		}

		return $result;
	}

	public function getTrashedAttribute()
	{
		return $this->trashed();
	}

	public function isOvernight($trip)
	{
		if(empty($trip))
			$trip = $this->trip;

		$start = new DateTime($this->start, new DateTimeZone( Auth::user()->timezone ));
		$end   = clone $start;

		$duration_hours   = floor($trip->duration);
		$duration_minutes = round( ($trip->duration - $duration_hours) * 60 );
		$end->add( new DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );

		return $start->format('Y-m-d') !== $end->format('Y-m-d');
	}

	public function addons()
	{
		return $this->hasManyThrough('Addon', 'Bookingdetail');
	}

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail', 'session_id');
	}

	public function customers()
	{
		return $this->belongsToMany('Customer', 'booking_details', 'session_id', 'customer_id')
			->withPivot('ticket_id')
			->withTimestamps();
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
