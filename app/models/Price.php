<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
use PhilipBrown\Money\Currency;

class Price extends Ardent {                       // ↓ The price here is needed for creation of new ticket/package during update, when old one is booked
	protected $fillable = array('new_decimal_price', 'price', 'currency', 'fromDay', 'fromMonth', 'untilDay', 'untilMonth');

	protected $appends = array('decimal_price');

	public static $owner_id_column_name   = 'owner_id';
	public static $owner_type_column_name = 'owner_type';

	public static $rules = array(
		'new_decimal_price' => 'required|numeric|min:0',
		'price'             => 'sometimes|integer|min:0',
		'currency'          => 'required|alpha|size:3',
		'fromDay'           => 'required|integer|between:1,31',
		'untilDay'          => 'required|integer|between:1,31',
		'fromMonth'         => 'required|integer|between:1,12',
		'untilMonth'        => 'required|integer|between:1,12'
	);

	public function beforeSave()
	{
		if( isset($this->new_decimal_price) )
		{
			$currency = new Currency( Helper::currency($this->currency) );
			$this->price = (int) round( $this->new_decimal_price * $currency->getSubunitToUnit() );
			unset($this->new_decimal_price);
		}
	}

	public function getDecimalPriceAttribute()
	{
		$currency = new Currency( $this->currency );

		return number_format(
			$this->price / $currency->getSubunitToUnit(), // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);
	}

	public function owner()
	{
		return $this->morphTo();
	}
}
