<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Boatroom extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description');

	public static $rules = array(
		'name'        => 'required|max:64',
		'description' => '',
		'photo'       => '',
	);

	public $appends = array('deleteable');

	public function beforeSave( $forced )
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);

		if( isset($this->photo) )
			$this->photo = Helper::sanitiseString($this->photo);
	}

	public function scopeOnlyOwners($query) 
	{
		return $query->where('company_id', '=', Context::get()->id);
	}

	public function getDeleteableAttribute()
	{
		return !($this->boats()->exists() || $this->tickets()->exists());
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function boats()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Boat')->withPivot('capacity')->withTimestamps();
	}

	public function tickets()
	{
		return $this->morphToMany('\Scubawhere\Entities\Ticket', 'ticketable')->withTimestamps();
	}

	public function bookingdetails()
	{
		return $this->hasMany('\Scubawhere\Entities\Bookingdetail');
	}
}
