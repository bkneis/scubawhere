<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Trip extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'duration', 'boat_required'/*, 'photo', 'video'*/);

	protected $appends = array('deletable');

	public static $rules = array(
		'name'          => 'required',
		'description'   => '',
		'duration'      => 'required|numeric',
		'boat_required' => 'boolean',
		'photo'         => '',
		'video'         => ''
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

		$this->duration = round($this->duration, 1);
	}

	public function getDeletableAttribute()
	{
		return !($this->tickets()->withTrashed()->exists() || $this->departures()->withTrashed()->exists());
	}

	public function scopeOnlyOwners($query) 
	{
		return $query->where('company_id', '=', Context::get()->id);			
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function locations()
	{
        return $this->belongsToMany('\Scubawhere\Entities\Location')
                    ->whereNull('location_trip.deleted_at')
                    ->withTimestamps();
	}

	public function tags()
	{
		return $this->morphToMany('\Scubawhere\Entities\Tag', 'taggable')->withTimestamps();
	}

	public function tickets()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Ticket');
	}

	public function departures()
	{
		return $this->hasMany('\Scubawhere\Entities\Departure');
	}

}
