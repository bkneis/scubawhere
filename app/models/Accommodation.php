<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Accommodation extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'capacity');

	protected $appends = array('has_bookings', 'trashed');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'capacity'    => 'integer|min:1'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);
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

	public function customers()
	{
		return $this->belongsToMany('Customer', 'accommodation_booking')->withPivot('booking_id', 'start', 'end')->withTimestamps();
	}

	public function basePrices()
	{
		return $this->morphMany('Price', 'owner')->whereNull('until');
	}

	public function prices()
	{
		return $this->morphMany('Price', 'owner')->whereNotNull('until');
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking')->withPivot('customer_id', 'date', 'nights')->withTimestamps();
	}
}
