<?php

use ScubaWhere\Helper;
use LaravelBook\Ardent\Ardent;

class Location extends Ardent {
	protected $guarded = array('id', 'created_at', 'updated_at');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'latitude'    => 'required|numeric|between:-90,90',
		'longitude'   => 'required|numeric|between:-180,180',
	);

    public $appends = array('deleteable');

	public function beforeSave()
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);
	}

    public function getDeleteableAttribute()
    {
        return !($this->trips()->exists()); 
    }

	public function companies()
	{
		return $this->belongsToMany('Company')->withPivot('description')->withTimestamps();
	}

	public function tags()
	{
		return $this->morphToMany('Tag', 'taggable')->withTimestamps();
    }

    public function trips()
    {
        return $this->belongsToMany('Trip')
                    ->whereNull('location_trip.deleted_at')
                    ->withTimestamps();
    }
}
