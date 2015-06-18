<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Package extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'parent_id');

	protected $hidden = array('parent_id');

	public static $rules = array(
		'name'                => 'required',
		'description'         => '',
		'parent_id'           => 'integer|min:1',
		'available_from'      => 'date',
		'available_until'     => 'date',
		'available_for_from'  => 'date',
		'available_for_until' => 'date'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);
	}

	public function setAvailableFromAttribute($value)
	{
		if($value === '') $this->attributes['available_from'] = null;
	}

	public function setAvailableUntilAttribute($value)
	{
		if($value === '') $this->attributes['available_until'] = null;
	}

	public function setAvailableForFromAttribute($value)
	{
		if($value === '') $this->attributes['available_for_from'] = null;
	}

	public function setAvailableForUntilAttribute($value)
	{
		if($value === '') $this->attributes['available_for_until'] = null;
	}

	public function calculatePrice($start, $limitBefore = false) {
		$price = Price::where(Price::$owner_id_column_name, $this->id)
		     ->where(Price::$owner_type_column_name, 'Package')
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

	public function accommodations()
	{
		return $this->morphedByMany('Accommodation', 'packageable')->withPivot('quantity')->withTimestamps();
	}

	public function addons()
	{
		return $this->morphedByMany('Addon', 'packageable')->withPivot('quantity')->withTimestamps();
	}

	public function bookingdetails()
	{
		return $this->hasManyThrough('Bookingdetail', 'Packagefacade');
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function courses()
	{
		return $this->morphedByMany('Course', 'packageable')->withPivot('quantity')->withTimestamps();
	}

	public function packagefacades()
	{
		return $this->hasMany('Packagefacade');
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
		return $this->morphedByMany('Ticket', 'packageable')->withPivot('quantity')->withTimestamps();
	}
}
