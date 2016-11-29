<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class CrmLink extends Ardent {

	protected $guarded = array('id', 'created_at', 'updated_at');

	public static $rules = array(
		'link'        => 'required',
        'campaign_id' => 'required'
	);

	public function campaign()
	{
		return $this->belongsTo('\Scubawhere\Entities\Campaign');
	}
    
    public function analytics()
    {
        return $this->hasMany('\Scubawhere\Entities\CrmLinkTracker', 'link_id');
    }

}
