<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;

class Bookingdetail extends Ardent {

	protected $fillable = array('customer_id', 'is_lead', 'ticket_id', 'session_id', 'boatroom_id', 'packagefacade_id', 'course_id', 'training_session_id', 'temporary', 'override_price');

	protected $table = 'booking_details';

	public static $rules = array(
		'customer_id'         => 'required|integer|min:1',
		'ticket_id'           => 'integer|min:1|required_with:session_id|required_without:course_id',
		'session_id'          => 'integer|min:1|required_without_all:training_session_id,temporary',
		'boatroom_id'         => 'integer|min:1',
		'packagefacade_id'    => 'integer|min:1',
		'course_id'           => 'integer|min:1|required_with:training_session_id,training_id|required_without:ticket_id',
		'training_id'         => 'integer|min:1|required_with:training_session_id|required_without:ticket_id',
		'training_session_id' => 'integer|min:1|required_without_all:session_id,temporary',
		'temporary'           => 'boolean',
		'override_price'      => ''
	);

	public function beforeSave()
	{
		//
	}

	public function addons()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Addon')->withPivot('quantity', 'packagefacade_id')->withTimestamps()->withTrashed();
	}

	public function boatroom()
	{
		return $this->belongsTo('\Scubawhere\Entities\Boatroom');
	}

	public function booking()
	{
		return $this->belongsTo('\Scubawhere\Entities\Booking');
	}

	public function course()
	{
		return $this->belongsTo('\Scubawhere\Entities\Course')->withTrashed();
	}

	public function customer()
	{
		return $this->belongsTo('\Scubawhere\Entities\Customer');
	}

	public function company()
	{
		return $this->hasManyThrough('\Scubawhere\Entities\Company', 'Booking');
	}

	public function ticket()
	{
		return $this->belongsTo('\Scubawhere\Entities\Ticket')->withTrashed();
	}

	public function departure()
	{
		return $this->belongsTo('\Scubawhere\Entities\Departure', 'session_id')->withTrashed();
	}

	public function session()
	{
		return $this->belongsTo('\Scubawhere\Entities\Departure', 'session_id')->withTrashed();
	}

	public function training()
	{
		return $this->belongsTo('\Scubawhere\Entities\Training')->withTrashed();
	}

	public function training_session()
	{
		return $this->belongsTo('\Scubawhere\Entities\TrainingSession')->withTrashed();
	}

	public function packagefacade()
	{
		return $this->belongsTo('\Scubawhere\Entities\Packagefacade');
	}

	public function getPrice()
	{
		$limitBefore = in_array($this->booking->status, ['reserved', 'expired', 'confirmed']) ? $this->created_at : false;

		if (count($this->packagefacade) == 0)
		{
			$this->ticket->calculatePrice($this->session->start, $limitBefore);

			$price = $this->ticket->decimal_price;

			foreach($this->addons as $addon)
			{
				if(count($addon->packagefacade) == 0)
				{
					$price += $addon->decimal_price;
				}
			}
		}

		return number_format($price, 2, '.', '');
	}
}
