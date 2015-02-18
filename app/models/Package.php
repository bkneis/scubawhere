<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Package extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'capacity', 'parent_id');

	protected $appends = array('has_bookings');

	protected $hidden = array('parent_id');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'capacity'    => 'integer|min:0',
		'parent_id'   => 'integer|min:1'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);
	}

	public function getHasBookingsAttribute()
	{
		return $this->bookingdetails()
		    ->whereHas('booking', function($query)
		    {
		    	$query->where('status', 'confirmed')->orWhereNotNull('reserved');
		    })
		    ->count() > 0;
	}

	public function calculatePrice($start) {
		$price = Price::where(Price::$owner_id_column_name, $this->id)
		     ->where(Price::$owner_type_column_name, 'Package')
		     ->where('from', '<=', $start)
		     ->where(function($query) use ($start)
		     {
		     	$query->whereNull('until')
		     	      ->orWhere('until', '>=', $start);
		     })
		     ->orderBy('id', 'DESC')
		     ->first();

		$this->decimal_price = $price->decimal_price;
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function packagefacades()
	{
		return $this->hasMany('Packagefacade')->withTimestamps();
	}

	public function basePrices()
	{
		return $this->morphMany('Price', 'owner')->whereNull('until');
	}

	public function prices()
	{
		return $this->morphMany('Price', 'owner')->whereNotNull('until');
	}

	public function bookingdetails()
	{
		return $this->hasManyThrough('Bookingdetail', 'Packagefacade');
	}

	public function tickets()
	{
		return $this->belongsToMany('Ticket')->withPivot('quantity')->withTimestamps();
	}
}
