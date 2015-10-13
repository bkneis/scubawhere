<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

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
		return $this->belongsTo('Company');
	}

	public function rules()
	{
		return $this->hasMany('CrmGroupRule');
	}

	public function campaigns()
	{
		return $this->hasMany('CrmCampaign');
	}

}
