<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Boat extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at');

	public static $rules = array(
		'name'        => 'required|max:64',
		'description' => '',
		'capacity'    => 'required|integer'
	);

	public function beforeSave( $forced )
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->photo) )
			$this->photo = Helper::sanitiseString($this->photo);
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function boatrooms()
	{
		return $this->belongsToMany('Boatroom')->withPivot('capacity')->withTimestamps();
	}

	public function tickets()
	{
		return $this->belongsToMany('Ticket')->withPivot('boatroom_id')->withTimestamps();
	}

	public function departures()
	{
		return $this->hasMany('Departure');
	}
}
