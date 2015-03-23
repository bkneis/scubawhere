<?php

use LaravelBook\Ardent\Ardent;
use PhilipBrown\Money\Currency;
use ScubaWhere\Helper;

class Booking extends Ardent {
	protected $fillable = array(
		'lead_customer_id',
		'agent_id',
		'agent_reference',
		'source',
		// 'price',
		'discount',
		'status',
		'reserved',
		'cancellation_fee',
		'pick_up_location',
		'pick_up_date',
		'pick_up_time',
		'comment'
	);

	protected $appends = array('decimal_price', 'real_decimal_price', 'arrival_date', 'created_at_local');

	public $loadTrashedAccommodations = true;

	public static $counted = array('initialised', 'reserved', 'confirmed');

	public static $rules = array(
		'lead_customer_id' => 'integer|min:1',
		'agent_id'         => 'integer|required_without:source',
		'agent_reference'  => 'required_with:agent_id',
		'source'           => 'alpha|required_without:agent_id|in:telephone,email,facetoface'/*,frontend,widget,other'*/,
		'price'            => 'integer|min:0',
		'discount'         => 'integer|min:0',
		'status'           => 'in:initialised,saved,reserved,expired,confirmed,on hold,cancelled',
		'reserved'         => 'date',
		'cancellation_fee' => 'integer|min:0',
		'pick_up_location' => 'required_with:pick_up_time',
		'pick_up_date'     => 'date|after:-1 day|required_with:pick_up_time',
		'pick_up_time'     => 'time|required_with:pick_up_date',
		'comment'          => ''
	);

	public function beforeSave()
	{
		if( isset($this->agent_reference) )
			$this->agent_reference = Helper::sanitiseString($this->agent_reference);

		if( isset($this->pick_up_location) )
			$this->pick_up_location = Helper::sanitiseString($this->pick_up_location);

		if( isset($this->comments) )
			$this->comments = Helper::sanitiseString($this->comments);
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

	public function getRealDecimalPriceAttribute()
	{
		// The real price is only of interest for bookings that have either a discount applied or are made by an agent
		// Thus, in all other cases, we can skip this costly calculation
		// However, in this case the real_decimal_price is still NOT the same as the decimal_price! That's why we return null instead (as it's not needed anyway)
		if((empty($this->discount) || $this->discount < 1) && empty($this->agent_id)) return null;

		$feeSum = 0;
		$this->load('bookingdetails', 'bookingdetails.addons');

		foreach ($this->bookingdetails as $detail) {
			foreach ($detail->addons as $addon) {
				if($addon->compulsory === 1)
					$feeSum += $addon->decimal_price;
			}
		}

		return $this->decimal_price - $feeSum;
	}

	public function getArrivalDateAttribute() {
		$earliestDeparture = $this->departures()->orderBy('sessions.start', 'ASC')->first(array('sessions.*'));
		if(!empty($earliestDeparture))
			$earliestDeparture = new DateTime($earliestDeparture->start);

		$earliestAccommodation = $this->accommodations()->orderBy('accommodation_booking.start', 'ASC')->first();
		if(!empty($earliestAccommodation))
			$earliestAccommodation = new DateTime($earliestAccommodation->pivot->start);

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

	public function getCreatedAtLocalAttribute() {
		$datetime = new DateTime( $this->created_at, new DateTimeZone('Europe/London') );
		$datetime->setTimezone( new DateTimeZone( Auth::user()->timezone ) );

		return $datetime->format('Y-m-d H:i:s');
	}

	public function getLastReturnDateAttribute() {
		$departures = $this->departures()->with('trip')->get();
		$departures->sortByDesc(function($departure)
		{
			$trip             = $departure->trip;
			$start            = new DateTime($trip->start);
			$end              = clone $start;
			$duration_hours   = floor($trip->duration);
			$duration_minutes = round( ($trip->duration - $duration_hours) * 60 );
			$end->add( new DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );

			$departure->end = $end;

			return $end->format('Y-m-d H:i:s');
		});
		$lastReturnDate = $departures->first()->end;

		$lastAccommodationDate = $this->accommodations()->orderBy('accommodation_booking.end', 'DESC')->first();
		if(!empty($lastAccommodationDate))
			$lastAccommodationDate = new DateTime($lastAccommodationDate->pivot->start);

		// This is ugly!
		// TODO Make it more elegant
		if(empty($lastReturnDate) && empty($lastAccommodationDate))
			return null;

		if(empty($lastReturnDate))
			return $lastAccommodationDate->format('Y-m-d');

		if(empty($lastAccommodationDate))
			return $lastReturnDate->format('Y-m-d');

		return $lastAccommodationDate > $lastReturnDate ? $lastAccommodationDate->format('Y-m-d') : $lastReturnDate->format('Y-m-d');
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

	public function setCancellationFeeAttribute($value)
	{
		$currency = new Currency( Auth::user()->currency->code );

		$this->attributes['cancellation_fee'] = (int) round( $value * $currency->getSubunitToUnit() );
	}

	public function getCancellationFeeAttribute($value)
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
		if($this->loadTrashedAccommodations)
			return $this->belongsToMany('Accommodation')->withPivot('customer_id', 'start', 'end', 'packagefacade_id')->withTimestamps()->withTrashed();

		return $this->belongsToMany('Accommodation')->withPivot('customer_id', 'start', 'end', 'packagefacade_id')->withTimestamps();
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

	public function refunds()
	{
		return $this->hasMany('Refund')/*->orderBy('created_at', 'DESC')*/;
	}

	public function isEditable()
	{
		return !($this->status === 'cancelled' || $this->status === 'on hold');
	}

	public function updatePrice()
	{
		$currency = new Currency( Auth::user()->currency->code );

		$bookingdetails = $this->bookingdetails()->with('ticket', 'departure', 'addons', 'packagefacade', 'packagefacade.package')->get();

		$sum = 0;
		$tickedOffPackagefacades = [];
		$tickedOffCourses = [];

		$bookingdetails->each(function($detail) use (&$sum, $currency, &$tickedOffPackagefacades, &$tickedOffCourses)
		{
			$limitBefore = in_array($this->status, ['reserved', 'expired', 'confirmed']) ? $detail->created_at : false;

			if($detail->packagefacade_id !== null)
			{
				if(!in_array($detail->packagefacade_id, $tickedOffPackagefacades))
				{
					// Add the packagefacadeID to the array so it is not summed/counted again in the next bookingdetails
					$tickedOffPackagefacades[] = $detail->packagefacade_id;

					// Find the first departure datetime that is booked in this package
					$bookingdetails = $detail->packagefacade->bookingdetails()->with('departure, training_session')->get();
					$firstDetail = $bookingdetails->sortBy(function($detail)
					{
						if($detail->departure)
							return $detail->departure->start;
						else
							return $detail->training_session->start;
					})->first();

					if($firstDetail->departure)
						$start = $firstDetail->departure->start;
					else
						$start = $firstDetail->training_session->start;

					// Calculate the package price at this first departure datetime and sum it up
					$detail->packagefacade->package->calculatePrice($start, $limitBefore);
					$sum += $detail->packagefacade->package->decimal_price;
				}
			}
			elseif($detail->course_id !== null)
			{
				$identifier = $detail->booking_id . '-' . $detail->customer_id . '-' . $detail->course_id;

				if(!in_array($identifier, $tickedOffCourses))
				{
					$tickedOffCourses[] = $identifier;

					// Find the first departure datetime that is booked in this package
					$bookingdetails = $detail->course->bookingdetails()->with('departure', 'training_session')->get();
					$firstDetail = $bookingdetails->sortBy(function($detail)
					{
						if($detail->departure)
							return $detail->departure->start;
						else
							return $detail->training_session->start;
					})->first();

					if($firstDetail->departure)
						$start = $firstDetail->departure->start;
					else
						$start = $firstDetail->training_session->start;

					// Calculate the package price at this first departure datetime and sum it up
					$detail->course->calculatePrice($start, $limitBefore);
					$sum += $detail->course->decimal_price;
				}
			}
			else
			{
				// Sum up the ticket
				$detail->ticket->calculatePrice($detail->departure->start, $limitBefore);
				$sum += $detail->ticket->decimal_price;
			}

			// Sum up all addons that are not part of a package
			$detail->addons->each(function($addon) use (&$sum, $currency)
			{
				if($addon->pivot->packagefacade_id === null)
					$sum += $addon->decimal_price * $addon->pivot->quantity;
			});
		});

		// Sum up all accommodations that are not part of a package
		$accommodations = $this->accommodations;

		$accommodations->each(function($accommodation) use (&$sum)
		{
			if(empty($accommodation->pivot->packagefacade_id))
			{
				$limitBefore = in_array($this->status, ['reserved', 'expired', 'confirmed']) ? $accommodation->pivot->created_at : false;


				$accommodation->calculatePrice($accommodation->pivot->start, $accommodation->pivot->end, $limitBefore);
				$sum += $accommodation->decimal_price;
			}
		});

		/*
		// TODO Do this properly with currency check
		$packagesSum = 0;
		$this->packagefacades()->distinct()->with('package')->get()->each(function($packagefacade) use ($packagesSum)
		{
			$packagesSum += $packagefacade->package()->first()->price;
		});
		*/

		$sum -= $this->discount;

		$this->price = (int) round( $sum * $currency->getSubunitToUnit() );

		$this->save();

		$this->decimal_price = $sum;
	}
}
