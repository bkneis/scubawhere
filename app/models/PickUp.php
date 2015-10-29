<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class PickUp extends Ardent {
	protected $guarded = array('id', 'created_at', 'updated_at');

	public static $rules = array(
		'location'   => 'required',
		'date'       => 'required|date',
		'time'       => 'required|time',
	);

	public function beforeSave( $forced )
	{
		if( isset($this->location) )
			$this->location = Helper::sanitiseString($this->location);
	}

	public function booking()
	{
		return $this->belongsTo('Booking');
	}
}
