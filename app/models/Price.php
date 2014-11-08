<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
use PhilipBrown\Money\Currency;

class Price extends Ardent {                       // ↓ The price here is needed for creation of new ticket/package during update, when old one is booked
	protected $fillable = array('new_decimal_price', 'price', 'from', 'until');

	protected $appends = array('decimal_price');

	public static $owner_id_column_name   = 'owner_id';
	public static $owner_type_column_name = 'owner_type';

	public static $rules = array(
		'new_decimal_price' => 'required|numeric|min:0',
		'price'             => 'sometimes|integer|min:0',
		'from'              => 'required|required_with:until|size:10',
		'until'             => 'sometimes|date',
	);

	public function beforeSave()
	{
		if( isset($this->new_decimal_price) )
		{
			$currency = new Currency( Auth::user()->currency->code );
			$this->price = (int) round( $this->new_decimal_price * $currency->getSubunitToUnit() );
			unset($this->new_decimal_price);
		}
	}

	public function getDecimalPriceAttribute()
	{
		$currency = new Currency( Auth::user()->currency->code );

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
