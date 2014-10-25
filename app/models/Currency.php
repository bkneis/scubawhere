<?php

use LaravelBook\Ardent\Ardent;

class Currency extends Ardent {
	
	protected $table = 'currencies';	
	protected $guarded = array('*');
	protected $fillable = array();
	protected $hidden = array('created_at', 'updated_at');

	public static $rules = array();

	public function countries()
	{
		return $this->hasMany('Country');
	}
}
