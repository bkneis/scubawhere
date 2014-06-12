<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
use PhilipBrown\Money\Currency;

class Package extends Ardent {
	protected $fillable = array('name', 'description', 'price', 'currency', 'capacity');

	protected $appends = array('decimal_price');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'price'       => 'required|numeric|min:0',
		'currency'    => 'required|alpha|size:3|valid_currency',
		'capacity'    => 'integer|min:0'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		$this->currency = Helper::currency($this->currency);
	}

	public function getDecimalPriceAttribute()
	{
		$currency = new Currency( $this->currency );

		return number_format(
			$this->price / $currency->getSubunitToUnit(), // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details');
	}

	public function tickets()
	{
		return $this->belongsToMany('Ticket')->withPivot('quantity')->withTimestamps();
	}
}
