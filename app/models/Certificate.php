<?php

use LaravelBook\Ardent\Ardent;

class Certificate extends Ardent {
	protected $guarded = array('*');
	protected $fillable = array();
	protected $hidden = array('created_at', 'updated_at');

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
