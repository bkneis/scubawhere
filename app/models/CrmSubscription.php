<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class CrmSubscription extends Ardent {

	protected $fillable = array('id', 'company_id', 'customer_id', 'token', 'subscribed', 'company_id');

	public static $rules = array(
		'customer_id'       => 'required',
        'token'             => 'required'
	);

	public function beforeSave( $forced )
	{

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

	}

	public function company()
	{
		return $this->belongsTo('Company');
	}
    
    public function customer()
    {
        return $this->belongsTo('Customer');
    }

}
