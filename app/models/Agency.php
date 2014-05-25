<?php

use LaravelBook\Ardent\Ardent;

class Agency extends Ardent {
	protected $guarded = array();
	protected $fillable = array(); // Not edible

	public static $rules = array();

	public function certificates()
	{
		return $this->hasMany('Certificate');
	}

	public function customers()
	{
		return $this->hasMany('Customer');
	}
}
