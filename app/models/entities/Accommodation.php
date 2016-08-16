<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
use ScubaWhere\Context;

class Accommodation extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'capacity', 'parent_id');

	protected $hidden = array('parent_id');

    protected $appends = array('deletable');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'capacity'    => 'required|integer|min:1',
		'parent_id'   => 'integer|min:1'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);
    }

    public function getDeletableAttribute() 
    {
        return !($this->packages()->exists());
    }

	public function calculatePrice($start, $end, $limitBefore = false) {
		$current_date = gettype($start) === "object" ? $start : new DateTime($start, new DateTimeZone( Context::get()->timezone ));
		$end          = gettype($end)   === "object" ? $end :   new DateTime($end,   new DateTimeZone( Context::get()->timezone ));

		$totalPrice = 0;
		$numberOfDays = 0;

		// Find the price for each night
		do
		{
			$date = $current_date->format('Y-m-d');

			$totalPrice += Price::where(Price::$owner_id_column_name, $this->id)
				->where(Price::$owner_type_column_name, 'Accommodation')
				->where('from', '<=', $date)
				->where(function($query) use ($date)
				{
					$query->whereNull('until')
					      ->orWhere('until', '>=', $date);
				})
			     ->where(function($query) use ($limitBefore)
			     {
			     	if($limitBefore)
			     		$query->where('created_at', '<=', $limitBefore);
			     })
				->orderBy('id', 'DESC')
				->first()->decimal_price;

			$current_date->add( new DateInterval('P1D') );
			$numberOfDays++;
		}
		while( $current_date < $end );

		$this->decimal_price         = number_format($totalPrice, 2, '.', '');
		$this->decimal_price_per_day = number_format($totalPrice / $numberOfDays, 2, '.', '');
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking')->withPivot('customer_id', 'start', 'end', 'packagefacade_id')->withTimestamps();
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function customers()
	{
		return $this->belongsToMany('Customer', 'accommodation_booking')->withPivot('booking_id', 'start', 'end')->withTimestamps();
	}

	public function basePrices()
	{
		return $this->morphMany('Price', 'owner')->whereNull('until');
	}

	public function prices()
	{
		return $this->morphMany('Price', 'owner')->whereNotNull('until');
	}

	public function packages()
	{
		return $this->morphToMany('Package', 'packageable')->withPivot('quantity')->withTimestamps();
	}
}
