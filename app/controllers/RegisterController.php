<?php

class RegisterController extends Controller {


	public function postCompany()
	{
		$data = Input::only('username', 'email', 'name', 'address_1', 'address_2', 'city', 'county', 'postcode', 'country_id', 'phone', 'website');

		$company = new Company($data);

		// Mass assigned insert with automatic validation
		if( $company->validate() )
		{
			$company->save();

			// Connect the selected agencies
			if( Input::has('agencies') )
			{
				$agencies = Input::get('agencies');
				if( is_array($agencies) )
					$company->agencies()->sync($agencies);
			}

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
