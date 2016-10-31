<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class CrmGroup extends Ardent {

	protected $fillable = array('name', 'description');

	/*public static $rules = array(
		'name'        => 'required',
		'description' => ''
	);*/

	public function beforeSave( $forced )
	{

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function rules()
	{
		return $this->hasMany('\Scubawhere\Entities\CrmGroupRule');
	}

	public function campaigns()
	{
		return $this->hasMany('\Scubawhere\Entities\CrmCampaign');
	}

}
