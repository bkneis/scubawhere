<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Boatroom extends Ardent {
	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at');

	public static $rules = array(
		'name'        => 'required|max:64',
		'description' => ''
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

	public function boats()
	{
		return $this->belongsToMany('Boat')->withPivot('capacity')->withTimestamps();
	}

	public function tickets()
	{
		return $this->belongsToMany('Ticket', 'boat_ticket')->withTimestamps();
	}
}
