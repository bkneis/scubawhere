<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Location extends Ardent {
	protected $guarded = array('id', 'created_at', 'updated_at');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'latitude'    => 'required|numeric|between:-90,90',
		'longitude'   => 'required|numeric|between:-180,180',
	);

	public function beforeSave()
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);
	}

	public function companies()
	{
		return $this->belongsToMany('Company')->withTimestamps();
	}

	public function tags()
	{
		return $this->morphToMany('Tag', 'taggable')->withTimestamps();
	}
}
