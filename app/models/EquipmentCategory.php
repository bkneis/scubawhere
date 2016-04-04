<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class EquipmentCategory extends Ardent {

	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at');

	public static $rules = array(
		'name'        => 'required|max:64',
		'description' => ''
	);

	/*public function beforeSave( $forced )
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);
	}*/

	public function company()
	{
		return $this->belongsTo('Company');
	}
    
    public function equipment()
	{
		return $this->hasMany('Equipment', 'category_id');
	}

	public function prices()
	{
		return $this->hasMany('EquipmentPrice', 'category_id');
	}

}