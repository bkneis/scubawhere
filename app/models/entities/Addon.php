<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
use ScubaWhere\Context;

class Addon extends Ardent {

	use SoftDeletingTrait;
	
	protected $dates = ['deleted_at'];

	protected $fillable = array(
		'name',
		'description',
		'compulsory',
		'parent_id'
	);

	protected $hidden = array('parent_id');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'compulsory'  => 'required|boolean',
		'parent_id'   => 'integer|min:1'
	);

    protected $appends = array('deletable');

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->compulsory) )
			$this->compulsory = Helper::sanitiseString($this->compulsory);
	}

    public function getDeletableAttribute() 
    {
        return !($this->packages()->exists());
    }

	public function calculatePrice($start, $limitBefore = false) {
		$price = Price::where(Price::$owner_id_column_name, $this->id)
		     ->where(Price::$owner_type_column_name, 'Addon')
		     ->where('from', '<=', $start)
		     ->where(function($query) use ($limitBefore)
		     {
		     	if($limitBefore)
		     		$query->where('created_at', '<=', $limitBefore);
		     })
		     ->orderBy('from', 'DESC')
			 ->withTrashed()
		     ->first();

		$this->decimal_price = $price->decimal_price;
	}

	public function getHasBookingsAttribute()
	{
		return $this->bookingdetails()
		    ->whereHas('booking', function($query)
		    {
		    	$query->whereIn('status', Booking::$counted);
		    })
		    ->count() > 0;
	}

	public function getCurrencyAttribute()
	{
		return Context::get()->currency;
	}

	public function scopeOnlyOwners($query)
	{
		return $query->where('company_id', '=', Context::get()->id);
	}

	/*public function bookings()
	{
		return $this->belongsToMany('Bookingdetail')
					->join('bookings', 'booking_id', '=', 'bookings.id')
					->select('bookings.reference', 'bookings.created_at');
	}*/

	public function bookingdetails()
	{
		return $this->belongsToMany('Bookingdetail')->withPivot('quantity', 'packagefacade_id')->withTimestamps();
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function customers()
	{
		return $this->hasManyThrough('Customer', 'Bookingdetail');
	}

	public function packages()
	{
		return $this->morphToMany('Package', 'packageable')->withPivot('quantity')->withTimestamps();
	}

	public function basePrices()
	{
		return $this->morphMany('Price', 'owner')->whereNull('until')->orderBy('from');
	}
}
