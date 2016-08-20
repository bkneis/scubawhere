<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

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
		     ->where(Price::$owner_type_column_name, 'Ticket')
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

	public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details')
			->withPivot('session_id', 'package_id', 'customer_id', 'is_lead')
			->withTimestamps();
	}

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail');
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function trips()
	{
		return $this->belongsToMany('Trip')->withTimestamps();
	}

	public function boats()
	{
		return $this->morphedByMany('Boat', 'ticketable')->withTimestamps();
	}

	public function boatrooms()
	{
		return $this->morphedByMany('Boatroom', 'ticketable')->withTimestamps();
	}

	public function courses()
	{
		return $this->belongsToMany('Course')->withPivot('quantity')->withTimestamps();
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

}
