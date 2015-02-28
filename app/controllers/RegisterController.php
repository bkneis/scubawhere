<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RegisterController extends Controller {

	public function postCompany()
	{
		$data = Input::only(
			'username',
			'contact',
			'description',
			'email',
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
			'phone',
			'website',
			'terms'
		);

		try
		{
			if( !Input::get('country_id') ) throw new ModelNotFoundException();
			$country = Country::findOrFail( Input::get('country_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The country could not be found.')), 404 ); // 404 Not Found
		}

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

		$company = new Company($data);

		// Mass assigned insert with automatic validation
		if($company->save())
		{
			$company->agencies()->sync( Input::get('agencies') );

			$originalInput = Request::input();
			$request = Request::create('password/remind', 'POST', array('email' => $company->email, 'welcome' => 1));
			Request::replace($request->input());
			return Route::dispatch($request);
			Request::replace($originalInput);
		}
		else
		{
			return Response::json( array('errors' => $company->errors()->all()), 406 ); // 406 Not Acceptable
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
