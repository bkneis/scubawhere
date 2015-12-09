<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class CrmLink extends Ardent {

	protected $guarded = array('id', 'created_at', 'updated_at');

	public static $rules = array(
		'link'        => 'required',
        'campaign_id' => 'required'
	);

	public function campaign()
	{
		return $this->belongsTo('Campaign');
	}
    
    public function analytics()
    {
        return $this->hasMany('CrmLinkTracker', 'link_id');
    }

}
