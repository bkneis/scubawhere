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
		$earth_uri       = "http://www.earthtools.org/timezone/".\Auth::user()->latitude."/".\Auth::user()->longitude;
		$earth_response  = simplexml_load_file($earth_uri);
		try
		{
			return new \DateTime($earth_response->localtime);
		}
		catch(\Exception $e)
		{
			// Another solution that has maybe more reliablity: http://worldtime.io/api/geo

			\Mail::send('emails.error-report', array(
				'content' => 'Earthtools API is not available! User: '.\Auth::user()->username.', Route: '.\Request::method().' '.\Request::path(),
				'variable' => $earth_response
			), function($message)
			{
				$message->to('soren@scubawhere.com')->subject('Scubawhere Earthtools Error');
			});

			return \Response::json( array('errors' => array('The earthtools.org API is not available. Please try again later or contact scubawhere support.')), 500 ); // 500 Internal Server Error
		}
	}

	// Check if date lies in the past (local time)
	public static function isPast($datestring) {
		$local_time = self::localTime();

		if( !($local_time instanceof \DateTime) )
			return $local_time;

		$departure_start = new \DateTime($datestring);

		if($departure_start < $local_time )
			return true;

		return false;
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

		do
		{
			$string = "";

			for($i = 0; $i < $length; $i++) {
				$string .= $chars[ mt_rand(0, $size - 1) ];
			}
		}
		while( in_array($string, $references) );

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
