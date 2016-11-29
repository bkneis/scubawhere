<?php

use Scubawhere\Helper;
use Scubawhere\CrmMailer;
use Scubawhere\Services\ObjectStoreService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RegisterController extends Controller {

	public function __construct(ObjectStoreService $obj_store_service)
	{
		$this->object_store_service = $obj_store_service;
	}

	// This is a hotfix for the cron jobs and will be removed once our worker tier is finished
	public function getDeleteExpiredBookings()
	{
		// Code goes here	
		$bookings = DB::select(
			DB::raw("SELECT bookings.id, bookings.reserved_until, bookings.status, companies.timezone 
					 FROM bookings 
					 JOIN companies WHERE bookings.company_id = companies.id 
					 AND bookings.status IN ('initialised', 'reserved');"
		));

		$ids_abandoned = array();
		$ids_expired   = array();

		foreach($bookings as $obj) 
		{
			$now = new DateTime('now', new DateTimeZone($obj->timezone));
			$test = new DateTime($obj->reserved_until, new DateTimeZone($obj->timezone));
			if($test < $now)
			{
				$obj->status === 'initialised' ? array_push($ids_abandoned, $obj->id) : array_push($ids_expired, $obj->id);
			}
		}

		if(count($ids_abandoned) > 0)
			DB::table('bookings')->whereIn('id', $ids_abandoned)->delete();
		
		if(count($ids_expired) > 0)
			DB::table('bookings')->whereIn('id', $ids_expired)->update(array('status' => 'expired'));

		return Response::json(
			array('status' => 'OK.', 
				'num_deletes' => count($ids_abandoned), 
				'num_expired' => count($ids_expired)
			));

	}
	
	public function postCompany()
	{
		$data = Input::only(
			'contact',
			'name'
		);

		$userData = Input::only(
			'username',
			'email',
			'phone'
		);

		$today = new DateTime();
		$trial_date = $today->add(new \DateInterval('P1M'));
		//$renewal_date = $today->add(new \DateInterval('P1Y'));

		$credit_data = array('trial_date' => $trial_date->format('Y-m-d H:i:s'));
		//$credit_data = array('renewal_date' => $renewal_date->format('Y-m-d H:i:s'));

		$password = Hash::make(Input::get('password'));

		$company = new Company($data);
		$company->verified = 1;
		$user    = new User($userData);
		$user->password = $password;
		$credit  = new Credit($credit_data);

		// Mass assigned insert with automatic validation
		if(!$company->validate())
			return Response::json( array('errors' => $company->errors()->all()), 406 ); // 406 Not Acceptable

		if(!$user->validate())
			return Response::json( array('errors' => $user->errors()->all()), 406 ); // 406 Not Acceptable

		if(!$credit->validate())
			return Response::json( array('errors' => $credit->errors()->all()), 406 ); // 406 Not Acceptable

		$company->save();

		$user = $company->users()->save($user);
		$credit = $company->users()->save($credit);

		// Company and User have been created successfully

		// Send notification to Slack if production RMS
		//if(gethostname() === 'rms.scubawhere.com')
		//if(isset($_SERVER['AWS_ENV']))
		//{
			/*Slack::attach([
				'color' => 'good',
				'fields' => [
				[
					'title' => 'Name',
					'value' => $company->name,
					'short' => true
				],
				[
					'title' => 'Contact',
					'value' => $company->contact . ': ' . $company->business_email,
					'short' => true
				]
				]
			])->send('New RMS registration! :smiley:');*/
		//}
		//
		if(isset($_SERVER['AWS_ENV']))
		{
			$settings = [
				 'username' => null,
				 'channel' => null,
				 'link_names' => false,
				 'fields' => [
				 [
					'title' => 'Name',
					'value' => $company->name,
					'short' => true
				 ],
				 [
					'title' => 'Contact',
					'value' => $company->contact . ': ' . $company->business_email,
					'short' => true
				 ]
				 ]
			];

			$client = new Maknz\Slack\Client('https://hooks.slack.com/services/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', $settings);

			$client->send('New RMS registration!');
			$client->send('Company Name : ' . $company->name . ',  Contact Name : ' . $company->contact);
			$client->send('Email Address : ' . $user->email . ',  Phone number : ' . $user->phone);
		}

		//CrmMailer::sendRegisterConf($user); // todo use this without context
		return Response::json(
					array('status' => 
						'Ok. Company and user created'
					), 201);
	}

	// @note this kind of feel more appropriate in the company controller, but when a user registers they are not authenticated yet ...
	/**
	 * API Function to save a DO's terms and conditions
	 * @return ResponseObject 200 if the file has been saved or a 406 if the file was not retrieved from the request params
	 */
	public function postUploadTerms()
	{
		$this->object_store_service->uploadTerms(Input::file('terms_file'));

		return Response::json(array('status' => 'Your terms have been uploaded'), 200);
	}

	public function getExists()
	{
		$field = Input::get('field');
		$value = Input::get('value');

		switch($field)
		{
			case 'username':
				$exists = Company::where('username', '=', $value)->exists();
				break;
			case 'email':
				$exists = Company::where('email', '=', $value)->exists();
				break;
			case 'business_email':
				$exists = Company::where('business_email', '=', $value)->exists();
				break;
			default:
				return Response::json( array('errors' => array('Field name not supported.')), 406 ); // 406 Not Acceptable
		}

		return $exists ? 1 : 0;
	}

}



/*
 *
	public function postCompany()
	{
		$data = Input::only(
			'contact',
			'description',
			'name',
			'address_1',
			'address_2',
			'city',
			'county',
			'postcode',
			'country_id',
			'currency_id',
			'business_phone',
			'business_email',
			'vat_number',
			'registration_number',
			'website'
		);

		$userData = Input::only(
			'username',
			'email',
			'phone'
		);

		// Check for the acceptance of scubawhereRMS' terms & conditions
		$terms = Input::get('our_terms', false);
		if(empty($terms))
			return Response::json(['errors' => ['Please accept scubawhereRMS\' terms & conditions']], 412); // 412 Precondition Failed

		// Find the country
		try
		{
			if( !Input::get('country_id') ) throw new ModelNotFoundException();
			$country = Country::findOrFail( Input::get('country_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The country could not be found.')), 404 ); // 404 Not Found
		}

		// Find the address, latLng and timezone with Google
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

			if((string) $timezone->status === "OK")
				$data['timezone'] = (string) $timezone->timeZoneId;
			else
				return Response::json( array('errors' => array('Sorry, Google could not determine your timezone.')), 406 ); // 406 Not Acceptable
		}
		else
		{
			return Response::json( array('errors' => array('Sorry, Google could not find the specified address.')), 406 ); // 406 Not Acceptable
		}

		$renewal_date = Helper::localTime()->add(new \DateInterval('P1Y'));

		$credit_data = array('renewal_date' => $renewal_date->format('Y-m-d H:i:s'));

		$company = new Company($data);
		$user    = new User($userData);
		$credit  = new Credit($credit_data);

		// Mass assigned insert with automatic validation
		if(!$company->validate())
			return Response::json( array('errors' => $company->errors()->all()), 406 ); // 406 Not Acceptable

		if(!$user->validate())
			return Response::json( array('errors' => $user->errors()->all()), 406 ); // 406 Not Acceptable

		if(!$credit->validate())
			return Response::json( array('errors' => $credit->errors()->all()), 406 ); // 406 Not Acceptable

		$company->save();

		$user = $company->users()->save($user);
		$credit = $company->users()->save($credit);

		// Company and User have been created successfully

		$company->agencies()->sync( Input::get('agencies', []) );

		// Send notification to Slack if production RMS
		if(gethostname() === 'rms.scubawhere.com')
		{
			Slack::attach([
				'color' => 'good',
				'fields' => [
				[
					'title' => 'Name',
					'value' => $company->name,
					'short' => true
				],
				[
					'title' => 'Contact',
					'value' => $company->contact . ': ' . $company->business_email,
					'short' => true
				]
				]
			])->send('New RMS registration! :smiley:');
		}

		// $originalInput = Request::input();

		CrmMailer::sendRegisterConf($user);
		/*$request = Request::create('password/remind', 'POST', array('email' => $user->email, 'welcome' => 1));
		Request::replace($request->input());
		return Route::dispatch($request); */

		// Request::replace($originalInput);
	//}
