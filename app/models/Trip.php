<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Trip extends Ardent {
	protected $guarded = array('id', 'company_id', 'views', 'created_at', 'updated_at');

	public static $rules = array(
		'name'        => 'required',
		'description' => 'required',
		'duration'    => 'required|integer',
		'location_id' => 'integer|exists:locations,id',
		'photo'       => '',
		'video'       => ''
	);

	public function beforeSave()
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->photo) )
			$this->photo = Helper::sanitiseString($this->photo);

		if( isset($this->video) )
			$this->video = Helper::sanitiseString($this->video);
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function location()
	{
		return $this->belongsTo('Location');
	}

	public function locations()
	{
		return $this->belongsToMany('Location');
	}

	public function triptypes()
	{
		return $this->belongsToMany('Triptype');
	}

	public function tickets()
	{
		return $this->hasMany('Ticket');
	}

	public function departures()
	{
		return $this->hasMany('Departure');
	}
}
