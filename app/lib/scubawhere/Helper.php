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
		$earth_uri       = "http://www.earthtools.org/timezone/".Auth::user()->latitude."/".Auth::user()->longitude;
		$earth_response  = simplexml_load_file($earth_uri);
		try
		{
			return new DateTime($earth_response->localtime);
		}
		catch(Exception $e)
		{
			// Another solution that has maybe more relieablity: http://worldtime.io/api/geo

			Mail::send('emails.error-report', array(
				'message' => 'Earthtools API is not available! User: '.Auth::user()->username.', Route: 'Request::method().' '.Request::path(),
				'variable' => $earth_response
			), function($message)
			{
				$message->to('soren@scubawhere.com')->subject('Scubawhere Earthtools Error');
			});

			return Response::json( array('errors' => array('The earthtools.org API is not available. Please try again later or contact scubawhere support.')), 500 ); // 500 Internal Server Error
		}
	}

	public static function booking_reference_number() {

		$length = 4;

		// $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		// Only use uppercase letters for clarity
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		$size = strlen($chars);

		$string = "";

		for($i = 0; $i < $length; $i++) {
			$string .= $chars[ mt_rand(0, $size - 1) ];
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
		if($currency === false)
		{
			// Return the company's default currency

			// Until properly implemented, we are only returning properly formatted Great Britain Pounds
			return 'GBP';
		}
		else
		{
			return strtoupper($currency);
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
