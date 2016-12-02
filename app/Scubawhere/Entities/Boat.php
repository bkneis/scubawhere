<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Boat extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $guarded = array('id', 'company_id', 'created_at', 'updated_at');

	public static $rules = array(
		'name'        => 'required|max:64',
		'description' => '',
		'capacity'    => 'required|integer'
	);

	public $appends = array('deleteable');

	public function beforeSave( $forced )
	{
		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->photo) )
			$this->photo = Helper::sanitiseString($this->photo);
	}

	public function getDeleteableAttribute()
	{
		return !($this->departures()->where('start', '>', Helper::localTime())
									->exists());
	}

	public function scopeOnlyOwners($query) 
	{
		return $query->where('company_id', '=', Context::get()->id);	
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function boatrooms()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Boatroom')->withPivot('capacity')->withTimestamps();
	}

	public function tickets()
	{
		return $this->morphToMany('\Scubawhere\Entities\Ticket', 'ticketable')->withTimestamps();
	}

	public function departures()
	{
		return $this->hasMany('\Scubawhere\Entities\Departure');
	}

	public function futureDepartures()
	{
		return $this->hasMany('\Scubawhere\Entities\Departure')->where('start', '>', Helper::localTime());
	}

	public function syncBoatrooms(array $boatrooms)
	{
		// We need to loop through each boatroom and attach them individually as when using the sync
		// method, the boatroom id's will write over each other due to them sharing the same key
		foreach ($boatrooms as $boatroom) {
			$this->boatrooms()->attach(array((int) $boatroom['id'] => array('capacity' => (int) $boatroom['capacity'])));
		}
	}
}
