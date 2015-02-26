<?php

use ScubaWhere\Helper;

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


		#################################
		// Add request paramets to result
		$RESULT['daterange'] = [
			'after'    => Helper::sanitiseString($after),
			'before'   => Helper::sanitiseString($before),
			'timezone' => Auth::user()->timezone,
		];


		#############################
		// Generate utilisation report
		$departures = Auth::user()->departures()->whereBetween('start', [$after, $before])->with(
			'trip',
			'bookingdetails',
				'bookingdetails.booking',
				'bookingdetails.ticket'
		)->get();

		$utilisation = []; $i = 1;
		foreach($departures as $departure)
		{
			$max = $departure->capacity[1];

			$utilisation[$i] = [
				'date'       => $departure->start,
				'name'       => $departure->trip->name,
				'tickets'    => [],
				'unassigned' => $max - $departure->capacity[0],
				'capacity'   => $max,
			];

			foreach($departure->bookingdetails as $detail)
			{
				if($detail->booking->status !== 'confirmed') continue;

				if(empty($utilisation[$i]['tickets'][$detail->ticket->name])) $utilisation[$i]['tickets'][$detail->ticket->name] = 0;

				$utilisation[$i]['tickets'][$detail->ticket->name]++;
			}

			$i++;
		}

		// Calculate total average
		$total = ['tickets' => [], 'unassigned' => 0, 'capacity' => 0];
		foreach ($utilisation as $trip)
		{
			$total['unassigned'] += $trip['unassigned'];
			$total['capacity']   += $trip['capacity'];

			foreach($trip['tickets'] as $name => $number)
			{
				if(empty($total['tickets'][$name])) $total['tickets'][$name] = 0;

				$total['tickets'][$name] += $number;
			}
		}

		$RESULT['utilisation'] = $utilisation;
		$RESULT['utilisation_total'] = $total;


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
