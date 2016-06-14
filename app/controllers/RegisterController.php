<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RegisterController extends Controller {

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

		$company = new Company($data);
		$user    = new User($userData);

		// Mass assigned insert with automatic validation
		if(!$company->validate())
			return Response::json( array('errors' => $company->errors()->all()), 406 ); // 406 Not Acceptable

		if(!$user->validate())
			return Response::json( array('errors' => $user->errors()->all()), 406 ); // 406 Not Acceptable

		$company->save();

		$user = $company->users()->save($user);

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
		$request = Request::create('password/remind', 'POST', array('email' => $user->email, 'welcome' => 1));
		Request::replace($request->input());
		return Route::dispatch($request);
		// Request::replace($originalInput);
	}

	// @note this kind of feel more appropriate in the company controller, but when a user registers they are not authenticated yet ...
	/**
	 * API Function to save a DO's terms and conditions
	 * @return ResponseObject 200 if the file has been saved or a 406 if the file was not retrieved from the request params
	 */
	public function postUploadTerms()
	{
		// @todo add a check to ensure it is pdf
		//$data = Input::all();
		//dd($data);
		if(Input::hasFile('terms_file'))
		{
			if (Input::file('terms_file')->isValid())
			{
				Input::file('terms_file')->move(storage_path() . '/scubawhere/' . Input::get('company_name') . '/', 'terms.pdf');
			}
			else
			{
				return Response::json(array('errors' => 'Error : Please upload a valid pdf'), 406);
			}
		}
		else
		{
			return Response::json(array('errors' => 'Error : Please upload a pdf to save as your terms and conditions'), 406);
		}
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
