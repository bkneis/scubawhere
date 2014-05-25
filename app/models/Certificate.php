<?php

use LaravelBook\Ardent\Ardent;

class Certificate extends Ardent {
	protected $guarded = array();
	protected $fillable = array(); // Not edible

	public static $rules = array();

	public function agency()
	{
		return $this->belongsTo('Agency');
	}

	public function customers()
	{
		return $this->hasMany('Customer');
	}
}
