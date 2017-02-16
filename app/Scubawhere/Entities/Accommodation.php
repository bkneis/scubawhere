<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class Accommodation extends Ardent {

	use Owneable;
	use Bookable;
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

	public function calculatePrice($start, $end, $limitBefore = false, $agent_id = null, $commissionable = true, $override_price = null) {

		$current_date = gettype($start) === "object" ? $start : new \DateTime($start, new \DateTimeZone( Context::get()->timezone ));
		$end          = gettype($end)   === "object" ? $end :   new \DateTime($end,   new \DateTimeZone( Context::get()->timezone ));

		if (is_null($override_price)) {

			$totalPrice = 0;
			$numberOfDays = 0;

			// Find the price for each night
			do
			{
				$date = $current_date->format('Y-m-d');

				$totalPrice += Price::where(Price::$owner_id_column_name, $this->id)
					->where(Price::$owner_type_column_name, 'Scubawhere\Entities\Accommodation')
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
					->withTrashed()
					->orderBy('id', 'DESC')
					->first()->decimal_price;

				$current_date->add( new \DateInterval('P1D') );
				$numberOfDays++;
			}
			while( $current_date < $end );

			$this->decimal_price         = number_format($totalPrice, 2, '.', '');
			$this->decimal_price_per_day = number_format($totalPrice / $numberOfDays, 2, '.', '');
		} else {
			$numberOfDays = (int) $end->diff($start)->format('%a');
			$this->decimal_price         = number_format(($override_price / 100), 2, '.', '');
			$this->decimal_price_per_day = number_format(($override_price / 100) / $numberOfDays, 2, '.', '');
		}
		
		$this->calculateCommission($agent_id, $commissionable);
	}

	public function update(array $attributes = [])
	{
		if(!parent::update($attributes)) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $this->errors()->all());
		}
		return $this;
	}

	public static function create(array $data)
	{
		$accommodation = new Accommodation($data);

		if (!$accommodation->validate()) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $accommodation->errors()->all());
		}

		Context::get()->accommodations()->save($accommodation);
		return $accommodation;
	}

	public function removeFromPackages()
	{
		$packages = $this->packages();
		if ($packages->exists()) {
			\DB::table('packageables')
				->where('packageable_type', Accommodation::class)
				->where('packageable_id', $this->id)
				->update(array('deleted_at' => \DB::raw('NOW()')));
		}
		return $this;
	}

	public function isDeleteable()
	{
		return !($this->packages()->exists());
	}

	/**
	 * Overload the bookable bookings relationship as accommodations have
	 * a direct pivot table accommodation_bookings.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
	public function bookings()
	{
		return $this->belongsToMany(Booking::class)
			->withPivot('customer_id', 'start', 'end', 'packagefacade_id')
			->withTimestamps();
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function customers()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Customer', 'accommodation_booking')->withPivot('booking_id', 'start', 'end')->withTimestamps();
	}

	/*public function basePrices()
	{
		return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNull('until');
	}

	public function prices()
	{
		return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNotNull('until');
	}*/

	public function packages()
	{
		return $this->morphToMany('\Scubawhere\Entities\Package', 'packageable')->withPivot('quantity')->withTimestamps();
	}
}
