<?php

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Entities\Country;
use Scubawhere\Entities\Booking;
use Scubawhere\Entities\Location;
use Scubawhere\Repositories\UserRepo;
use Scubawhere\Services\CreditService;
use Scubawhere\Services\DomainService;
use Scubawhere\Services\ObjectStoreService;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class CompanyController extends Controller {

	protected $credit_service;
	protected $object_store_service;

	public function __construct(CreditService $credit_service,
								ObjectStoreService $object_store_service,
								DomainService $domain_service,
								UserRepo $user_repo)
	{
		$this->credit_service       = $credit_service;
		$this->object_store_service = $object_store_service;
		$this->domain_service       = $domain_service;
		$this->user_repo            = $user_repo;
	}

	public function getIndex()
	{
		return Context::get();
	}

	public function postUpdate()
	{
		$data = Input::only('contact', 'description', 'reference_base', 'name', 'address_1', 'address_2', 'city', 'county', 'postcode','country_id', 'currency_id', 'business_phone', 'business_email', 'vat_number', 'registration_number', /*'phone',*/ 'website', 'alias');

		if(Input::has('lat')) {
			$googleAPIKey = 'AIzaSyDBX2LjGDdq2QlaGq0UJ9RcEHYdodJXCWk';

			$data['latitude']  = (double) Input::get('lat');
			$data['longitude'] = (double) Input::get('lng');

			$timezone = 'https://maps.googleapis.com/maps/api/timezone/json?location='.$data['latitude'].','.$data['longitude'].'&timestamp='.time().'&key='.$googleAPIKey;
			$timezone = file_get_contents($timezone);
			$timezone = json_decode($timezone);

			if((string) $timezone->status === "OK") {
				$data['timezone'] = (string) $timezone->timeZoneId;
			}
			else {
				return Response::json( array('errors' => array('Sorry, Google could not determine your timezone.')), 406 );
			}
		} elseif( Input::has('address_1') || Input::has('address_2') || Input::has('postcode') || Input::has('city') || Input::has('county') )
		{
			//$country = Context::get()->country;
			$country = Country::findOrFail($data['country_id']);

			$address = urlencode( implode( ',', array(
				$data['address_1'],
				$data['address_2'],
				$data['postcode'],
				$data['city'],
				$data['county'],
				$country->name,
			) ) );

			$googleAPIKey = 'AIzaSyDBX2LjGDdq2QlaGq0UJ9RcEHYdodJXCWk';

			$latLng = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='.$googleAPIKey;
			$latLng = file_get_contents($latLng);
			$latLng = json_decode($latLng);

			if((string) $latLng->status === "OK")
			{
				$data['latitude']  = (double) $latLng->results[0]->geometry->location->lat;
				$data['longitude'] = (double) $latLng->results[0]->geometry->location->lng;

				$timezone = 'https://maps.googleapis.com/maps/api/timezone/json?location='.$data['latitude'].','.$data['longitude'].'&timestamp='.time().'&key='.$googleAPIKey;
				$timezone = file_get_contents($timezone);
				$timezone = json_decode($timezone);

				if((string) $timezone->status === "OK") {
					$data['timezone'] = (string) $timezone->timeZoneId;
				}
				else {
					if (is_null(Context::get()->timezone)) {
						return Response::json( array('errors' => array('Sorry, Google could not determine your timezone.')), 406 ); // 406 Not Acceptable
					}
				}
			}
			else {
				if (is_null(Context::get()->timezone)) {
					return Response::json( array('errors' => array('Sorry, Google could not determine your timezone.')), 406 ); // 406 Not Acceptable
				}
				//return Response::json( array('errors' => array('Sorry, Google could not find the specified address.')), 406 ); // 406 Not Acceptable
			}
		} else {
			return Response::json( array('errors' => array("Please either enter your address or click 'cant find your address?' and click on your location.")), 406 ); // 406 Not Acceptable
		}

		$company = Context::get();

		if(is_null($company->alias)) {
			$this->postSetSubdomain($data['alias']);
		}

		if(is_null($data['alias'])) {
			unset($data['alias']);
		}

		// Mass assigned insert with automatic validation
		$company = $company->fill($data);
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
		Context::get()->locations()->attach( $location->id );

		return Response::json( array('status' => 'OK. Location created', 'id' => $location->id), 201 ); // 201 Created
	}

	public function postInitialise()
	{
		$company = Context::get();
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

		$pick_ups = Context::get()->pick_ups()->with('booking', 'booking.lead_customer')
		    ->where('date', $date)
		    ->whereHas('booking', function($query)
		    {
		    	$query->whereIn('status', Booking::$counted);
		    })
		    ->orderBy('time')
		    ->get();

		/*
		foreach($pick_ups as &$pick_up)
		{
			$pick_up->booking->setNumberOfCustomersAttribute();
		}
		*/

		return ['date' => $date, 'pick_ups' => $pick_ups];
	}

	public function postFeedback() {
		$data = Input::only('tab', 'feature', 'issue');

		if(empty($data['tab'] && $data['issue']))
			return Response::json(['errors' => ['A tab and issue is required.']], 406); // 406 Not Acceptable

		Mail::send('emails.feedback', array('company' => Context::get(), 'feedback' => $data), function($message) {
			$message->to('support@scubawhere.com', 'Support')->subject('scubawhereRMS Feedback');
		});
	}

	public function postEmail() {
		$data = Input::only('to', 'subject', 'customer_name', 'message');

		if(empty($data['message'] && $data['subject']))
			return Response::json(['errors' => ['A message and subject is required.']], 406); // 406 Not Acceptable

		$company = Context::get();

		Mail::send('emails.customerEmail', array('company' => $company, 'data' => $data), function($message) use ($data, $company) {
			$message->to($data['to'], $data['customer_name'])
					->subject($data['subject'])
					->from($company->business_email, $company->name);
			// $message->to('thomas@scubawhere.com', 'Thomas Paris')->subject('Feedback');
		});
	}

	/**
	 * info
	 * tab, time, button_clicks, mouse_movement
	 */
	public function postUsageInfo()
	{
		$data = Input::only('info');

		$data['ip'] = Request::getClientIp();

		$this->usageService->log($data);

		return Resonse::json(200);
	}

	public function postHeartbeat()
	{
		## Set up file
		//$path = storage_path() . '/logs';

		/*if (!file_exists($path))
		{
			// Directory doesn't exist, try to create it
			if (!mkdir($path, 0700, true))
				throw new \Exception('Directory "' . $path . '" cannot be created.');

			// Create default .gitignore, to ignore stored log files
			file_put_contents($path . '/.gitignore', "*.log\n");
		}*/

		//if (!is_writable($path))
			//throw new \Exception('Path "' . $path . '" is not writable.');

		//$file = $path . '/heartbeats.log';

		$file = storage_path() . '/logs/heartbeats.log';

		// Get the log file url from s3
		$log_url = $this->object_store_service->getHeartbeatsLogUrl();
		// Download the log file and save to $file
		file_put_contents($file, fopen($log_url, 'r'));		

		## Set up log line
		$line = array();

		// Add server timestamp
		array_push($line, date('Y-m-d H:i:s'));

		// Add user ID
		array_push($line, Context::get()->id);

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

		// Upload the file to s3
		$this->object_store_service->uploadHeartbeatsLog();
	}

	public function getNotifications()
	{

		$NOTIFICATIONS = [];

		$currency = new PhilipBrown\Money\Currency( Context::get()->currency->code );

		if(!Context::get()->initialised)
			$NOTIFICATIONS['init'] = 'Please start the tour!';

		// TODO Possible performance problem because this query gets ALL counted bookings?
		$bookings = Context::get()->bookings()
			->with('payments', 'refunds')
			->whereIn('status', Booking::$counted)
			->orderBy('id', 'DESC')
			->get();

		// TODO What about agent bookings? Should they show up as well?

		$outstandingPayments = array();
		$expiring = array();

		$localNow = Helper::localTime();
		$localNow->setTime(0, 0, 0); // Set localNow datetime to the start of the day, so "today's" trips get returned as well.

		$in24Hours = new DateTime('+24 hours', new DateTimeZone( Context::get()->timezone ));

		foreach($bookings as $booking)
		{
			$amountPaid = 0.0; // decimal

			foreach($booking->payments as $payment)
			{
				$amountPaid += $payment->amount;
			}
			foreach($booking->refunds as $refund)
			{
				$amountPaid -= $refund->amount;
			}

			$arrivalDate = new DateTime($booking->arrival_date, new DateTimeZone( Context::get()->timezone ));

			/* Get all bookings that have outstanding payments */
			$amountDue = $booking->price / $currency->getSubunitToUnit() - $amountPaid;
			if($amountDue > 0 && $arrivalDate > $localNow)
			{
				array_push($outstandingPayments, array($booking->reference, $amountDue));
			}

			/* Get all booking that expire within the next 24 hours */
			if($booking->reserved_until !== null)
			{
				$reservedDate = new DateTime($booking->reserved_until, new DateTimeZone( Context::get()->timezone ));

				if($reservedDate < $in24Hours && $reservedDate > $localNow)
				{
					array_push($expiring, array($booking->reference, $booking->reserved_until));
				}
			}
		}

		$NOTIFICATIONS['overdue']  = $outstandingPayments;
		$NOTIFICATIONS['expiring'] = $expiring;

		return $NOTIFICATIONS;
	}

	/**
	 * @return array Credit usage data about the RMS user
	 * example  
	 * {
     * 		renewal_date    : '2016-08-22',
	 *		booking_credits : { used : 14, total : 200 }
	 *		email_credits   : { used : 450, total : 20000 }
	 * }
	 */
	public function getCredits()
	{
		return Response::json($this->credit_service->getCredits(), 200);
	}

	/**
	 * Create the company's subdomain for the front facing portal
	 *
	 * @api /company/set-subdomain
	 * @param string $subdomain
	 * @return mixed
	 * @throws HttpPreconditionFailed
	 * @throws HttpUnprocessableEntity
	 */
	public function postSetSubdomain($subdomain)
	{
		//$subdomain = Input::get('subdomain');
		if(is_null($subdomain)) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The sub domain field is required']);
		}
		$this->domain_service->createSubdomain($subdomain);
		return Response::json(array('status' => 'OK. Your subdomain has been created'), 200);
	}

	/**
	 * Retrieve all of the users related to a company
	 *
	 * @api GET /company/users
	 * @return \Illuminate\Http\Response
	 */
	public function getUsers()
	{
		$users = $this->user_repo->getUsersInContext();

		return Response::json(array(
			'status' => 'success',
			'data' => array('users' => $users)
		, 200));
	}

	public function postLogo()
	{
		$logo = Input::file('company_logo');
		$this->object_store_service->uploadLogo($logo);
		
		$data = array('logoExt' => $logo->getClientOriginalExtension());
		
		if (!Context::get()->update($data)) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The file format was not valid']);
		}

		return Response::json(array('status' => 'Your company logo has been uploaded'), 200);
	}
}
