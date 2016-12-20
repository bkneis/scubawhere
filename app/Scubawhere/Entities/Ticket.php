<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class Ticket extends Ardent {
	
	use Owneable;
	use Bookable;
	use SoftDeletingTrait;
	use LimitedAvailability;
	
	protected $dates = array('deleted_at');

	//protected $guarded = array('id', 'company_id', 'created_at', 'updated_at', 'deleted_at');

	protected $fillable = array('name', 'description', 'only_packaged', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until');

	protected $hidden = array('parent_id');

	public static $rules = array(
		'name'                => 'required',
		'description'         => '',
		'only_packaged'       => 'boolean',
		'parent_id'           => 'integer|min:1',
		'available_from'      => 'date',
		'available_until'     => 'date',
		'available_for_from'  => 'date',
		'available_for_until' => 'date'
	);

    public $appends = array('deleteable');

	public function beforeSave()
	{
		if ( isset($this->name) ) {
			$this->name = Helper::sanitiseString($this->name);
		}

		if ( isset($this->description) ) {
			$this->description = Helper::sanitiseBasicTags($this->description);
		}
    }

    public function getDeleteableAttribute()
    {
        return !($this->packages()->exists() || $this->courses()->exists());
    }

	/**
	 * Associate any boats to the ticket if the ticket is limited to certain boats
	 *
	 * @param array $boats
	 * @throws HttpUnprocessableEntity
	 */
	public function syncBoats($boats)
	{
		if(is_array($boats) && !empty($boats)) {
			try {
				$this->boats()->sync($boats);
			} catch (\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Could not assign boats to the ticket, \'boats\' array is propably erroneous.']);
			}
		}
	}

	/**
	 * Associate any boatrooms that the ticket is restricted to
	 *
	 * @param array $boatrooms
	 * @throws HttpUnprocessableEntity
	 */
	public function syncBoatrooms($boatrooms)
	{
		if(is_array($boatrooms) && !empty($boatrooms)) {
			try {
				$this->boatrooms()->sync($boatrooms);
			} catch (\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Could not assign locations to trip, \'tags\' array is propably erroneous.']);
			}
		}
	}

	/**
	 * Associate all trips the ticket can be booked for
	 *
	 * @param array $trips
	 * @throws HttpUnprocessableEntity
	 */
	public function syncTrips($trips)
	{
		if (is_array($trips) && !empty($trips)) {
			try {
				$this->trips()->sync($trips);
			} catch (\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Could not assign locations to trip, \'tags\' array is propably erroneous.']);
			}
		}
	}

	/**
	 * Batch sync all the relationships of a ticket.
	 * 
	 * This method is mainly used when updating / creating a ticket from
	 * the ticket service as it provides a nice and simple interface
	 * 
	 * @param $items
	 * @return $this
	 * @throws HttpUnprocessableEntity
     */
	public function syncItems($items)
	{
		$this->syncTrips($items['trips']);
		$this->syncBoats($items['boats']);
		$this->syncBoatrooms($items['boatrooms']);
		return $this;
	}
	
	public static function create(array $data = [])
	{
		$ticket = new Ticket($data);
		if (!$ticket->validate()) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $ticket->errors()->all());
		}
		return Context::get()->tickets()->save($ticket);
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

	public function bookings()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Booking', 'booking_details')
			->withPivot('session_id', 'package_id', 'customer_id', 'is_lead')
			->withTimestamps();
	}

	public function trips()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Trip')->withTimestamps();
	}

	public function boats()
	{
		return $this->morphedByMany('\Scubawhere\Entities\Boat', 'ticketable')->withTimestamps();
	}

	public function boatrooms()
	{
		return $this->morphedByMany('\Scubawhere\Entities\Boatroom', 'ticketable')->withTimestamps();
	}

	public function courses()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Course')->withPivot('quantity')->withTimestamps();
	}

	public function packages()
	{
		return $this->morphToMany('\Scubawhere\Entities\Package', 'packageable')->withPivot('quantity')->withTimestamps();
	}

}
