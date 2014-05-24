<?php

use LaravelBook\Ardent\Ardent;

class Triptype extends Ardent {
	protected $guarded = array('id', 'created_at', 'updated_at');

	public static $rules = array();

	public function beforeSave()
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);
	}

	public function trips()
	{
		return $this->belongsToMany('Trip');
	}
}
