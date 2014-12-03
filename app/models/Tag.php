<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Tag extends Ardent {
	protected $guarded = array('*');
	protected $fillable = array();
	protected $hidden = array('created_at', 'updated_at');

	public static $rules = array();

	public function beforeSave()
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);
	}

	public function trips()
	{
		return $this->morphedByMany('Trip', 'taggable')->withTimestamps();
	}

	public function locations()
	{
		return $this->morphedByMany('Location', 'taggable')->withTimestamps();
	}
}
