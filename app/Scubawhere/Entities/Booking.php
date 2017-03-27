<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use PhilipBrown\Money\Currency;

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
		'comment',
		'cancel_reason',
		'discount_reason',
		'cancelled_at',
		'commission_amount',
		'discount_percentage'
	);

	protected $appends = array('decimal_price', 'real_decimal_price', 'arrival_date', 'created_at_local', 'absolute_price');

	public $loadTrashedAccommodations = true;

	public static $counted = array('initialised', 'reserved', 'confirmed');

	public static $rules = array(
		'lead_customer_id' => 'integer|min:1',
		'agent_id'         => 'integer|required_without:source',
		'agent_reference'  => 'required_with:agent_id',
		'source'           => 'alpha|required_without:agent_id|in:telephone,email,facetoface'/*,frontend,widget,other'*/,
		'price'            => 'integer|min:0',
		'discount'         => 'integer|min:0',
		'status'           => 'in:initialised,saved,reserved,expired,confirmed,on hold,cancelled,temporary',
		'reserved_until'   => 'date',
		'cancellation_fee' => 'integer|min:0',
		'comment'          => '',
		'cancelled_at'     => 'date',
		'commission_amount' => 'integer|min:0',
		'discount_percentage' => 'boolean'
	);

	public static function isActive($status)
	{
		return isset(Booking::$counted[$status]);
	}

	public function beforeSave()
	{
		if( isset($this->agent_reference) )
			$this->agent_reference = Helper::sanitiseString($this->agent_reference);

		if( isset($this->comments) )
			$this->comments = Helper::sanitiseString($this->comments);
	}

	public function getDecimalPriceAttribute()
	{
		$currency = new Currency( Context::get()->currency->code );

		return number_format(
			$this->price / $currency->getSubunitToUnit(), // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);
	}

	public function getAbsolutePriceAttribute()
	{
		$this->load('agent');
		if($this->agent)
		{
			if($this->agent->terms == 'deposit') return $this->decimal_price * (1 - ($this->agent->commission / 100));
		}
		return null;
	}

	public function getRealDecimalPriceAttribute()
	{
		// The real price is only of interest for bookings that have either a discount applied or are made by an agent
		// Thus, in all other cases, we can skip this costly calculation
		// However, in this case the real_decimal_price is still NOT the same as the decimal_price! That's why we return null instead (as it's not needed anyway)
		if((empty($this->discount) || $this->discount < 1) && empty($this->agent_id)) return null;

		$feeSum = 0;
		if(empty($this->bookingdetails))
			$this->load('bookingdetails', 'bookingdetails.departure', 'bookingdetails.addons');

		foreach($this->bookingdetails as $detail) {
			$limitBefore = in_array($this->status, ['reserved', 'expired', 'confirmed']) ? $detail->created_at : false;

			foreach($detail->addons as $addon) {
				if($addon->compulsory === 1)
				{
					if($detail->departure)
						$start = $detail->departure->start;
					else
						$start = $detail->created_at;

					$addon->calculatePrice($start, $limitBefore);

					$feeSum += floatval($addon->decimal_price) * $addon->pivot->quantity;
				}
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
			$earliestDeparture = new \DateTime($model->start);

		$model = $this->training_sessions()->orderBy('training_sessions.start', 'ASC')->first(array('training_sessions.*'));
		if(!empty($model))
			$earliestClass = new \DateTime($model->start);

		$model = $this->accommodations()->orderBy('accommodation_booking.start', 'ASC')->first();
		if(!empty($model))
			$earliestAccommodation = new \DateTime($model->pivot->start);

		$dates = [$earliestDeparture, $earliestClass, $earliestAccommodation];
		$dates = array_filter($dates);
		sort($dates);

		if(empty($dates) || empty($dates[0]))
			return null;

		return $dates[0]->format('Y-m-d');
	}

	public function getCreatedAtLocalAttribute() {
		$datetime = new \DateTime( $this->created_at, new \DateTimeZone('UTC') );
		$datetime->setTimezone( new \DateTimeZone( Context::get()->timezone ) );

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
				$start            = new \DateTime($departure->start);
				$end              = clone $start;
				$duration_hours   = floor($trip->duration);
				$duration_minutes = round( ($trip->duration - $duration_hours) * 60 );
				$end->add( new \DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );

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
				$start            = new \DateTime($training_session->start);
				$end              = clone $start;
				$duration_hours   = floor($class->duration);
				$duration_minutes = round( ($class->duration - $duration_hours) * 60 );
				$end->add( new \DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );

				$training_session->end = $end;

				return $end->format('Y-m-d H:i:s');
			});
			$lastClassReturn = $training_sessions->first()->end;
		}

		$model = $this->accommodations()->orderBy('accommodation_booking.end', 'DESC')->first();
		if(!empty($model))
			$lastAccommodationEnd = new \DateTime($model->pivot->start);

		$dates = [$lastTripReturn, $lastClassReturn, $lastAccommodationEnd];
		$dates = array_filter($dates);
		sort($dates);

		if(empty($dates))
			return null;

		$result = array_pop($dates);

		if(empty($result))
			return null;

		return $result->format('Y-m-d');
	}

	public function setDiscountAttribute($value)
	{
		$currency = new Currency( Context::get()->currency->code );

		$this->attributes['discount'] = (int) round( $value * $currency->getSubunitToUnit() );
	}

	public function getDiscountAttribute($value)
	{
		$currency = new Currency( Context::get()->currency->code );

		return number_format(
			$value / $currency->getSubunitToUnit(), // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);
	}

	public function setCancellationFeeAttribute($value)
	{
		$currency = new Currency( Context::get()->currency->code );

		$this->attributes['cancellation_fee'] = (int) round( $value * $currency->getSubunitToUnit() );
	}

	public function getCancellationFeeAttribute($value)
	{
		$currency = new Currency( Context::get()->currency->code );

		return number_format(
			$value / $currency->getSubunitToUnit(), // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);
	}

	public function getPrice()
	{
		// @note when we upgrade to php7, use ??
		return $this->real_decimal_price ? $this->real_decimal_price : $this->decimal_price;
	}

	/*
	public function setNumberOfCustomersAttribute()
	{
		$this->attributes['number_of_customers'] = count(DB::table('booking_details')->where('booking_id', $this->id)->distinct()->select(['customer_id'])->get());
	}
	*/

	public function scopeFetch($query)
	{
		// @todo change this to function parameters
		$id = \Input::get('id');
		$ref = \Input::get('ref');
		if(isset($id)) return $query->findOrFail($id);
		else return $query->where('reference', '=', $ref)->first();
	}

	public function scopeOnlyOwners($query)
	{
		return $query->where('company_id', '=', Context::get()->id);
	}

	public function decimal_price()
	{
		// TODO Tombstone
		return $this->getDecimalPriceAttribute();
	}

	public function accommodations()
	{
		if($this->loadTrashedAccommodations)
			return $this->belongsToMany(Accommodation::class)
				->withPivot('customer_id', 'start', 'end', 'packagefacade_id', 'commissionable', 'override_price')
				->withTimestamps()
				->withTrashed();

		return $this->belongsToMany(Accommodation::class)
			->withPivot('customer_id', 'start', 'end', 'packagefacade_id', 'commissionable', 'override_price')
			->withTimestamps();
	}

	/**
	 * Get all the customer related to the booking
	 *
	 * @note should this filter out the lead_customer ?? Or maybe place a lead flag on the customer object
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function customers()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Customer', 'booking_details')->withPivot('ticket_id', 'session_id', 'boatroom_id', 'packagefacade_id', 'course_id', 'training_session_id')->withTimestamps();
	}

	public function lead_customer()
	{
		return $this->belongsTo('\Scubawhere\Entities\Customer', 'lead_customer_id');
	}

	/*public function addons()
	{
		return $this->hasManyThrough('Addon', 'Bookingdetail');
	}*/

	public function bookingdetails()
	{
		return $this->hasMany('\Scubawhere\Entities\Bookingdetail');
	}

	public function sessions()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Departure', 'booking_details', 'booking_id', 'session_id')->withTimestamps();
	}

	public function departures()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Departure', 'booking_details', 'booking_id', 'session_id')->withTimestamps();
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function agent()
	{
		return $this->belongsTo('\Scubawhere\Entities\Agent');
	}

	/*public function packages()
	{
		return $this->belongsToMany('Package', 'booking_details')->withPivot('customer_id', 'ticket_id', 'session_id');
	}*/

	public function packagefacades()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Packagefacade', 'booking_details');
	}

	public function tickets()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Ticket', 'booking_details');
	}

	public function training_sessions()
	{
		return $this->belongsToMany('\Scubawhere\Entities\TrainingSession', 'booking_details', 'booking_id', 'training_session_id')->withTimestamps();
	}

	public function payments()
	{
		return $this->hasMany('\Scubawhere\Entities\Payment')/*->orderBy('created_at', 'DESC')*/;
	}

	public function pick_ups()
	{
		return $this->hasMany('\Scubawhere\Entities\PickUp')->orderBy('date');
	}

	public function refunds()
	{
		return $this->hasMany('\Scubawhere\Entities\Refund')/*->orderBy('created_at', 'DESC')*/;
	}

	public function isEditable()
	{
		return !($this->status === 'cancelled' || $this->status === 'on hold');
	}

	public function updatePrice($onlyApplyDiscount = false, $oldDiscount = 0)
	{
		$currency = new Currency( Context::get()->currency->code );
		$tickedOffPackagefacades = [];
		$tickedOffCourses = [];
		$sum = 0;
		$commission = 0;

		if($onlyApplyDiscount) {
			$sum += $this->price / $currency->getSubunitToUnit();

			$sum += (double) $oldDiscount;
			
			if ($this->price === 0) {
				$commissionRatio = 0;
			} else {
				$commissionRatio = $this->commission_amount / $this->price;
			}

			if ($this->discount_percentage) {
				$discountPercentage = $this->calculateDiscount($oldDiscount);
				$this->price = round(( $sum * $currency->getSubunitToUnit() ) * (1 - $discountPercentage));
			} else {
				$this->price = round(($sum - $this->discount) * $currency->getSubunitToUnit() );
			}
			
			$this->commission_amount = $commissionRatio * $this->price;

			$this->save();

			$this->decimal_price = $sum;

			return true;
		}

		$bookingdetails = $this->bookingdetails()->with('ticket', 'departure', 'addons', 'packagefacade', 'packagefacade.package')->get();

		$bookingdetails->each(function($detail) use (&$sum, $currency, &$tickedOffPackagefacades, &$tickedOffCourses, &$commission, $bookingdetails)
		{
			$limitBefore = in_array($this->status, ['reserved', 'expired', 'confirmed']) ? $detail->created_at : false;

			if($detail->packagefacade_id !== null)
			{
				if(!in_array($detail->packagefacade_id, $tickedOffPackagefacades))
				{
					// Add the packagefacadeID to the array so it is not summed/counted again in the next bookingdetails
					$tickedOffPackagefacades[] = $detail->packagefacade_id;

					// Find the first departure datetime that is booked in this package
					$detailStart = null;
					$accommStart = null;

					$bookingdetails = $detail->packagefacade->bookingdetails()->with('departure', 'training_session')->get();
					if($bookingdetails->count() > 0)
					{
						$firstDetail = $bookingdetails->sortBy(function($detail)
						{
							if($detail->departure)
								return $detail->departure->start;
							elseif($detail->training_session)
								return $detail->training_session->start;
							else
								return '9999-12-31 23:59:59';
						})->first();

						if($firstDetail->departure)
							$detailStart = new \DateTime($firstDetail->departure->start);
						elseif($firstDetail->training_session)
							$detailStart = new \DateTime($firstDetail->training_session->start);
					}

					$accommodations = $this->accommodations()->wherePivot('packagefacade_id', $detail->packagefacade_id)->get();
					if($accommodations->count() > 0)
					{
						$firstAccommodation = $accommodations->sortBy(function($accommodation)
						{
							return $accommodation->pivot->start;
						})->first();

						$accommStart = new \DateTime($firstAccommodation->pivot->start);
					}

					$dates = [$detailStart, $accommStart];
					$dates = array_filter($dates);
					sort($dates);

					if(empty($dates) || empty($dates[0]))
						$start = $detail->created_at;
					else
						$start = $dates[0]->format('Y-m-d H:i:s');

					// Calculate the package price at this first departure datetime and sum it up
					$detail->packagefacade->package
						->calculatePrice(
							$start,
							$limitBefore,
							$this->agent_id,
							$detail->packagefacade->commissionable,
							$detail->packagefacade->override_price
						);
					
					$sum += $detail->packagefacade->package->decimal_price;
					$commission += $detail->packagefacade->package->commission_amount;
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
						elseif($detail->training_session)
							return $detail->training_session->start;
						else
							return '9999-12-31 23:59:59';
					})->first();

					if($firstDetail->departure)
						$start = $firstDetail->departure->start;
					elseif($firstDetail->training_session)
						$start = $firstDetail->training_session->start;
					else
						$start = $detail->created_at;

					// Calculate the package price at this first departure datetime and sum it up
					$detail->course
						->calculatePrice(
							$start,
							$limitBefore,
							$this->agent_id,
							$detail->item_commissionable,
							$detail->override_price
						);
					
					$sum += $detail->course->decimal_price;
					$commission += $detail->course->commission_amount;
				}
			}
			else
			{
				// Sum up the ticket
				if($detail->departure)
					$start = $detail->departure->start;
				else
					$start = $detail->created_at;

				$detail->ticket
					->calculatePrice(
						$start,
						$limitBefore,
						$this->agent_id,
						$detail->item_commissionable,
						$detail->override_price
					);
				
				$sum += $detail->ticket->decimal_price;
				$commission += $detail->ticket->commission_amount;
			}

			/*
			 * OK. So originally the addon discount was storied within it's pivot (the addon_bookingdetail)
			 * table. The problem is that the addons discount is applied per addon, not by booking_detail, therefore
			 * applying a discount where there is more than 1 addon and in separate booking details, only
			 * 1 of the booking details addons will have the discount applied, and the total will look incorrect.
			 * So long story short, below is a fix to this problem. It will need to be address in the future, but for now
			 * it is the fix. We will make a separate lookup in the booking details by addon id, then have an array by addon id
			 * to discount, then check the addon id and submitting the discount when calculating the price.
			 */
			$addonDiscounts = array();

			$bookingdetails->each(function ($detail) use (&$addonDiscounts) {
				$detail->load('addons');
				$detail->addons->each( function ($addon) use (&$addonDiscounts) {
					if (! is_null($addon->pivot->override_price)) {
						$addonDiscounts[$addon->id] = $addon->pivot->override_price;
					}
				});
			});

			// Sum up all addons that are not part of a package
			$detail->addons->each(function($addon) use ($detail, &$sum, $limitBefore, &$commission, $addonDiscounts) {
				if($addon->pivot->packagefacade_id === null)
				{
					if($detail->departure)
						$start = $detail->departure->start;
					else
						$start = $detail->created_at;

					$addon->calculatePrice(
						$start,
						$limitBefore,
						$this->agent_id,
						$addon->pivot->commissionable,
						isset($addonDiscounts[$addon->id]) ? $addonDiscounts[$addon->id] : $addon->pivot->override_price
					);
					$sum += floatval($addon->decimal_price) * $addon->pivot->quantity;
					$commission += ($addon->commission_amount * $addon->pivot->quantity);
				}
			});
		});

		// Sum up all accommodations
		$accommodations = $this->accommodations;

		$accommodations->each(function($accommodation) use (&$sum, &$tickedOffPackagefacades, &$commission)
		{
			$limitBefore = in_array($this->status, ['reserved', 'expired', 'confirmed']) ? $accommodation->pivot->created_at : false;

			if(empty($accommodation->pivot->packagefacade_id))
			{
				// $accommodation->pivot->commissionable
				$accommodation->calculatePrice(
					$accommodation->pivot->start,
					$accommodation->pivot->end,
					$limitBefore, $this->agent_id,
					$accommodation->pivot->commissionable,
					$accommodation->pivot->override_price
				);
				$sum += $accommodation->decimal_price;
				$commission += $accommodation->commission_amount;
			}
			elseif(!in_array($accommodation->pivot->packagefacade_id, $tickedOffPackagefacades)) {
				// Add the packagefacadeID to the array so it is not summed/counted again in the next bookingdetails
				$tickedOffPackagefacades[] = $accommodation->pivot->packagefacade_id;

				// Here it is enough that we only consider accommodations, because if a trip or class would have been
				// booked for the package, the calculation would have been done above in the bookingdetail section.

				$accommodations = $this->accommodations()->wherePivot('packagefacade_id', $accommodation->pivot->packagefacade_id)->get();
				$firstAccommodation = $accommodations->sortBy(function($accommodation)
				{
					return $accommodation->pivot->start;
				})->first();

				// Calculate the package price at this first departure datetime and sum it up
				// $packagaefacade->commissionable
				$packagefacade = Packagefacade::find($accommodation->pivot->packagefacade_id);
				$packagefacade->package
					->calculatePrice(
						$firstAccommodation->pivot->start,
						$limitBefore,
						$this->agent_id,
						$packagefacade->commissionable,
						$packagefacade->override_price
					);
				$sum += $packagefacade->package->decimal_price;
				$commission += $packagefacade->package->commission_amount;
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
		
		// Ok, so to calculate the commission, incase a discount is applied globally to the booking,
		// we need to determine the percentage of the original commission, then apply that to the new price
		$discountPercentage = $this->calculateDiscount($this->discount);
		if ($this->discount_percentage) {
			$this->discount = $sum * $discountPercentage;
			$this->price = round(( $sum * $currency->getSubunitToUnit() ) * (1 - $discountPercentage));
		} else {
			$this->price = round( ($sum - $this->discount) * $currency->getSubunitToUnit() );
		}
		
		$this->commission_amount = (int) ($commission - ($commission * $discountPercentage));

		$this->save();
		
		$this->decimal_price = $sum;
	}

	protected function calculateDiscount($oldDiscount = 0)
	{
		if ($this->discount === null || $this->discount === 0 || $this->discount === '0.00') {
			return 0;
		}
		
        $discountAmount = (double) $this->discount;
		$discountAmount = $discountAmount * 100;
        $totalAmount = $this->price + ($oldDiscount * 100);
        return $discountAmount / $totalAmount;
	}

	// @todo Can i remove this as the cascade flag on delete should be set on bookings table anyways ??
	public static function boot() {
		\Eloquent::boot(); // as parent refers to Ardent we refrence Eloquent directly

		static::deleting(function($booking) {
			$booking->bookingdetails()->delete();
			$booking->pick_ups()->delete();
			$booking->accommodations()->detach();
		});
	}

}
