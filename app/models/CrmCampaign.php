<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class CrmCampaign extends Ardent {

	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at');

	public static $rules = array(
		'subject'        => 'required',
		'message' 		 => 'required',
		'num_sent'		 => ''
	);

	public function beforeSave( $forced )
	{

		if( isset($this->subject) )
			$this->subject = Helper::sanitiseString($this->subject);

	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function groups()
	{
		return $this->belongsToMany('CrmGroup');
	}

}