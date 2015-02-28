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

	public static function localTime()
	{
		return new \DateTime( 'now', new \DateTimeZone( \Auth::user()->timezone ) );
	}

	/**
	 * Check if date lies in the past (local time)
	 * @param  string  $datestring  The datestring to test
	 * @return boolean
	 */
	public static function isPast($datestring) {
		$local_time = self::localTime();

		$test_date = new \DateTime($datestring, new \DateTimeZone( \Auth::user()->timezone ));

		if($test_date < $local_time )
			return true;

		return false;
	}

	public static function cleanPriceArray($prices)
	{
		return array_filter($prices, function($element, $id)
		{
			// Filter out every price that is either empty or has a numeric ID (already exists)
			return !($element['new_decimal_price'] === '' || is_numeric($id));
		}, \ARRAY_FILTER_USE_BOTH);
	}

	public static function checkPricesChanged($old_prices, $prices, $isBase = false)
	{
		$old_prices = $old_prices->toArray();

		// Compare number of prices
		if(count($prices) !== count($old_prices)) return true;

		// Keyify $old_prices and reduce them to input fields
		$array = array();
		$input_keys = array('decimal_price' => '', 'from' => '');
		if(!$isBase)
			$input_keys['until'] = '';

		foreach($old_prices as $old_price)
		{
			$array[ $old_price['id'] ] = array_intersect_key($old_price, $input_keys);
		}
		$old_prices = $array; unset($array);

		// Compare price IDs
		if( count( array_merge( array_diff_key($prices, $old_prices), array_diff_key($old_prices, $prices) ) ) > 0 )
			return true;

		/**
		 * The following comparison works, because `array_diff` only compares the values of the arrays, not the keys.
		 * The $prices arrays have a `new_decimal_price` key, while the $old_prices arrays have a `decimal_price` key,
		 * but since they represent the same info, the comparison works and returns the expected result.
		 */
		foreach($old_prices as $id => $old_price)
		{
			// Compare arrays in both directions
			if( count( array_merge( array_diff($prices[$id], $old_price), array_diff($old_price, $prices[$id]) ) ) > 0 )
				return true;
		}

		return false;
	}

	public static function booking_reference_number() {

		$length = 4;

		// $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		// Only use uppercase letters for clarity
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		$size = strlen($chars);

		$references = \Booking::lists('reference');

		// Taken from http://www.noswearing.com/fourletterwords.php
		$forbidden  = array('ANUS', 'ARSE', 'CLIT', 'COCK', 'COON', 'CUNT', 'DAGO', 'DAMN', 'DICK', 'DIKE', 'DYKE', 'FUCK', 'GOOK', 'HEEB', 'HELL', 'HOMO', 'JIZZ', 'KIKE', 'KUNT', 'KYKE', 'MICK', 'MUFF', 'PAKI', 'PISS', 'POON', 'POOP', 'PUTO', 'SHIT', 'SHIZ', 'SLUT', 'SMEG', 'SPIC', 'TARD', 'TITS', 'TWAT', 'WANK');

		$unallowed = array_merge($references, $forbidden);

		do
		{
			$string = "";

			for($i = 0; $i < $length; $i++) {
				$string .= $chars[ mt_rand(0, $size - 1) ];
			}
		}
		while( in_array($string, $unallowed));

		return $string;
	}

	/**
	 * Makes an array with keys into an array without keys
	 */
	public static function normaliseArray($array)
	{
		$normalisedArray = [];
		foreach($array as $value)
		{
			$normalisedArray[] = $value;
		}
		return $normalisedArray;
	}
}
