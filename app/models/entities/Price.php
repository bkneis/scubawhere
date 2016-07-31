<?php

use LaravelBook\Ardent\Ardent;
use ScubaWhere\Helper;
use ScubaWhere\Context;
use PhilipBrown\Money\Currency;

class Price extends Ardent {
	protected $fillable = array('new_decimal_price', 'from', 'until');

	protected $appends = array('decimal_price');

	public static $owner_id_column_name   = 'owner_id';
	public static $owner_type_column_name = 'owner_type';

	public static $rules = array(
		'new_decimal_price' => 'required|numeric|min:0',
		'price'             => 'sometimes|integer|min:0',
		'from'              => 'required|required_with:until|date',
		'until'             => 'sometimes|date',
	);

	public function beforeSave()
	{
		if( isset($this->new_decimal_price) )
		{
			$currency = new Currency( Context::get()->currency->code );
			$this->price = (int) round( $this->new_decimal_price * $currency->getSubunitToUnit() );
			unset($this->new_decimal_price);
		}
	}

	public function getDecimalPriceAttribute()
	{
		$currency = new Currency( Context::get()->currency->code );

		return number_format(
			$this->price / $currency->getSubunitToUnit(), // number
			strlen( $currency->getSubunitToUnit() ) - 1, // decimals
			/* $currency->getDecimalMark() */ '.', // decimal seperator
			/* $currency->getThousandsSeperator() */ ''
		);
	}

	public function setFromAttribute($value)
	{
		$this->attributes['from'] = $value === '0000-00-00' ? '1901-12-14' : $value;
	}

	public function getFromAttribute($value)
	{
		return $value === '1901-12-14' ? '0000-00-00' : $value;
	}

	public function owner()
	{
		return $this->morphTo();
	}
}