<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Country extends Ardent {
	protected $hidden = array('created_at', 'updated_at');

	public static $rules = array();

	public function beforeSave()
	{
		if( isset($this->abbreviation) )
			$this->abbreviation = Helper::sanitiseBasicTags($this->abbreviation);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->flag) )
			$this->flag = Helper::sanitiseString($this->flag);
	}

	public function continent()
	{
		return $this->belongsTo('Continent');
	}

	public function companies()
	{
		return $this->hasMany('Company');
	}

	public function customers()
	{
		return $this->hasMany('Customer');
	}

	public function currency()
	{
		return $this->belongsTo('Currency');
	}
}
