<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

/**
 * This controller lets you retrieve and save all data concerning companies. It will only return data for the logged in company. The methods in this controller require authentication.
 *
 * For documentation, please refer to http://scubawhere.com/docs
 */
class CompanyController extends Controller {

	public function getIndex()
	{
        return Auth::user();
	}

	public function postUpdate()
	{
		$data = Input::only('username', 'contact', 'description', 'email', 'name', 'address_1', 'address_2', 'city', 'county', 'postcode',/* 'country_id', 'currency_id',*/ 'business_phone', 'business_email', 'vat_number', 'registration_number', 'phone', 'website', 'terms');

		if( Input::has('address_1') || Input::has('address_2') || Input::has('postcode') || Input::has('city') || Input::has('county') )
		{
			$country = Auth::user()->country;

			$address = urlencode( implode( ',', array(
				$data['address_1'],
				$data['address_2'],
				$data['postcode'],
				$data['city'],
				$data['county'],
				$country->name,
			) ) );

			$googleAPIKey = 'AIzaSyDBX2LjGDdq2QlaGq0UJ9RcEHYdodJXCWk';

			$latLng = 'https://maps.googleapis.com/maps/api/geocode/xml?address='.$address.'&key='.$googleAPIKey;
			$latLng = simplexml_load_file($latLng);

			if((string) $latLng->status === "OK")
			{
				$data['latitude']  = $latLng->result->geometry->location->lat;
				$data['longitude'] = $latLng->result->geometry->location->lng;

				$timezone = 'https://maps.googleapis.com/maps/api/timezone/xml?location='.$data['latitude'].','.$data['longitude'].'&timestamp='.time().'&key='.$googleAPIKey;
				$timezone = simplexml_load_file($timezone);

				if((string) $timezone->status === "OK")
					$data['timezone'] = $timezone->time_zone_id;
				else
					return Response::json( array('errors' => array('Sorry, we could not determine your timezone.')), 406 ); // 406 Not Acceptable
			}
			else
			{
				return Response::json( array('errors' => array('Sorry, we could not find the specified address.')), 406 ); // 406 Not Acceptable
			}
		}

		$company = Auth::user();

		// Mass assigned insert with automatic validation
		$company->fill($data);
		if($company->updateUniques())
		{
			if( Input::has('agencies') )
				$company->agencies()->sync( Input::get('agencies') );

			return array('status' => 'OK. Company data updated', 'company' => $company);
		}
		else
		{
			return Response::json( array('errors' => $company->errors()->all()), 406 ); // 406 Not Acceptable
		}
	}

	public function getLocations()
	{
		$area = Input::get('area'); // Structure: [north, west, south, east]
		if( is_array($area) )
		{
			$north = $area[0];
			$west  = $area[1];
			$south = $area[2];
			$east  = $area[3];

			// Cater for the southern hemisphere
			if( $north > $south )
			{
				$north = $area[2];
				$south = $area[0];
			}

			$locations = Location::whereBetween('latitude',  array($north, $south))
			                     ->whereBetween('longitude', array($west, $east))
			                     ->with('tags')
			                     ->get();
		}
		else
		{
			$data = Input::only('latitude', 'longitude', 'limit');

			$validator = Validator::make( $data, array(
				'latitude'  => 'required|numeric|between:-90,90',
				'longitude' => 'required|numeric|between:-180,180',
				'limit'     => 'integer|min:1'
			) );

			if( $validator->fails() )
				return Response::json( array('errors' => $validator->messages()->all()), 406 ); // 406 Not Acceptable

			$lat   = $data['latitude'];
			$lon   = $data['longitude'];
			$limit = empty($data['limit']) ? 5 : $data['limit'];

			$timer = microtime(true);
			$locations = DB::table('locations')
			                 ->select(DB::raw('*, ((ACOS(SIN('.$lat.' * PI() / 180) * SIN(latitude * PI() / 180) + COS('.$lat.' * PI() / 180) * COS(latitude * PI() / 180) * COS(('.$lon.' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance'))
			                 ->with('tags')
			                 ->orderBy('distance', 'asc')
			                 ->take($limit)
			                 ->get();
			$timer = round( ( microtime(true) - $timer ) * 1000, 3 );

			Log::info('The locations query took '.$timer.' ms to execute.('.$lat.', '.$lon.', '.$limit.')');

		}

		return $locations;
	}

	public function postAddLocation()
	{
		$data = Input::only('name', 'description', 'latitude', 'longitude');

		$tags = Input::get('tags', false);
		if( !$tags || empty($tags) )
			$tags = false;

		$location = new Location($data);

		if( !$location->save() )
		{
			return Response::json( array('errors' => $location->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Sync tags
		if($tags)
		{
			try
			{
				$location->tags()->sync($tags);
			}
			catch(Exeption $e)
			{
				return Response::json( array('errors' => array('Could not assign tags to location, \'tags\' array is propably erroneous.')), 400 ); // 400 Bad Request
			}
		}

		// Automatically attach location to the company
		Auth::user()->locations()->attach( $location->id );

		return Response::json( array('status' => 'OK. Location created', 'id' => $location->id), 201 ); // 201 Created
	}

	public function postInitialise()
	{
		$company = Auth::user();
		$company->fill(array('initialised' => 1));
		if(!$company->updateUniques())
			return Response::json( array('errors' => $company->errors()->all()), 406 );

		return array('status' => 'OK. Company initialised');
	}

	public function getPickUpSchedule()
	{
		$date = Input::get('date');

		if(empty($date))
			return Response::json(['errors' => ['A date is required.']], 406); // 406 Not Acceptable

		$date = new DateTime($date);
		$date = $date->format('Y-m-d');

		$bookings = Auth::user()->bookings()->with('lead_customer')
		    ->whereNotNull('pick_up_location')
		    ->where('pick_up_date', $date)
		    ->whereIn('status', Booking::$counted)
		    ->orderBy('pick_up_time')
		    ->get();

		foreach($bookings as &$booking)
		{
			$booking->number_of_customers = $booking->customers()->distinct()->count();
		}

		return ['date' => $date, 'bookings' => $bookings];
	}

	public function postFeedback() {
		$data = Input::only('tab', 'feature', 'issue');

		if(empty($data['tab'] && $data['issue']))
			return Response::json(['errors' => ['A tab and issue is required.']], 406); // 406 Not Acceptable

		Mail::send('emails.feedback', array('company' => Auth::user(), 'feedback' => $data), function($message) {
			$message->to('thomas@scubawhere.com', 'Thomas Paris')->subject('Feedback');
		});
	}

	public function postHeartbeat() {
		## Set up file
		$path = storage_path() . '/logs';

		if (!file_exists($path))
		{
			// Directory doesn't exist, try to create it
			if (!mkdir($path, 0700, true))
				throw new \Exception('Directory "' . $path . '" cannot be created.');

			// Create default .gitignore, to ignore stored log files
			file_put_contents($path . '/.gitignore', "*.log\n");
		}

		if (!is_writable($path))
			throw new \Exception('Path "' . $path . '" is not writable.');

		$file = $path . '/heartbeats.log';

		## Set up log line
		$line = array();

		// Add server timestamp
		array_push($line, date('Y-m-d H:i:s'));

		// Add user ID
		array_push($line, Auth::user()->id);

		// Add identifier of navigation event vs. automated heartbeat (or '-' if not specified)
		if(Input::has('n'))
		{
			array_push($line, 'n');
			array_push($line, Input::get('route', '-'));
		}
		elseif(Input::has('h'))
		{
			array_push($line, 'h');
			array_push($line, Input::get('route', '-'));
		}
		else
		{
			array_push($line, '- -');
		}

		// Add IP address
		array_push($line, Request::getClientIp());

		## Write log
		file_put_contents($file, implode(' ', $line)."\n", FILE_APPEND | LOCK_EX);
	}

	public function getNotifications($from = 0, $take = 20) {

		$NOTIFICATIONS = [];

		if(!Auth::user()->initialised) $NOTIFICATIONS['init'] = 'Please start the tour!';

		$bookings = Auth::user()->bookings()
			->orderBy('id', 'DESC')
			->skip($from)
			->take($take)
			->get();

		$overdue = array();
		$expiring = array();

		foreach($bookings as $booking) {

			$amountPaid = 0;
			foreach($booking->payments as $payment) {
				$amountPaid += $payment->amount;
			}

			$divingDate = new DateTime( $booking->arrival_date, new DateTimeZone( Auth::user()->timezone ) );

			/* Get all bookings that have overdue payments */
			if($booking->price > $amountPaid && $divingDate > new DateTime('now', new DateTimeZone( Auth::user()->timezone ))) {
				array_push($overdue, array($booking->reference, $booking->price - $amountPaid));
			}

			/* Get all booking that expire within 30 minutes */
			$reservedDate = new DateTime( $booking->reserved, new DateTimeZone( Auth::user()->timezone ) );
			if( $reservedDate < new DateTime('+30 minutes', new DateTimeZone( Auth::user()->timezone ))
					&&  $reservedDate > new DateTime('now', new DateTimeZone( Auth::user()->timezone ))) {
				array_push($expiring, array($booking->reference, $booking->reserved));
			}

		}
		
		$NOTIFICATIONS['overdue'] = $overdue;
		$NOTIFICATIONS['expiring'] = $expiring;

		return $NOTIFICATIONS;
	}
}
