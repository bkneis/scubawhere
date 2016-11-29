<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class CrmCampaign extends Ardent {
    
    use SoftDeletingTrait;

	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at');
    
    protected $dates = ['deleted_at'];

	public static $rules = array(
		'subject'        => 'required',
		'email_html' 	 => 'required',
		'num_sent'		 => '',
        'name'           => 'required',
        'sendallcustomers' => 'required'
	);

	public function beforeSave( $forced )
	{

		if( isset($this->subject) )
			$this->subject = Helper::sanitiseString($this->subject);

	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function groups()
	{
		return $this->belongsToMany('\Scubawhere\Entities\CrmGroup');
	}
    
    public function tokens() 
    {
        return $this->hasMany('\Scubawhere\Entities\CrmToken', 'campaign_id');
    }
    
    public function crmLinks()
    {
        return $this->hasMany('\Scubawhere\Entities\CrmLink', 'campaign_id');
    }

}
