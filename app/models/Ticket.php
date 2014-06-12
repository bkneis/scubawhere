<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
use PhilipBrown\Money\Currency;

class Ticket extends Ardent {
	protected $guarded = array('id', 'company_id', 'active', 'created_at', 'updated_at');

	protected $appends = array('decimal_price');

	public static $rules = array(
		'name'        => 'required',
		'description' => 'required',
		'price'       => 'required|integer|min:0',
		'currency'    => 'required|alpha|size:3|valid_currency'
	);

	public function beforeSave()
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

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

	public function trips()
	{
		return $this->belongsToMany('Trip')->withTimestamps();
	}

	public function boats()
	{
		return $this->belongsToMany('Boat')->withPivot('accommodation_id');
	}

	public function packages()
	{
		return $this->belongsToMany('Package')->withPivot('quantity');
	}

	public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details')->withPivot('session_id', 'package_id', 'customer_id', 'is_lead');
	}

}
