<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Currency extends Ardent {	
	protected $guarded = array('*');
	protected $fillable = array();
	protected $hidden = array('created_at', 'updated_at');

	public static $rules = array();

	public function beforeSave()
	{
		if( isset($this->code) )
			$this->code = Helper::sanitiseBasicTags($this->code);
		
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);
		
		if( isset($this->symbol) )
			$this->symbol = Helper::sanitiseString($this->symbol);
	}
	
	public function countries()
	{
		return $this->hasMany('Country');
	}
}
