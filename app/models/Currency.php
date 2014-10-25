<?php

use LaravelBook\Ardent\Ardent;

class Currency extends Ardent {
	
	protected $table = 'currencies';
	
	protected $guarded = array();
	protected $fillable = array();

	public static $rules = array();

	public function countries()
	{
		return $this->hasMany('Country');
	}
}
