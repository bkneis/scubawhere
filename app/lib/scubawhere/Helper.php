<?php namespace ScubaWhere;

use Mews\Purifier\Facades\Purifier;

class Helper
{

	public static function sanitiseString($string)
	{
		// return strip_tags( trim($string) );
		return htmlentities( strip_tags( trim($string) ) );
	}

	public static function sanitiseBasicTags($string)
	{
		// htmlpurifier.org
		return Purifier::clean($string);
	}

	public static function rand_string($length) {

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		$size = strlen($chars);

		for($i = 0; $i < $length; $i++) {
			$string .= $chars[ rand(0, $size - 1) ];
		}

		return $string;
	}

	/**
	 * Validates a currency code, takes Input::('currency') if no parameter provided
	 * @param  string $currency The currency code to validate
	 * @return string           A correctly formatted currency string
	 */
	public static function currency($currency = false)
	{

		// Until properly implemented, we are only returning properly formatted Great Britain Pounds
		return 'GBP';

		if($currency === false)
		{
			// Return the company's default currency
		}

		// Ensure the code is lowercase
		$currency = strtoupper($currency);

		// Check if the provided currency code is valid
		if( in_array( $currency, currencies() ) )
		{
			return $currency;
		}
		else
		{
			return '';
		}
	}

	public static function currencies()
	{
		$currencies = array(
			'GBP',
			'EUR',
			'USD'
			);

		return $currencies;
	}
}
