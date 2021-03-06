<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class Paymentgateway extends Ardent {
	protected $guarded = array('*');
	protected $fillable = array();
	protected $hidden = array('created_at', 'updated_at');

	public static $rules = array();

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);
	}

	public function payments()
	{
		return $this->hasMany('\Scubawhere\Entities\Payment');
	}

	public function refunds()
	{
		return $this->hasMany('\Scubawhere\Entities\Refund');
	}

}
