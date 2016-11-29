<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class CrmSubscription extends Ardent {

	protected $fillable = array('id', 'company_id', 'customer_id', 'token', 'subscribed', 'company_id');

	public static $rules = array(
        'token'             => 'required'
	);

	public function beforeSave( $forced )
	{

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}
    
    public function customer()
    {
        return $this->belongsTo('\Scubawhere\Entities\Customer');
    }

}
