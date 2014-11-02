<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;

class Package extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'capacity');

	protected $appends = array('has_bookings', 'trashed');

	public static $rules = array(
		'name'        => 'required',
		'description' => '',
		'capacity'    => 'integer|min:0'
	);

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);
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

	public function prices()
	{
		return $this->morphMany('Price', 'owner')->orderBy('fromMonth');
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
