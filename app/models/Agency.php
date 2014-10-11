<?php

use LaravelBook\Ardent\Ardent;

class Agency extends Ardent {
	protected $guarded = array('*');

	protected $fillable = array();

	protected $hidden = array('created_at', 'updated_at');

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
