<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
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
		return $this->belongsTo('Company');
	}

	public function groups()
	{
		return $this->belongsToMany('CrmGroup');
	}
    
    public function tokens() 
    {
        return $this->hasMany('CrmToken', 'campaign_id');
    }
    
    public function crmLinks()
    {
        return $this->hasMany('CrmLink', 'campaign_id');
    }

}
