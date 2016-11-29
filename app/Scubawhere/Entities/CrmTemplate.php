<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;

class CrmTemplate extends Ardent {

	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at');

	public static $rules = array(
		'html_string'	 => 'required',
        'name'           => 'required'
	);

	public function beforeSave( $forced ) // MAYBE REMOVE
	{
		if( isset($this->name) ) $this->name = Helper::sanitiseString($this->name);
	}

	public static function scopeOnlyOwners($query)
	{
		return $query->where('company_id', '=', Context::get()->id);
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

}
