<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Agency extends Ardent {
	protected $guarded = array('*');
	protected $fillable = array();
	protected $hidden = array('created_at', 'updated_at');

	public static $rules = array();

	public function beforeSave()
	{
		if( isset($this->abbreviation) )
			$this->abbreviation = Helper::sanitiseBasicTags($this->abbreviation);
		
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);
	}

	public function certificates()
	{
		return $this->hasMany('Certificate');
	}

	public function customers()
	{
		return $this->hasMany('Customer');
	}
}
