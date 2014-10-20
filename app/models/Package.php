<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
use PhilipBrown\Money\Currency;

class Package extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'price', 'currency', 'capacity');

	protected $appends = array('decimal_price', 'has_bookings', 'trashed');

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

	public function getHasBookingsAttribute()
	{
		return $this->bookingdetails()->count() > 0;
	}

	public function getTrashedAttribute()
	{
		return $this->trashed();
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	/* public function bookings()
	{
		return $this->belongsToMany('Booking', 'booking_details')
			->withPivot('ticket_id', 'customer_id', 'is_lead', 'session_id')
			->withTimestamps();
	}*/

	public function packagefacades()
	{
		return $this->hasMany('Packagefacade')->withTimestamps();
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
