<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class Package extends Ardent {
	
	use Owneable;
	use Bookable;
	use SoftDeletingTrait;
	
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until');

	protected $hidden = array('parent_id');

	public static $rules = array(
		'name'                => 'required',
		'description'         => '',
		'parent_id'           => 'integer|min:1',
		'available_from'      => 'date',
		'available_until'     => 'date',
		'available_for_from'  => 'date',
		'available_for_until' => 'date'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);
	}

	public function setAvailableFromAttribute($value)
	{
		$value = trim($value);
		$this->attributes['available_from'] = $value ?: null;
	}

	public function setAvailableUntilAttribute($value)
	{
		$value = trim($value);
		$this->attributes['available_until'] = $value ?: null;
	}

	public function setAvailableForFromAttribute($value)
	{
		$value = trim($value);
		$this->attributes['available_for_from'] = $value ?: null;
	}

	public function setAvailableForUntilAttribute($value)
	{
		$value = trim($value);
		$this->attributes['available_for_until'] = $value ?: null;
	}

	public function calculatePrice($start, $limitBefore = false) {
		$price = Price::where(Price::$owner_id_column_name, $this->id)
		     ->where(Price::$owner_type_column_name, 'Scubawhere\Entities\Package')
		     ->where('from', '<=', $start)
		     ->where(function($query) use ($start)
		     {
		     	$query->whereNull('until')
		     	      ->orWhere('until', '>=', $start);
		     })
		     ->where(function($query) use ($limitBefore)
		     {
		     	if($limitBefore)
		     		$query->where('created_at', '<=', $limitBefore);
		     })
		     ->orderBy('id', 'DESC')
			 ->withTrashed()
		     ->first();

		$this->decimal_price = $price->decimal_price;
	}

    /**
     * @param array $data
     * @return $this
     * @throws HttpUnprocessableEntity
     */
    public function update(array $data = [])
    {
        if (! parent::update($data)) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $this->errors()->all());
        }
        return $this;
    }
	
	public static function create(array $data = [])
	{
		$package = new Package($data);
		if (!$package->validate()) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $package->errors()->all());
		}
		return Context::get()->packages()->save($package);
	}

	public function accommodations()
	{
        return $this->morphedByMany('\Scubawhere\Entities\Accommodation', 'packageable')
                    ->withPivot('quantity')
                    //->withTrashed()
                    ->withTimestamps();
	}

	public function addons()
	{
		return $this->morphedByMany('\Scubawhere\Entities\Addon', 'packageable')->withPivot('quantity')->withTimestamps();
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function courses()
	{
        return $this->morphedByMany('\Scubawhere\Entities\Course', 'packageable')
                    ->withPivot('quantity')
                    ->withTimestamps();
	}

	public function packagefacades()
	{
		return $this->hasMany('\Scubawhere\Entities\Packagefacade');
	}

	public function tickets()
	{
		return $this->morphedByMany('\Scubawhere\Entities\Ticket', 'packageable')->withPivot('quantity')->withTimestamps();
	}

	public function syncTickets($tickets)
	{
		if(is_array($tickets) && !empty($tickets)) {
			try {
				$this->tickets()->sync( $tickets );
			}
			catch(\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__,
					['Their was a problem associating the tickets array, please ensure it meets the API requirements']);
			}
		}
	}

	public function syncCourses($courses)
	{
		if(is_array($courses) && !empty($courses)) {
			try {
				$this->courses()->sync($courses);
			}
			catch(\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__,
					['Their was a problem associating the courses array, please ensure it meets the API requirements']);
			}
		}
	}

	public function syncAccommodations($accommodations)
	{
		if(is_array($accommodations) && !empty($accommodations)) {
			try {
				$this->accommodations()->sync($accommodations);
			}
			catch(\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__,
					['Their was a problem associating the accommodations array, please ensure it meets the API requirements']);
			}
		}
	}

	public function syncAddons($addons)
	{
		if(is_array($addons) && !empty($addons)) {
			try {
				$this->addons()->sync($addons);
			}
			catch(\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__,
					['Their was a problem associating the addons array, please ensure it meets the API requirements']);
			}
		}
	}

	public function syncItems(array $items)
	{
		$this->syncAccommodations($items['accommodations']);
		$this->syncAddons($items['addons']);
		$this->syncCourses($items['courses']);
		$this->syncTickets($items['tickets']);
		return $this;
	}
}
