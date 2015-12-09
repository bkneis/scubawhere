<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class CrmToken extends Ardent {

	protected $guarded = array('created_at', 'updated_at');

	public static $rules = array(
		'campaign_id'    => 'required',
		'token' 		 => 'required',
        'customer_id'    => 'required',
        'opened_time'    => '',
        'opened'         => ''
	);

	public function beforeSave( $forced )
	{
		if( isset($this->token) )
			$this->token = Helper::sanitiseString($this->token);
	}

	public function campaign()
	{
		return $this->belongsTo('CrmCampaign');
	}
    
    public function customer()
    {
        return $this->belongsTo('Customer', 'customer_id');
    }
    
}
