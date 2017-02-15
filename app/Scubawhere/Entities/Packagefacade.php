<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Helper;

class Packagefacade extends Ardent {
	protected $fillable = array('package_id', 'commissionable');

	public static $rules = array(
		'package_id'  => 'required|integer',
		'commissionable' => 'boolean'
	);

	public function beforeSave( $forced )
	{
		//
	}

	public function bookingdetails()
	{
		return $this->hasMany('\Scubawhere\Entities\Bookingdetail');
	}

	public function package()
	{
		return $this->belongsTo('\Scubawhere\Entities\Package')->withTrashed();
	}
}
