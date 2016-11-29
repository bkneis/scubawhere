<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Course extends Ardent {
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	protected $fillable = array('name', 'description', 'capacity', 'certificate_id');

	public static $rules = array(
		'name'           => 'required',
		'description'    => '',
		'capacity'       => 'integer|min:0',
		'certificate_id' => 'integer|exists:certificates,id'
	);

    public $appends = array('deleteable');

	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);
    }

    public function getDeleteableAttribute()
    {
        return !($this->packages()->exists());
    }

	public function calculatePrice($start, $limitBefore = false)
	{
		$price = Price::where(Price::$owner_id_column_name, $this->id)
		     ->where(Price::$owner_type_column_name, 'Scubawhere\Entities\Course')
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

	public function scopeOnlyOwners($query)
	{
		return $query->where('company_id', '=', Context::get()->id);
	}

	public function bookingdetails()
	{
		return $this->hasMany('\Scubawhere\Entities\Bookingdetail');
	}

	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	public function packages()
	{
        return $this->morphToMany('\Scubawhere\Entities\Package', 'packageable')
                    ->withPivot('quantity')
                    ->withTimestamps();
	}

	public function basePrices()
	{
		return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNull('until');
	}

	public function prices()
	{
		return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNotNull('until');
	}

	public function tickets()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Ticket')->withPivot('quantity')->withTimestamps();
	}

	public function trainings()
	{
        return $this->belongsToMany('\Scubawhere\Entities\Training')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
