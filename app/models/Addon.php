<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
use PhilipBrown\Money\Currency;

class Addon extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $guarded = array('id', 'created_at', 'updated_at');

	protected $appends = array('decimal_price', 'currency', 'has_bookings', 'trashed');

	protected $fillable = array(
		'name',
		'description',
		'new_decimal_price',
		'price',
		'compulsory'
	);

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'price'       => 'sometimes|integer|min:0',
		'compulsory'  => 'required|boolean'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->new_decimal_price) )
		{
			$currency = new Currency( Auth::user()->currency->code );
			$this->price = (int) round( $this->new_decimal_price * $currency->getSubunitToUnit() );
			unset($this->new_decimal_price);
		}

		if( isset($this->compulsory) )
			$this->compulsory = Helper::sanitiseString($this->compulsory);
	}

	public function getDecimalPriceAttribute()
	{
		$currency = new Currency( Auth::user()->currency->code );

		return number_format(
			$this->price / $currency->getSubunitToUnit(), // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);
	}

	public function getHasBookingsAttribute()
	{
		return $this->bookingdetails()->count() > 0;
	}

	public function getTrashedAttribute()
	{
		return $this->trashed();
	}

	public function getCurrencyAttribute()
	{
		return Auth::user()->currency;
	}

	/* public function bookings()
	{
		return $this->hasManyThrough('Booking', 'Bookingdetail');
	} */

	public function bookingdetails()
	{
		return $this->belongsToMany('Bookingdetail')->withPivot('quantity')->withTimestamps();
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function customers()
	{
		return $this->hasManyThrough('Customer', 'Bookingdetail');
	}
}
