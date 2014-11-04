<?php

class RegisterController extends Controller {


	public function postCompany()
	{
		$data = Input::only('username', 'email', 'name', 'address_1', 'address_2', 'city', 'county', 'postcode', 'country_id', 'business_phone', 'business_email', 'vat_number', 'registration_number', 'phone', 'website');
		// add all otyher fields in companies table
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
