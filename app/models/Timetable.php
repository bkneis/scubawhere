<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Timetable extends Ardent {
	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at');

	public static $rules = array(
		'weeks'    => 'integer|min:1',
		'schedule' => 'required|valid_json'
	);

	public function beforeSave()
	{
		if( isset($this->schedule) )
		{
			// JSON encoding is already done in the controller (due to the JSON having to be validated before save)
			// $this->schedule = json_encode($this->schedule);

			/*
			 * The risk of corrupted data being saved and evaluated is estimated to be very low.
			 * A such, the sanitisation can be skipped for nicer formatting and easier evaluation
			 * on the editing side.
			 */
			// $this->schedule = Helper::sanitiseString($this->schedule);
		}
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function departures()
	{
		return $this->hasMany('Departure');
	}
}
