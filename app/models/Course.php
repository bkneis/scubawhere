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

	public function calculatePrice($start, $limitBefore = false)
	{
		$price = Price::where(Price::$owner_id_column_name, $this->id)
		     ->where(Price::$owner_type_column_name, 'Course')
		     ->where('from', '<=', $start)
		     ->where(function($query) use ($start)
		     {
		     	$query->whereNull('until')
		     	      ->orWhere('until', '>=', $start);
		     })
		     ->where(function($query) use ($limitBefore)
		     {
		     	if($limitBefore)
		     		$query->where('created_at', '<=', $limitBefore);
		     })
		     ->orderBy('id', 'DESC')
		     ->first();

		$this->decimal_price = $price->decimal_price;
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
		return $this->morphToMany('Package', 'packageable')->withPivot('quantity')->withTimestamps();
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
}
