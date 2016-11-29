<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class EquipmentPrice extends Ardent {

	protected $guarded = array('id', 'category_id');

	public static $rules = array(
		'duration'        => 'required',
		'price'           => 'required'
	);

	/*public function beforeSave( $forced )
	{
		if( isset($this->duration) )
			$this->description = Helper::sanitiseBasicTags($this->duration);

		if( isset($this->price) )
			$this->name = Helper::sanitiseString($this->price);
	}*/

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}
    
    public function category()
	{
		return $this->belongsTo('\Scubawhere\Entities\EquipmentCategory');
	}

}
