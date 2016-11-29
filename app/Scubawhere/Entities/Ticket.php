<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Ticket extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at', 'deleted_at');

	protected $hidden = array('parent_id');

	public static $rules = array(
		'name'                => 'required',
		'description'         => '',
		'only_packaged'       => 'boolean',
		'parent_id'           => 'integer|min:1',
		'available_from'      => 'date',
		'available_until'     => 'date',
		'available_for_from'  => 'date',
		'available_for_until' => 'date'
	);

    public $appends = array('deleteable');

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);
    }

    public function getDeleteableAttribute()
    {
        return !($this->packages()->exists() || $this->courses()->exists());
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
		     ->where(Price::$owner_type_column_name, 'Scubawhere\Entities\Ticket')
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

	public function bookings()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Booking', 'booking_details')
			->withPivot('session_id', 'package_id', 'customer_id', 'is_lead')
			->withTimestamps();
	}

	public function bookingdetails()
	{
		return $this->hasMany('\Scubawhere\Entities\Bookingdetail');
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function trips()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Trip')->withTimestamps();
	}

	public function boats()
	{
		return $this->morphedByMany('\Scubawhere\Entities\Boat', 'ticketable')->withTimestamps();
	}

	public function boatrooms()
	{
		return $this->morphedByMany('\Scubawhere\Entities\Boatroom', 'ticketable')->withTimestamps();
	}

	public function courses()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Course')->withPivot('quantity')->withTimestamps();
	}

	public function packages()
	{
		return $this->morphToMany('\Scubawhere\Entities\Package', 'packageable')->withPivot('quantity')->withTimestamps();
	}

	public function basePrices()
	{
		return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNull('until');
	}

	public function prices()
	{
		return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNotNull('until');
	}

}
