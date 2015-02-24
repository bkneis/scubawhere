<?php

class ReportController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		/**
		 * Allowed input parameter
		 * after  {date string}
		 * before {date string}
		 */

		$after  = Input::get('after', null);
		$before = Input::get('before', null);

		if(empty($after) || empty($before))
			return Response::json(['errors' => ['Both the "after" and the "before" parameters are required.']], 400); // 400 Bad Request

		$afterUTC  = new DateTime( $after,  new DateTimeZone( Auth::user()->timezone ) ); $afterUTC->setTimezone( new DateTimeZone('Europe/London') );
		$beforeUTC = new DateTime( $before, new DateTimeZone( Auth::user()->timezone ) ); $beforeUTC->setTimezone( new DateTimeZone('Europe/London') );

		$RESULT = [];
		$currency = new PhilipBrown\Money\Currency( Auth::user()->currency->code );


		#############################
		// Generate total utilisation
		$departures = Auth::user()->departures()->whereBetween('start', [$after, $before])->get();

		$usedUp = $available = 0;

		foreach($departures as $departure)
		{
			$usedUp    += $departure->capacity[0] * 1;
			$available += $departure->capacity[1] * 1;
		}

		$RESULT['utilisation'] = ['used' => $usedUp, 'available' => $available];


		########################################
		// Generate frequency of booking sources
		$sql = "SELECT source, COUNT(*) FROM bookings WHERE company_id=? AND status='confirmed' AND created_at BETWEEN ? AND ? GROUP BY source";
		$sql = DB::select($sql, [Auth::user()->id, $afterUTC, $beforeUTC]);

		$sources = [];

		foreach($sql as $object)
		{
			$name = $object->source === null ? 'agent' : $object->source;
			$sources[$name] = $object->{'COUNT(*)'};
		}

		$RESULT['source_frequency'] = $sources;


		######################################
		// Generate revenue per booking source
		$sql = "SELECT source, SUM(price), SUM(discount) FROM bookings WHERE company_id=? AND status='confirmed' AND created_at BETWEEN ? AND ? GROUP BY source";
		$sql = DB::select($sql, [Auth::user()->id, $afterUTC, $beforeUTC]);

		$sources = [];

		foreach($sql as $object)
		{
			$name = $object->source === null ? 'agent' : $object->source;
			$sources[$name] = number_format(
				($object->{'SUM(price)'} - $object->{'SUM(discount)'}) / $currency->getSubunitToUnit(), // number
				strlen( $currency->getSubunitToUnit() ) - 1, // decimals
				/* $currency->getDecimalMark() */ '.', // decimal seperator
				/* $currency->getThousandsSeperator() */ ''
			);
		}

		$RESULT['source_revenue'] = $sources;


		print_r($RESULT);
		exit;
	}

}
