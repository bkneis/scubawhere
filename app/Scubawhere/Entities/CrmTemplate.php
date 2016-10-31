<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

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

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

}
