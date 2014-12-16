<?php

use LaravelBook\Ardent\Ardent;
use PhilipBrown\Money\Currency;
use ScubaWhere\Helper;

class Booking extends Ardent {
	protected $fillable = array(
		'lead_customer_id',
		'agent_id',
		'source',
		// 'price',
		'discount',
		// 'status',
		'reserved',
		'pick_up_location',
		'pick_up_date',
		'pick_up_time',
		'comment'
	);

	protected $appends = array('decimal_price', 'arrival_date');

	public static $rules = array(
		'lead_customer_id' => 'integer|min:1',
		'agent_id'         => 'integer|required_without:source',
		'source'           => 'alpha|required_without:agent_id|in:telephone,email,facetoface'/*,frontend,widget,other'*/,
		'price'            => 'integer|min:0',
		'discount'         => 'integer|min:0',
		'status'           => 'in:saved,reserved,confirmed,on hold,canceled',
		'reserved'         => 'date|after_local_now',
		'pick_up_location' => 'required_with:pick_up_time',
		'pick_up_date'     => 'date|after:-1 day|required_with:pick_up_time',
		'pick_up_time'     => 'time|required_with:pick_up_date',
		'comment'          => ''
	);

	public function beforeSave()
	{
		if( isset($this->pick_up_location) )
			$this->pick_up_location = Helper::sanitiseString($this->pick_up_location);

		if( isset($this->comments) )
			$this->comments = Helper::sanitiseString($this->comments);
	}

	public function getDecimalPriceAttribute()
	{
		$currency = new Currency( Auth::user()->currency->code );

		return number_format(
			$this->price / $currency->getSubunitToUnit() - $this->discount, // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);
	}

	public function getArrivalDateAttribute() {
		$earliestDeparture = $this->departures()->orderBy('sessions.start', 'ASC')->first(array('sessions.*'));
		if(!empty($earliestDeparture))
			$earliestDeparture = new DateTime($earliestDeparture->start, new DateTimeZone( Auth::user()->timezone ));

		$earliestAccommodation = $this->accommodations()->orderBy('accommodation_booking.start', 'ASC')->first();
		if(!empty($earliestAccommodation))
			$earliestAccommodation = new DateTime($earliestAccommodation->pivot->start, new DateTimeZone( Auth::user()->timezone ));

		// This is ugly!
		// TODO Make it more elegant
		if(empty($earliestDeparture) && empty($earliestAccommodation))
			return null;

		if(empty($earliestDeparture))
			return $earliestAccommodation->format('Y-m-d');

		if(empty($earliestAccommodation))
			return $earliestDeparture->format('Y-m-d');

		return $earliestAccommodation < $earliestDeparture ? $earliestAccommodation->format('Y-m-d') : $earliestDeparture->format('Y-m-d');
	}

	public function setDiscountAttribute($value)
	{
		$currency = new Currency( Auth::user()->currency->code );

		$this->attributes['discount'] = (int) round( $value * $currency->getSubunitToUnit() );
	}

	public function getDiscountAttribute($value)
	{
		$currency = new Currency( Auth::user()->currency->code );

		return number_format(
			$value / $currency->getSubunitToUnit(), // number
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
		return $this->belongsToMany('Accommodation')->withPivot('customer_id', 'start', 'end')->withTimestamps();
	}

	public function customers()
	{
		return $this->belongsToMany('Customer', 'booking_details')->withPivot('ticket_id', 'session_id', 'packagefacade_id')->withTimestamps();
	}

	public function lead_customer()
	{
		return $this->belongsTo('Customer', 'lead_customer_id');
	}

	/*public function addons()
	{
		return $this->hasManyThrough('Addon', 'Bookingdetail');
	}*/

	public function bookingdetails()
	{
		return $this->hasMany('Bookingdetail');
	}

	public function sessions()
	{
		return $this->belongsToMany('Departure', 'booking_details', 'booking_id', 'session_id')->withTimestamps();
	}

	public function departures()
	{
		return $this->belongsToMany('Departure', 'booking_details', 'booking_id', 'session_id')->withTimestamps();
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
		return $this->belongsToMany('Package', 'booking_details')->withPivot('customer_id', 'ticket_id', 'session_id');
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
		return $this->hasMany('Payment')/*->orderBy('created_at', 'DESC')*/;
	}

	public function updatePrice()
	{
		$currency = new Currency( Auth::user()->currency->code );

		$bookingdetails = $this->bookingdetails()->where('packagefacade_id', null)->with('ticket', 'session', 'addons')->get();
		$sum = 0;

		$bookingdetails->each(function($detail) use (&$sum, $currency)
		{
			if($detail->packagefacade_id != null)
			{
				// Skip packages for now
			}
			else
			{
				// Sum up all tickets
				$detail->ticket->calculatePrice($detail->session->start);
				$sum += $detail->ticket->decimal_price;

				// Sum all addons
				$detail->addons->each(function($addon) use (&$sum, $currency)
				{
					$sum += number_format(
						$addon->price * $addon->pivot->quantity / $currency->getSubunitToUnit(), // number
						strlen( $currency->getSubunitToUnit() ) - 1, // decimals
						/* $currency->getDecimalMark() */ '.', // decimal seperator
						/* $currency->getThousandsSeperator() */ ''
					);
				});
			}
		});

		// Sum all accommodations
		$accommodations = $this->accommodations;

		$accommodations->each(function($accommodation) use (&$sum)
		{
			$accommodation->calculatePrice($accommodation->pivot->start, $accommodation->pivot->end);
			$sum += $accommodation->decimal_price;
		});

		/*
		// TODO Do this properly with currency check
		$packagesSum = 0;
		$this->packagefacades()->distinct()->with('package')->get()->each(function($packagefacade) use ($packagesSum)
		{
			$packagesSum += $packagefacade->package()->first()->price;
		});
		*/

		$this->price = (int) round( $sum * $currency->getSubunitToUnit() );

		$this->save();

		$this->decimal_price = $sum;
	}
}
