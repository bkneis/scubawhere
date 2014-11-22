<?php

use LaravelBook\Ardent\Ardent;
use PhilipBrown\Money\Currency;

class Booking extends Ardent {
	protected $fillable = array(
		'agent_id',
		'source',
		// 'price',
		'discount',
		// 'confirmed',
		'reserved',
		'pick_up_location',
		'pick_up_time',
		'comment'
	);

	protected $appends = array('decimal_price');

	public static $rules = array(
		'agent_id'         => 'integer|exists:agents,id|required_without:source',
		'source'           => 'alpha|required_without:agent_id|in:telephone,email,facetoface'/*,frontend,widget,other'*/,
		'price'            => 'integer|min:0',
		'discount'         => 'integer|min:0',
		'confirmed'        => 'integer|in:0,1',
		'reserved'         => 'date|after:now',
		'pick_up_location' => '',
		'pick_up_time'     => 'date|after:now',
		'comment'          => ''
	);

	public function beforeSave()
	{
		if( isset($this->pick_up) )
			$this->pick_up = Helper::sanitiseString($this->pick_up);

		if( isset($this->drop_off) )
			$this->drop_off = Helper::sanitiseString($this->drop_off);

		if( isset($this->comments) )
			$this->comments = Helper::sanitiseString($this->comments);
	}

	public function getDecimalPriceAttribute()
	{
		$currency = new Currency( Auth::user()->currency->code );

		return number_format(
			($this->price - $this->discount) / $currency->getSubunitToUnit(), // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);
	}

	public function decimal_price()
	{
		// TODO Tombstone
		return $this->getDecimalPriceAttribute();
	}

	public function accommodations()
	{
		return $this->belongsToMany('Accommodation')->withTrashed()->withPivot('customer_id', 'start', 'end')->withTimestamps();
	}

	public function customers()
	{
		return $this->belongsToMany('Customer', 'booking_details')->withPivot('ticket_id', 'session_id', 'packagefacade_id', 'is_lead')->withTimestamps();
	}

	public function lead_customer()
	{
		return $this->belongsToMany('Customer', 'booking_details')->wherePivot('is_lead', 1)->withTimestamps();
	}

	/*public function addons()
	{
		return $this->hasManyThrough('Addon', 'Bookingdetail');
	}*/

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail');
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}

	public function agent()
	{
		return $this->belongsTo('Agent');
	}

	/*public function packages()
	{
		return $this->belongsToMany('Package', 'booking_details')->withPivot('customer_id', 'is_lead', 'ticket_id', 'session_id');
	}*/

	public function packagefacades()
	{
		return $this->belongsToMany('Packagefacade', 'booking_details');
	}

	public function tickets()
	{
		return $this->belongsToMany('Ticket', 'booking_details');
	}

	public function payments()
	{
		return $this->hasMany('Payment');
	}

	public function updatePrice()
	{
		// TODO Do this properly with currency check
		/*
		$packagesSum = 0;
		$this->packagefacades()->distinct()->with('package')->get()->each(function($packagefacade) use ($packagesSum)
		{
			$packagesSum += $packagefacade->package()->first()->price;
		});

		$ticketsSum  = $this->tickets()->wherePivot('packagefacade_id', null)->sum('price');

		// $addonSum    = $this->addons()->sum('price');

		$this->price = $packagesSum + $ticketsSum /*+ $addonSum*/;
		/*
		$this->save();
		*/
	}
}
