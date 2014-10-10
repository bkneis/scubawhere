<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Addon extends Ardent {
	protected $guarded = array('id', 'created_at', 'updated_at');

	protected $fillable = array(
		'name',
		'description',
		'price',
		'currency',
		'compulsory'
	);

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'currency'    => 'required|alpha|size:3|valid_currency',
		'price'       => 'required|integer|min:0',
		'compulsory'  => 'required'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->price) )
			$this->price = Helper::sanitiseString($this->price);

		if( isset($this->compulsory) )
			$this->compulsory = Helper::sanitiseString($this->compulsory);
	}

	public function bookings()
	{
		return $this->hasMany('Booking');
	}

	public function company()
	{
		return $this->belongsTo('Company', 'Customer');
	}

	public function customers()
	{
		return $this->hasManyThrough('Customer', 'Booking');
	}
}
