<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Ticket extends Ardent {
	protected $guarded = array('id', 'trip_id', 'active', 'created_at', 'updated_at');

	public static $rules = array(
		'name'        => 'required',
		'description' => 'required',
		'price'       => 'required|numeric|min:0',
		'currency'    => 'required|alpha|size:3'
	);

	public function beforeSave()
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		$this->currency = Helper::currency($this->currency);
	}

	public function trip()
	{
		return $this->belongsTo('Trip');
	}

	public function boats()
	{
		return $this->belongsToMany('Boat')->withPivot('accommodation_id');
	}

	public function packages()
	{
		return $this->belongsToMany('Package');
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details')->withPivot('session_id', 'package_id', 'customer_id', 'is_lead');
	}

}
