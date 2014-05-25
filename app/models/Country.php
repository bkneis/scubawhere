<?php

use LaravelBook\Ardent\Ardent;

class Country extends Ardent {
	protected $guarded = array();
	protected $fillable = array();

	public static $rules = array();

	public function continent()
	{
		return $this->belongsTo('Continent');
	}

	public function regions()
	{
		return $this->hasMany('Region');
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
