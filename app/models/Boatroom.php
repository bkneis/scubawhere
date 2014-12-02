<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Boatroom extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description');

	public static $rules = array(
		'name'        => 'required|max:64',
		'description' => '',
		'photo'       => '',
	);

	public function beforeSave( $forced )
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->photo) )
			$this->photo = Helper::sanitiseString($this->photo);
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function boats()
	{
		return $this->belongsToMany('Boat')->withPivot('capacity')->withTimestamps();
	}

	public function tickets()
	{
		return $this->belongsToMany('Ticket', 'boat_ticket')->withTimestamps();
	}

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail');
	}
}
