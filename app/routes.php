<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	if (Auth::viaRemember())
	{
		// If user is remembered as logged in, redirect to dashboard
		return Redirect::to('dashboard/');
	}

	return Redirect::to('blog/');
});

Route::get('secret_phpinfo', function()
{
	phpinfo();
});

// Route::controller('test', 'TestController');

Route::controller('password', 'PasswordController');

Route::controller('register', 'RegisterController');

Route::post('login', function()
{
	$credentials = Input::only('username', 'password', 'remember');

	if(Auth::attempt(
		array(
			'username' => Input::get('username'),
			'password' => Input::get('password'),
			'verified' => true
		), Input::get('remember') ))
	{
		// Login successfull!
		$user = Auth::user();

		// TODO Regenerate token (maybe even on every new POST request?)

		// Check to see if the password needs to be re-hashed (when the hash technique is different from when originally saved)
		if ( Hash::needsRehash($user->password) ) {
			$user->password = Hash::make( Input::get('password') );
			$user->updateUniques();
		}

		// Update the updated_at timestamp in the table at each login
		$user->touch();

		return Response::json( array('status' => 'Login successfull. Welcome!'), 202 ); // 202 Accepted
	}
	else
	{
		try
		{
			$company = Company::where('username', Input::get('username'))->first();
		}
		catch(Exception $e)
		{
			$company = false;
		}
		if( $company && $company->verified == 0)
			return Response::json( array('errors' => array('Your account is on the waiting list.<br><br>Please <a href="mailto:hello@scubawhere.com?subject=Please verify my account&body=Hello Team Scubawhere!%0A%0APlease verify my account.%0AMy username is: '.$company->username.'. %0A%0AThank you!">contact us</a> to accelerate your verification.')), 406 ); // 406 Not Acceptable

		return Response::json( array('errors' => array('Oops, something wasn\'t correct.')), 401 ); // 401 Unauthorized
	}
});

Route::get('logout', function()
{
	Auth::logout();

	return Response::json( array('status' => 'Successfully logged out. See you soon!') );
});

// Needs to be unauthorized, because it's needed in registration
Route::controller('api/country', 'CountryController');
Route::controller('api/currency', 'CurrencyController');
Route::controller('api/agency', 'AgencyController');

Route::group(array('before' => 'auth'), function()
{
	Route::get('token', function()
	{
		return Session::getToken();
	});

	Route::controller('company', 'CompanyController');

	Route::controller('api/accommodation', 'AccommodationController');

	Route::controller('api/addon', 'AddonController');

	//Route::controller('api/agency', 'AgencyController');

	Route::controller('api/agent', 'AgentController');

	Route::controller('api/boat', 'BoatController');

	Route::controller('api/boatroom', 'BoatroomController');

	Route::controller('api/booking', 'BookingController');

	Route::controller('api/customer', 'CustomerController');

	Route::controller('api/location', 'LocationController');

	Route::controller('api/package', 'PackageController');

	Route::controller('api/session', 'DepartureController');

	Route::controller('api/ticket', 'TicketController');

	Route::controller('api/timetable', 'TimetableController');

	Route::controller('api/trip', 'TripController');
});
