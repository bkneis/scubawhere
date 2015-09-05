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
		'reserved_until',
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
		'reserved_until'   => 'date',
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
		if(empty($this->bookingdetails))
			$this->load('bookingdetails', 'bookingdetails.addons');

		foreach($this->bookingdetails as $detail) {
			foreach($detail->addons as $addon) {
				if($addon->compulsory === 1)
					$feeSum += floatval($addon->decimal_price) * $addon->pivot->quantity;
			}
		}

		return floatval($this->decimal_price) - $feeSum;
	}

	public function getArrivalDateAttribute() {
		$earliestDeparture = null;
		$earliestClass = null;
		$earliestAccommodation = null;

		$model = $this->departures()->orderBy('sessions.start', 'ASC')->first(array('sessions.*'));
		if(!empty($model))
			$earliestDeparture = new DateTime($model->start);

		$model = $this->training_sessions()->orderBy('training_sessions.start', 'ASC')->first(array('training_sessions.*'));
		if(!empty($model))
			$earliestClass = new DateTime($model->start);

		$model = $this->accommodations()->orderBy('accommodation_booking.start', 'ASC')->first();
		if(!empty($model))
			$earliestAccommodation = new DateTime($model->pivot->start);

		$dates = [$earliestDeparture, $earliestClass, $earliestAccommodation];
		$dates = array_filter($dates);
		sort($dates);

		$result = $dates[0];

		if(!empty($result))
			return $result->format('Y-m-d');
		else
			return null;
	}

	public function getCreatedAtLocalAttribute() {
		$datetime = new DateTime( $this->created_at, new DateTimeZone('UTC') );
		$datetime->setTimezone( new DateTimeZone( Auth::user()->timezone ) );

		return $datetime->format('Y-m-d H:i:s');
	}

	public function getLastReturnDateAttribute() {
		$lastTripReturn = null;
		$lastClassReturn = null;
		$lastAccommodationEnd = null;

		$departures = $this->departures()->with('trip')->get();
		if(count($departures) > 0)
		{
			$departures->sortByDesc(function($departure)
			{
				$trip             = $departure->trip;
				$start            = new DateTime($departure->start);
				$end              = clone $start;
				$duration_hours   = floor($trip->duration);
				$duration_minutes = round( ($trip->duration - $duration_hours) * 60 );
				$end->add( new DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );

				$departure->end = $end;

				return $end->format('Y-m-d H:i:s');
			});
			$lastTripReturn = $departures->first()->end;
		}

		$training_sessions = $this->training_sessions()->with('training')->get();
		if(count($training_sessions) > 0)
		{
			$training_sessions->sortByDesc(function($training_session)
			{
				$class            = $training_session->training;
				$start            = new DateTime($training_session->start);
				$end              = clone $start;
				$duration_hours   = floor($class->duration);
				$duration_minutes = round( ($class->duration - $duration_hours) * 60 );
				$end->add( new DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );

				$training_session->end = $end;

				return $end->format('Y-m-d H:i:s');
			});
			$lastClassReturn = $training_sessions->first()->end;
		}

		$model = $this->accommodations()->orderBy('accommodation_booking.end', 'DESC')->first();
		if(!empty($model))
			$lastAccommodationEnd = new DateTime($model->pivot->start);

		$dates = [$lastTripReturn, $lastClassReturn, $lastAccommodationEnd];
		$dates = array_filter($dates);
		sort($dates);

		$result = array_pop($dates);

		if(!empty($result))
			return $result->format('Y-m-d');
		else
			return null;
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
		return $this->belongsToMany('Customer', 'booking_details')->withPivot('ticket_id', 'session_id', 'boatroom_id', 'packagefacade_id', 'course_id', 'training_session_id')->withTimestamps();
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

	public function training_sessions()
	{
		return $this->belongsToMany('TrainingSession', 'booking_details', 'booking_id', 'training_session_id')->withTimestamps();
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

	public function updatePrice($onlyApplyDiscount = false, $oldDiscount = 0)
	{
		$currency = new Currency( Auth::user()->currency->code );
		$tickedOffPackagefacades = [];
		$tickedOffCourses = [];
		$sum = 0;

		$sum -= $this->discount;

		if($onlyApplyDiscount) {
			$sum += $this->price / $currency->getSubunitToUnit();

			$sum += $oldDiscount;

			$this->price = (int) round( $sum * $currency->getSubunitToUnit() );

			$this->save();

			$this->decimal_price = $sum;

			return true;
		}

		$bookingdetails = $this->bookingdetails()->with('ticket', 'departure', 'addons', 'packagefacade', 'packagefacade.package')->get();

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
					$bookingdetails = $detail->packagefacade->bookingdetails()->with('departure', 'training_session')->get();
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

					$accommodations = $this->accommodations()->wherePivot('packagefacade_id', $detail->packagefacade_id)->get();
					$firstAccommodation = $accommodations->sortBy(function($accommodation)
					{
						return $accommodation->pivot->start;
					})->first();

					if(!empty($firstAccommodation))
					{
						$detailStart = new DateTime($start);
						$accommStart = new DateTime($firstAccommodation->pivot->start);

						$start = ($detailStart < $accommStart) ? $detailStart : $accommStart;

						$start = $start->format('Y-m-d H:i:s');
					}

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

		$accommodations->each(function($accommodation) use (&$sum, &$tickedOffPackagefacades)
		{
			$limitBefore = in_array($this->status, ['reserved', 'expired', 'confirmed']) ? $accommodation->pivot->created_at : false;

			if(empty($accommodation->pivot->packagefacade_id))
			{
				$accommodation->calculatePrice($accommodation->pivot->start, $accommodation->pivot->end, $limitBefore);
				$sum += $accommodation->decimal_price;
			}
			elseif(!in_array($accommodation->pivot->packagefacade_id, $tickedOffPackagefacades)) {
				// Add the packagefacadeID to the array so it is not summed/counted again in the next bookingdetails
				$tickedOffPackagefacades[] = $accommodation->pivot->packagefacade_id;

				$accommodations = $this->accommodations()->wherePivot('packagefacade_id', $accommodation->pivot->packagefacade_id)->get();
				$firstAccommodation = $accommodations->sortBy(function($accommodation)
				{
					return $accommodation->pivot->start;
				})->first();

				// Calculate the package price at this first departure datetime and sum it up
				$packagefacade = Packagefacade::find($accommodation->pivot->packagefacade_id);
				$packagefacade->package->calculatePrice($firstAccommodation->pivot->start, $limitBefore);
				$sum += $packagefacade->package->decimal_price;
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

		$this->price = (int) round( $sum * $currency->getSubunitToUnit() );

		$this->save();

		$this->decimal_price = $sum;
	}
}
