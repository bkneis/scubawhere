<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Course extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'capacity', 'training_id', 'training_quantity');

	public static $rules = array(
		'name'              => 'required',
		'description'       => '',
		'capacity'          => 'integer|min:0',
		'training_id'       => 'required|integer',
		'training_quantity' => 'required|integer|min:1',
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);
	}

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail');
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function packages()
	{
		return $this->belongsToMany('Package')->withPivot('quantity')->withTimestamps();
	}

	public function basePrices()
	{
		return $this->morphMany('Price', 'owner')->whereNull('until');
	}

	public function prices()
	{
		return $this->morphMany('Price', 'owner')->whereNotNull('until');
	}

	public function tickets()
	{
		return $this->belongsToMany('Ticket')->withPivot('quantity')->withTimestamps();
	}

	public function training()
	{
		return $this->belongsTo('Training');
	}

	public function training_sessions()
	{
		return $this->hasMany('TrainingSession');
	}
}
