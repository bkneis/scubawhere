<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Ticket extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at', 'deleted_at');

	protected $appends = array('has_bookings', 'trashed');

	public static $rules = array(
		'name'        => 'required',
		'description' => 'required',
	);

	public function beforeSave()
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);
	}

	public function getHasBookingsAttribute()
	{
		return $this->bookings()->count() > 0;
	}

	public function getTrashedAttribute()
	{
		return $this->trashed();
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function trips()
	{
		return $this->belongsToMany('Trip')->withTimestamps();
	}

	public function boats()
	{
		return $this->belongsToMany('Boat')->withPivot('accommodation_id')->withTimestamps();
	}

	public function packages()
	{
		return $this->belongsToMany('Package')->withPivot('quantity')->withTimestamps();
	}

	public function prices()
	{
		return $this->morphMany('Price', 'owner')->orderBy('fromMonth');
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details')
			->withPivot('session_id', 'package_id', 'customer_id', 'is_lead')
			->withTimestamps();
	}

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail');
	}

}
