<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class CrmLinkTracker extends Ardent {

	protected $guarded = array('id', 'created_at', 'updated_at');

	public static $rules = array(
		'count'          => '',
		'token' 	     => 'required',
        'customer_id'    => 'required', 
        'link_id'        => 'required'
	);

	public function beforeSave( $forced )
	{
		if( isset($this->token) )
			$this->token = Helper::sanitiseString($this->token);
	}

	public function crmLink()
	{
		return $this->belongsTo('\Scubawhere\Entities\CrmLink');
	}

    public function customer()
    {
        return $this->belongsTo('\Scubawhere\Entities\Customer', 'customer_id');
    }

}
