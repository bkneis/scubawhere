<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Package extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until');

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
		$value = trim($value);
		$this->attributes['available_from'] = $value ?: null;
	}

	public function setAvailableUntilAttribute($value)
	{
		$value = trim($value);
		$this->attributes['available_until'] = $value ?: null;
	}

	public function setAvailableForFromAttribute($value)
	{
		$value = trim($value);
		$this->attributes['available_for_from'] = $value ?: null;
	}

	public function setAvailableForUntilAttribute($value)
	{
		$value = trim($value);
		$this->attributes['available_for_until'] = $value ?: null;
	}

	public function calculatePrice($start, $limitBefore = false) {
		$price = Price::where(Price::$owner_id_column_name, $this->id)
		     ->where(Price::$owner_type_column_name, 'Scubawhere\Entities\Package')
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
			 ->withTrashed()
		     ->first();

		$this->decimal_price = $price->decimal_price;
	}

	public function scopeOnlyOwners($query) 
	{
		return $query->where('company_id', '=', Context::get()->id);
	}

	public function accommodations()
	{
        return $this->morphedByMany('\Scubawhere\Entities\Accommodation', 'packageable')
                    ->withPivot('quantity')
                    //->withTrashed()
                    ->withTimestamps();
	}

	public function addons()
	{
		return $this->morphedByMany('\Scubawhere\Entities\Addon', 'packageable')->withPivot('quantity')->withTimestamps();
	}

	public function bookingdetails()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Bookingdetail', '\Scubawhere\Entities\Packagefacade');
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function courses()
	{
        return $this->morphedByMany('\Scubawhere\Entities\Course', 'packageable')
                    ->withPivot('quantity')
                    ->withTimestamps();
	}

	public function packagefacades()
	{
		return $this->hasMany('\Scubawhere\Entities\Packagefacade');
	}

	public function basePrices()
	{
		return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNull('until');
	}

	public function prices()
	{
		return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNotNull('until');
	}

	public function tickets()
	{
		return $this->morphedByMany('\Scubawhere\Entities\Ticket', 'packageable')->withPivot('quantity')->withTimestamps();
	}
}
