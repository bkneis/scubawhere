<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

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
		return $this->belongsTo('\Scubawhere\Entities\Continent');
	}

	public function companies()
	{
		return $this->hasMany('\Scubawhere\Entities\Company');
	}

	public function customers()
	{
		return $this->hasMany('\Scubawhere\Entities\Customer');
	}

	public function currency()
	{
		return $this->belongsTo('\Scubawhere\Entities\Currency');
	}
}
