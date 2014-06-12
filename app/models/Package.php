<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Package extends Ardent {
	protected $fillable = array('name', 'description', 'price', 'currency', 'capacity');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'price'       => 'required|numeric|min:0',
		'currency'    => 'required|alpha|size:3|valid_currency',
		'capacity'    => 'integer|min:0'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		$this->currency = Helper::currency($this->currency);
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details');
	}

	public function tickets()
	{
		return $this->belongsToMany('Ticket')->withPivot('quantity');
	}
}
