<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class Continent extends Ardent {
	protected $hidden = array('created_at', 'updated_at');

	public static $rules = array();

	public function beforeSave()
	{
		if( isset($this->abbreviation) )
			$this->abbreviation = Helper::sanitiseBasicTags($this->abbreviation);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);
	}

	public function countries()
	{
		return $this->hasMany('\Scubawhere\Entities\Country');
	}

	public function regions()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Region', '\Scubawhere\Entities\Country');
	}

	public function companies()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Company', '\Scubawhere\Entities\Country');
	}

	public function customers()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Customer', '\Scubawhere\Entities\Country');
	}
}
