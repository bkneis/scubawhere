<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class CrmGroupRule extends Ardent {

	protected $fillable = array('certificate_id', 'agency_id', 'ticket_id', 'training_id');

	/*public static $rules = array(
		'certificate_id'   => '',
		'agency_id'        => ''
	);*/

	public function group()
	{
		return $this->belongsTo('\Scubawhere\Entities\CrmGroup');
	}

}
