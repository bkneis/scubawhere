<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class Currency extends Ardent {
	protected $hidden = array('created_at', 'updated_at');

	public static $rules = array();

	public function beforeSave()
	{
		if( isset($this->code) )
			$this->code = Helper::sanitiseBasicTags($this->code);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		/*
		Because this model is never actually used to create a currency, only in testing, it is probably safe to ignore sanitising the symbol.
		if( isset($this->symbol) )
			$this->symbol = Helper::sanitiseString($this->symbol);
		*/
	}

	public function countries()
	{
		return $this->hasMany('\Scubawhere\Entities\Country');
	}
}
