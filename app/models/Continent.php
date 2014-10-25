<?php

use LaravelBook\Ardent\Ardent;

class Continent extends Ardent {
	protected $guarded = array('*');
	protected $fillable = array();
	protected $hidden = array('created_at', 'updated_at');

	public static $rules = array();

	public function countries()
	{
		return $this->hasMany('Country');
	}

	public function regions()
	{
		return $this->hasManyThrough('Region', 'Country');
	}

	public function companies()
	{
		return $this->hasManyThrough('Company', 'Country');
	}

	public function customers()
	{
		return $this->hasManyThrough('Customer', 'Country');
	}
}
