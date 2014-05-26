<?php

use LaravelBook\Ardent\Ardent;

class Booking extends Ardent {
	protected $fillable = array(
		'agent_id',
		'source',
		// 'price',
		'currency',
		'discount',
		'confirmed',
		'paid_cash',
		'paid_creditcard',
		'paid_cheque',
		'paid_banktransfer',
		'pay_online',
		'pay_later',
		'reserved',
		'pick_up',
		'drop_off',
		'comments'
	);

	public static $rules = array(
		'agent_id'          => 'integer|exists:agents,id|required_without:source',
		'source'            => 'alpha|required_without:agent_id|in:telephone,email,facetoface'/*,frontend,widget,other'*/,
		// 'price'          => 'numeric|min:0',
		'currency'          => 'alpha|size:3',
		'discount'          => 'numeric|min:0',
		'confirmed'         => 'integer|in:0,1',
		'paid_cash'         => 'numeric|min:0',
		'paid_creditcard'   => 'numeric|min:0',
		'paid_cheque'       => 'numeric|min:0',
		'paid_banktransfer' => 'numeric|min:0',
		'pay_online'        => 'numeric|min:0',
		'pay_later'         => 'numeric|min:0',
		'reserved'          => 'date|after:now',
		'pick_up'           => '',
		'drop_off'          => '',
		'comments'          => ''
	);

	public function beforeSave()
	{
		if( isset($this->pick_up) )
			$this->pick_up = Helper::sanitiseString($this->pick_up);

		if( isset($this->drop_off) )
			$this->drop_off = Helper::sanitiseString($this->drop_off);

		if( isset($this->comments) )
			$this->comments = Helper::sanitiseString($this->comments);
	}

	public function customers()
	{
		return $this->belongsToMany('Customer')/*->withPivot('chief')*/->withTimestamps()->orderBy('updated_at', 'asc');
	}

	public function company()
	{
		return $this->belongsTo('Company');
	}
}
