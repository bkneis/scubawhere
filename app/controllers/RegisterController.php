<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RegisterController extends Controller {

	public function postCompany()
	{
		$data = Input::only('username', 'contact', 'description', 'email', 'name', 'address_1', 'address_2', 'city', 'county', 'postcode', 'country_id', 'currency_id', 'business_phone', 'business_email', 'vat_number', 'registration_number', 'phone', 'website');

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
		$ch = curl_init( 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec( $ch );
		curl_close( $ch );
		$result = json_decode( $result );

		if($result->status === "OK")
		{
			$data['latitude']  = $result->results[0]->geometry->location->lat;
			$data['longitude'] = $result->results[0]->geometry->location->lng;
		}
		else
		{
			$data['latitude']  = 0;
			$data['longitude'] = 0;
		}

		$company = new Company($data);

		// Mass assigned insert with automatic validation
		if($company->save())
		{
			$request = Request::create('password/remind', 'POST', array('email' => $company->email));
			return Route::dispatch($request);
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
			default:
				return Response::json( array('errors' => array('Field name not supported.')), 406 ); // 406 Not Acceptable
		}

		return $exists ? 1 : 0;
	}

}
