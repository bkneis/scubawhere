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
			$this->schedule = json_encode($this->schedule);
			$this->schedule = Helper::sanitiseString($this->schedule);
		}
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function sessions()
	{
		return $this->hasMany('Session');
	}
}
