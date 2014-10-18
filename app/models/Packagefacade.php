<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Packagefacade extends Ardent {
	protected $fillable = array('package_id');

	public static $rules = array(
		'package_id'  => 'required|integer'
	);

	public function beforeSave( $forced )
	{
		//
	}

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail');
	}

	public function package()
	{
		return $this->belongsTo('Package');
	}
}
