<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Trip extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $guarded = array('id', 'company_id', 'views', 'created_at', 'updated_at');

	protected $appends = array('deletable');

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

	public function getDeletableAttribute()
	{
		return !($this->tickets()->withTrashed()->count() > 0 || $this->departures()->withTrashed()->count() > 0 );
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
		return $this->belongsToMany('Location')->withTimestamps();
	}

	public function triptypes()
	{
		return $this->belongsToMany('Triptype')->withTimestamps();
	}

	public function tickets()
	{
		return $this->belongsToMany('Ticket');
	}

	public function departures()
	{
		return $this->hasMany('Departure');
	}
}
