<?php

use ScubaWhere\Helper;

class ReportController extends Controller {

	public function getUtilisation()
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

		$before = new DateTime($before);
		$before->add(new DateInterval('P1D'));
		$before = $before->format('Y-m-d H:i:s');

		$RESULT = [];


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
		)->orderBy('start')->get();

		$utilisation = []; $i = 1;
		foreach($departures as $departure)
		{
			$max = isset($departure->capacity[1]) ? $departure->capacity[1] : 0;

			$utilisation[$i] = [
				'date'       => $departure->start,
				'name'       => $departure->trip->name,
				'tickets'    => [],
				'assigned'   => 0,
				'unassigned' => $max,
				'capacity'   => $max,
			];

			foreach($departure->bookingdetails as $detail)
			{
				if($detail->booking->status !== 'confirmed') continue;

				$utilisation[$i]['assigned']++;

				if(empty($utilisation[$i]['tickets'][$detail->ticket->name])) $utilisation[$i]['tickets'][$detail->ticket->name] = 0;

				$utilisation[$i]['tickets'][$detail->ticket->name]++;

				if($utilisation[$i]['unassigned'] > 0)
					$utilisation[$i]['unassigned']--;
			}

			if($utilisation[$i]['capacity'] === 0)
				$utilisation[$i]['capacity'] = $utilisation[$i]['assigned'];

			$i++;
		}

		// Calculate total average
		$total = ['tickets' => [], 'assigned' => 0, 'unassigned' => 0, 'capacity' => 0];
		foreach ($utilisation as $trip)
		{
			$total['assigned']   += $trip['assigned'];
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

		return $RESULT;
	}

	public function getTrainingutilisation()
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

		$before = new DateTime($before);
		$before->add(new DateInterval('P1D'));
		$before = $before->format('Y-m-d H:i:s');

		$RESULT = [];


		#################################
		// Add request paramets to result
		$RESULT['daterange'] = [
			'after'    => Helper::sanitiseString($after),
			'before'   => Helper::sanitiseString($before),
			'timezone' => Auth::user()->timezone,
		];


		#############################
		// Generate utilisation report
		$trainings = Auth::user()->training_sessions()->whereBetween('start', [$after, $before])->with(
			'training',
			'bookingdetails',
				'bookingdetails.booking',
				'bookingdetails.course'
		)->orderBy('start')->get();

		$utilisation = []; $i = 1;
		foreach($trainings as $training)
		{
			$max = $training->capacity[1];

			$utilisation[$i] = [
				'date'       => $training->start,
				'name'       => $training->training->name,
				'courses'    => [],
				'unassigned' => $max,
				'capacity'   => $max,
			];

			foreach($training->bookingdetails as $detail)
			{
				if($detail->booking->status !== 'confirmed') continue;

				if(empty($utilisation[$i]['courses'][$detail->course->name])) $utilisation[$i]['courses'][$detail->course->name] = 0;

				$utilisation[$i]['courses'][$detail->course->name]++;
				$utilisation[$i]['unassigned']--;
			}

			$i++;
		}

		// Calculate total average
		$total = ['courses' => [], 'unassigned' => 0, 'capacity' => 0];
		foreach ($utilisation as $training)
		{
			$total['unassigned'] += $training['unassigned'];
			$total['capacity']   += $training['capacity'];

			foreach($training['courses'] as $name => $number)
			{
				if(empty($total['courses'][$name])) $total['courses'][$name] = 0;

				$total['courses'][$name] += $number;
			}
		}

		$RESULT['utilisation'] = $utilisation;
		$RESULT['utilisation_total'] = $total;

		return $RESULT;
	}

	public function getSources()
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

		$afterUTC  = new DateTime( $after,  new DateTimeZone( Auth::user()->timezone ) ); $afterUTC->setTimezone(  new DateTimeZone('UTC') );
		$beforeUTC = new DateTime( $before, new DateTimeZone( Auth::user()->timezone ) ); $beforeUTC->setTimezone( new DateTimeZone('UTC') );
		$beforeUTC->add(new DateInterval('P1D'));

		$RESULT = [];
		$currency = new PhilipBrown\Money\Currency( Auth::user()->currency->code );


		#################################
		// Add request paramets to result
		$RESULT['daterange'] = [
			'after'    => Helper::sanitiseString($after),
			'before'   => Helper::sanitiseString($before),
			'timezone' => Auth::user()->timezone,
		];

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

		return $RESULT;
	}

	public function getDemographics()
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

		$afterUTC  = new DateTime( $after,  new DateTimeZone( Auth::user()->timezone ) ); $afterUTC->setTimezone(  new DateTimeZone('UTC') );
		$beforeUTC = new DateTime( $before, new DateTimeZone( Auth::user()->timezone ) ); $beforeUTC->setTimezone( new DateTimeZone('UTC') );
		$beforeUTC->add(new DateInterval('P1D'));

		$RESULT = [];
		$currency = new PhilipBrown\Money\Currency( Auth::user()->currency->code );


		#################################
		// Add request paramets to result
		$RESULT['daterange'] = [
			'after'    => Helper::sanitiseString($after),
			'before'   => Helper::sanitiseString($before),
			'timezone' => Auth::user()->timezone,
		];

		###########################################
		// Generate revenue by customer age
		$sql = DB::table('bookings')
		    ->join('customers', 'bookings.lead_customer_id', '=', 'customers.id')
			->where('bookings.company_id', Auth::user()->id)
		    ->where('bookings.status', 'confirmed')
		    ->whereBetween('bookings.created_at', [$afterUTC, $beforeUTC])
		    ->select('bookings.price', 'bookings.created_at', 'customers.birthday')
			->get();

		$ages = [];
		$ages['0-15']    = 0;
		$ages['16-25']   = 0;
		$ages['26-35']   = 0;
		$ages['36-50']   = 0;
		$ages['50+']     = 0;
		$ages['unknown'] = 0;

		foreach($sql as $object)
		{
			if($object->birthday != null) {

				$dateOfBooking = new DateTime($object->created_at);
				$dateOfBooking->setTime(0, 0, 0);

				$birthday = new DateTime($object->birthday);

				$age = $dateOfBooking->diff($birthday)->y;

				     if(             $age <= 15) $ages['0-15']  += $object->price;
				else if($age > 16 && $age <= 25) $ages['16-25'] += $object->price;
				else if($age > 25 && $age <= 35) $ages['26-35'] += $object->price;
				else if($age > 35 && $age <= 50) $ages['36-50'] += $object->price;
				else if($age > 50)               $ages['50+']   += $object->price;
			}
			else
				$ages['unknown'] += $object->price;
		}

		###########################################
		// Generate revenue by customer country
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

		###########################################
		// Generate revenue by customer certificate
		$sql = DB::table('bookings')
		    ->join('customers', 'bookings.lead_customer_id', '=', 'customers.id')
		    ->join('certificate_customer', 'customer_id', '=', 'bookings.lead_customer_id')
		    ->where('bookings.company_id', Auth::user()->id)
		    ->where('bookings.status', 'confirmed')
		    ->whereBetween('bookings.created_at', [$afterUTC, $beforeUTC])
		    ->get();

		$certificates = [];

		/*foreach($sql as $object)
		{
			$name = $object->{'name'};

			if(empty($certificates[$name])) $certificates[$name] = 0;

			$certificates[$name] += ($object->{'SUM(price)'} - $object->{'SUM(discount)'}) / $currency->getSubunitToUnit();
		}*/

		$RESULT['country_revenue'] = $countries;
		$RESULT['age_revenue'] = $ages;
		$RESULT['certificate_revenue'] = $sql;

		return $RESULT;
	}

	public function getRevenueStreams()
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

		$afterUTC  = new DateTime( $after,  new DateTimeZone( Auth::user()->timezone ) ); $afterUTC->setTimezone(  new DateTimeZone('UTC') );
		$beforeUTC = new DateTime( $before, new DateTimeZone( Auth::user()->timezone ) ); $beforeUTC->setTimezone( new DateTimeZone('UTC') );
		$beforeUTC->add(new DateInterval('P1D'));

		$RESULT = [];

		#################################
		// Add request paramets to result
		$RESULT['daterange'] = [
			'after'    => Helper::sanitiseString($after),
			'before'   => Helper::sanitiseString($before),
			'timezone' => Auth::user()->timezone,
		];

		$timer = -microtime(true);

		###############################
		// Generate true booking prices

		/**
		 * # REASON OF COMMENTING #
		 * This logic has been moved to the Booking model. This commented code should remain
		 * here until all performance impact questions have been sufficently resolved.
		 */

		/**
		 * We are going to save the true booking prices (without fees) in this associative
		 * array, but only for bookings that have compulsory addons (fees).
		 */
		/*$realDiscountPercentage = [];

		$bookings = Booking::where('company_id', Auth::user()->id)
		    ->whereIn('status', ['confirmed'])
		    ->whereBetween('created_at', [$afterUTC, $beforeUTC])
		    ->whereHas('bookingdetails', function($query)
		    {
		    	$query->whereHas('addons', function($query)
		    	{
		    		$query->where('compulsory', 1);
		    	});
		    })
		    ->get();

		foreach ($bookings as $booking) {
			$feeSum = 0;
			foreach ($booking->bookingdetails as $detail) {
				foreach ($detail->addons as $addon) {
					if($addon->compulsory === 1)
						$feeSum += $addon->decimal_price;
				}
			}
			$realPrice = $booking->decimal_price - $feeSum;
			$realDiscountPercentage[$booking->id] = $realPrice / ($realPrice + $booking->discount);
		} */

		$counted_packagefacades = $counted_courses = [];

		  $RESULT['tickets']
		= $RESULT['packages']
		= $RESULT['courses']
		= $RESULT['addons']
		= $RESULT['fees']
		= $RESULT['accommodations'] = [];

		  $RESULT['tickets_total']
		= $RESULT['packages_total']
		= $RESULT['courses_total']
		= $RESULT['addons_total']
		= $RESULT['fees_total']
		= $RESULT['accommodations_total'] = ['quantity' => 0, 'revenue' => 0];

		$bookingdetails = Bookingdetail::with(
		    	'ticket',
		    	'departure',
		    	'packagefacade',
		    		'packagefacade.package',
		    	'course',
		    	'training_session',
		    	'addons'
		    )
		    ->whereHas('booking', function($query) use ($afterUTC, $beforeUTC)
		    {
		    	$query
		    	    ->where('company_id', Auth::user()->id)
		    	    ->whereIn('status', ['confirmed'])
		    	    ->whereBetween('created_at', [$afterUTC, $beforeUTC]);
		    })->get();

		$bookingdetails->load('booking.agent');


		foreach($bookingdetails as $detail)
		{
			if(!empty($detail->ticket_id) && !empty($detail->session_id) && empty($detail->packagefacade_id) && empty($detail->course_id))
			{
				### -------------------------------- ###
				### This is a directly booked ticket ###
				### -------------------------------- ###

				$detail->ticket->calculatePrice($detail->departure->start, $detail->created_at);

				$revenue = $detail->ticket->decimal_price;
				$model   = 'tickets';
				$name    = $detail->ticket->name;
				$id      = $detail->ticket->id;


			}
			elseif(empty($detail->packagefacade_id) && !empty($detail->course_id))
			{
				### -------------------------------- ###
				### This is a directly booked course ###
				### -------------------------------- ###

				$identifier = $detail->booking_id . '-' . $detail->customer_id . '-' . $detail->course_id;

				// Only continue, if the course has not been counted yet
				if(!in_array($identifier, $counted_courses))
				{
					$counted_courses[] = $identifier;

					// Find the first departure or training datetime that is booked in this course
					$bookingdetails = $detail->course->bookingdetails()
					    ->where('booking_id', $detail->booking_id)
					    ->where('customer_id', $detail->customer_id)
					    ->with('departure', 'training_session')
					    ->get();

					$firstDetail = $bookingdetails->sortBy(function($d)
					{
						if(!empty($d->departure))
							return $d->departure->start;
						else
							return $d->training_session->start;
					})->first();

					$start = !empty($firstDetail->departure) ? $firstDetail->departure->start : $firstDetail->training_session->start;

					// Calculate the course price at this first departure/training_session datetime
					$detail->course->calculatePrice($start, $detail->created_at);

					$revenue = $detail->course->decimal_price;
					$model   = 'courses';
					$name    = $detail->course->name;
					$id      = $detail->course->id;
				}
				else
					$model = null;
			}
			elseif(!empty($detail->packagefacade_id))
			{
				### ----------------- ###
				### This is a package ###
				### ----------------- ###

				// Only continue, if the package has not been counted yet
				if(!in_array($detail->packagefacade_id, $counted_packagefacades))
				{
					$counted_packagefacades[] = $detail->packagefacade_id;

					// Find the first departure datetime that is booked in this package
					$details = $detail->packagefacade->bookingdetails()->with('departure', 'training_session')->get();
					$firstDetail = $details->sortBy(function($detail)
					{
						if($detail->departure)
							return $detail->departure->start;
						else
							return $detail->training_session->start;
					})->first();

					if($firstDetail->departure)
						$start = $firstDetail->departure->start;
					else
						$start = $firstDetail->training_session->start;

					$accommodations = $detail->booking->accommodations()->wherePivot('packagefacade_id', $detail->packagefacade_id)->get();
					$firstAccommodation = $accommodations->sortBy(function($accommodation)
					{
						return $accommodation->pivot->start;
					})->first();

					if(!empty($firstAccommodation))
					{
						$detailStart = new DateTime($start);
						$accommStart = new DateTime($firstAccommodation->pivot->start);

						$start = ($detailStart < $accommStart) ? $detailStart : $accommStart;

						$start = $start->format('Y-m-d H:i:s');
					}

					// Calculate the package price at this first departure datetime and sum it up
					$detail->packagefacade->package->calculatePrice($start, $detail->created_at);

					$revenue = $detail->packagefacade->package->decimal_price;
					$model   = 'packages';
					$name    = $detail->packagefacade->package->name;
					$id      = $detail->packagefacade->package->id;
				}
				else
					$model = null;
			}
			else
			{
				### ---------------------------------------- ###
				### The detail does not fall into a category ###
				### ---------------------------------------- ###

				Log::write('ERROR: Unable to parse bookingdetail: ' . json_encode($detail));
				return Response::json(['errors' => ['A bookingdetail cannot be handled, as it doesn\'t fit the rules! Please check the log file to see what happened.']], 500); // 500 Internal Server Error
			}

			$realPricePercentage = ($detail->booking->real_decimal_price === null)
				? 1
				: $detail->booking->real_decimal_price / ($detail->booking->real_decimal_price + $detail->booking->discount);

			if(!empty($model))
			{
				### ---------------------------------- ###
				### Apply all special cases to revenue ###
				### ---------------------------------- ###

				// Apply percentage discount to price and sum up
				$revenue *= $realPricePercentage;

				// If booked through agent, subtract agent's commission
				if(!empty($detail->booking->agent))
				{
					$revenue *= (1 - $detail->booking->agent->commission / 100);
				}

				// Sum revenue and increase counter
				if(empty($RESULT[$model][$id])) $RESULT[$model][$id] = ['name' => $name, 'quantity' => 0, 'revenue' => 0];

				$RESULT[$model][$id]['quantity']++;
				$RESULT[$model][$id]['revenue'] += round($revenue, 2);

				$RESULT[$model . '_total']['quantity']++;
				$RESULT[$model . '_total']['revenue'] += round($revenue, 2);
			}

			### -------------------------------------------- ###
			### Sum up addons that are not part of a package ###
			### -------------------------------------------- ###

			// (packages would have been caught above, because packaged addons are only allowed on tickets of the same package)
			foreach($detail->addons as $addon)
			{
				if(!empty($addon->pivot->packagefacade_id)) continue;

				if($addon->compulsory)
				{
					// Handle as a fee

					// Sum revenue and increase counter
					if(empty($RESULT['fees'][$addon->id])) $RESULT['fees'][$addon->id] = ['name' => $addon->name, 'quantity' => 0, 'revenue' => 0];

					$RESULT['fees'][$addon->id]['quantity']++;
					$RESULT['fees'][$addon->id]['revenue'] += round($addon->decimal_price, 2);

					$RESULT['fees_total']['quantity']++;
					$RESULT['fees_total']['revenue'] += round($addon->decimal_price, 2);
				}
				else
				{
					// Handle as regular addon

					// Apply percentage discount to price and sum up
					$revenue = $addon->decimal_price * $addon->pivot->quantity * $realPricePercentage;

					// If booked through agent, subtract agent's commission
					if(!empty($detail->booking->agent))
					{
						$revenue = $revenue * (1 - $detail->booking->agent->commission / 100);
					}

					// Sum revenue and increase counter
					if(empty($RESULT['addons'][$addon->id])) $RESULT['addons'][$addon->id] = ['name' => $addon->name, 'quantity' => 0, 'revenue' => 0];

					$RESULT['addons'][$addon->id]['quantity'] += $addon->pivot->quantity;
					$RESULT['addons'][$addon->id]['revenue'] += round($revenue, 2);

					$RESULT['addons_total']['quantity'] += $addon->pivot->quantity;
					$RESULT['addons_total']['revenue'] += round($revenue, 2);
				}
			}
		}

		### --------------------- ###
		### Sum up accommodations ###
		### --------------------- ###

		$accommodations = Accommodation::whereHas('bookings', function($query) use ($afterUTC, $beforeUTC)
		{
			$query
			    ->where('company_id', Auth::user()->id)
			    ->whereIn('status', ['confirmed'])
			    ->whereBetween('bookings.created_at', [$afterUTC, $beforeUTC]);
		})->get();

		$accommodations->load('bookings.agent');

		foreach($accommodations as $accommodation)
		{
			foreach($accommodation->bookings as $booking)
			{
				// Only continue if the accommodation is not part of a package
				if(empty($booking->pivot->packagefacade_id))
				{
					$accommodation->calculatePrice(
						$booking->pivot->start,
						$booking->pivot->end,
						$booking->pivot->created_at
					);

					$realPricePercentage = ($booking->real_decimal_price === null)
						? 1
						: $booking->real_decimal_price / ($booking->real_decimal_price + $booking->discount);

					// Apply percentage discount to price and sum up
					$revenue = $accommodation->decimal_price * $realPricePercentage;

					// If booked through agent, subtract agent's commission
					if(!empty($booking->agent))
					{
						$revenue = $revenue * (1 - $booking->agent->commission / 100);
					}

					// Sum revenue and increase counter
					if(empty($RESULT['accommodations'][$accommodation->id])) $RESULT['accommodations'][$accommodation->id] = ['name' => $accommodation->name, 'quantity' => 0, 'revenue' => 0];

					$RESULT['accommodations'][$accommodation->id]['quantity']++;
					$RESULT['accommodations'][$accommodation->id]['revenue'] += round($revenue, 2);

					$RESULT['accommodations_total']['quantity']++;
					$RESULT['accommodations_total']['revenue'] += round($revenue, 2);
				}
			}
		}

		$timer += microtime(true);
		$RESULT['execution_time'] = round($timer * 1000, 3);

		return $RESULT;
	}
}
