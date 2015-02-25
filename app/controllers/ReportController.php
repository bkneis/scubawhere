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

		$afterUTC  = new DateTime( $after,  new DateTimeZone( Auth::user()->timezone ) ); $afterUTC->setTimezone(  new DateTimeZone('Europe/London') );
		$beforeUTC = new DateTime( $before, new DateTimeZone( Auth::user()->timezone ) ); $beforeUTC->setTimezone( new DateTimeZone('Europe/London') );

		$RESULT = [];
		$currency = new PhilipBrown\Money\Currency( Auth::user()->currency->code );


		#############################
		// Generate utilisation report
		$departures = Auth::user()->departures()->whereBetween('start', [$after, $before])->get();
		$cabinNames = Auth::user()->boatrooms()->lists('name', 'id');

		$usedUp = $available = 0;
		$cabins = [];
		// Prepare cabins array
		foreach($cabinNames as $name)
		{
			$cabins[$name] = ['used' => 0, 'available' => 0];
		}

		foreach($departures as $departure)
		{
			// Calculate total utilisation
			$usedUp    += $departure->capacity[0];
			$available += $departure->capacity[1];

			// Calculate utilisation per cabin
			foreach($departure->capacity[2] as $key => $array)
			{
				$name = $cabinNames[$key];

				$cabins[$name]['used']      += $array[0];
				$cabins[$name]['available'] += $array[1];
			}
		}

		$RESULT['utilisation'] = ['used' => $usedUp, 'available' => $available];
		$RESULT['utilisation_cabins'] = $cabins;


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

			if(empty($sources[$name])) $sources[$name] = 0;

			$sources[$name] += ($object->{'SUM(price)'} - $object->{'SUM(discount)'}) / $currency->getSubunitToUnit();
		}

		$RESULT['source_revenue'] = $sources;

		###########################################
		// Generate revenue by customer demographic
		$sql = DB::table('bookings')
		    ->join('customers', 'bookings.lead_customer_id', '=', 'customers.id')
		    ->join('countries', 'customers.country_id', '=', 'countries.id')
		    ->where('bookings.company_id', Auth::user()->id)
		    ->where('bookings.status', 'confirmed')
		    ->whereBetween('bookings.created_at', [$afterUTC, $beforeUTC])
		    ->select('customers.country_id', 'countries.name', DB::raw('SUM(price)'), DB::raw('SUM(discount)'))
		    ->groupBy('customers.country_id')
		    ->get();

		$countries = [];

		foreach($sql as $object)
		{
			$name = $object->{'name'};

			if(empty($countries[$name])) $countries[$name] = 0;

			$countries[$name] += ($object->{'SUM(price)'} - $object->{'SUM(discount)'}) / $currency->getSubunitToUnit();
		}

		$RESULT['country_revenue'] = $countries;


		return($RESULT);
	}

}
