<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Training extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'duration');

	protected $appends = array('deletable');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'duration'    => 'required|numeric|min:0',
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		$this->duration = round($this->duration, 1);
	}

	public function getDeletableAttribute()
	{
		// TODO Only deny when future training_sessions are affected
		return !($this->courses()->exists() || $this->training_sessions()->exists());
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function courses()
	{
		return $this->belongsToMany('Course')->withPivot('quantity')->withTimestamps();
	}

	public function training_sessions()
	{
		return $this->hasMany('TrainingSession');
	}
}