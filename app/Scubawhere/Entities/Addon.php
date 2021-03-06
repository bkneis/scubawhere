<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

/**
 * Class Addon
 * @package Scubawhere\Entities
 *
 * Addons are additional bookable items that must be assigned to a booked session. They have a one to many
 * relationship so that a customer can book multiple addons to the same day trip.
 */
class Addon extends Ardent {

	use Owneable;
	use Bookable;
	use SoftDeletingTrait;
	
	protected $dates = ['deleted_at'];

	protected $fillable = array(
		'name',
		'description',
		'compulsory',
		'parent_id'
	);

	protected $hidden = array('parent_id');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'compulsory'  => 'required|boolean',
		'parent_id'   => 'integer|min:1'
	);

    protected $appends = array('deletable');

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->compulsory) )
			$this->compulsory = Helper::sanitiseString($this->compulsory);
	}

    public function getDeletableAttribute() 
    {
        return !($this->packages()->exists());
    }

	public function getHasBookingsAttribute()
	{
		return $this->bookingdetails()
		    ->whereHas('booking', function($query)
		    {
		    	$query->whereIn('status', Booking::$counted);
		    })
		    ->count() > 0;
	}

	public function getCurrencyAttribute()
	{
		return Context::get()->currency;
	}

	public static function create(array $data)
	{
		$addon = new Addon($data);

		if (!$addon->validate()) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $addon->errors()->all());
		}

		Context::get()->addons()->save($addon);
		return $addon;
	}

	public function update(array $data = [])
	{
		if (! parent::update($data)) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $this->errors()->all());
		}
		return $this;
	}

	/*public function bookings()
	{
		return $this->belongsToMany('Bookingdetail')
					->join('bookings', 'booking_id', '=', 'bookings.id')
					->select('bookings.reference', 'bookings.created_at');
	}*/

	public function bookingdetails()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Bookingdetail')->withPivot('quantity', 'packagefacade_id')->withTimestamps();
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function customers()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Customer', 'Bookingdetail');
	}

	public function packages()
	{
		return $this->morphToMany('\Scubawhere\Entities\Package', 'packageable')->withPivot('quantity')->withTimestamps();
	}
	
}
