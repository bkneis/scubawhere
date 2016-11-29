<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class PickUp extends Ardent {
	protected $guarded = array('id', 'created_at', 'updated_at');

	public static $rules = array(
		'location'   => 'required',
		'date'       => 'required|date',
		'time'       => 'required|time',
		'quantity'   => 'required|integer|min:1',
	);

	public function beforeSave( $forced )
	{
		if( isset($this->location) )
			$this->location = Helper::sanitiseString($this->location);
	}

	public function booking()
	{
		return $this->belongsTo('\Scubawhere\Entities\Booking');
	}
}
