<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class Equipment extends Ardent {

	protected $guarded = array('id', 'created_at', 'updated_at');

	public static $rules = array(
		'uuid'        => '',
        'service_date'=> '',
		'size'        => 'required'
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
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}
    
    public function equipmentCategory()
	{
		return $this->hasOne('\Scubawhere\Entities\EquipmentCategory');
	}

}
