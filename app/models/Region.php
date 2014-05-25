<?php

use LaravelBook\Ardent\Ardent;

class Region extends Ardent {
	protected $guarded = array();
	protected $fillable = array();

	public static $rules = array();

	public function country()
	{
		return $this->belongsTo('Country');
	}

	public function companies()
	{
		return $this->hasMany('Company');
	}

	public function customers()
	{
		return $this->hasMany('Customer');
	}
}
