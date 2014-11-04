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
		return Response::json( array('errors' => array('Oops, something wasn\'t correct.')), 406 ); // 406 Not Acceptable
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

Route::group(array('before' => 'auth'), function()
{
	Route::get('token', function()
	{
		return Session::getToken();
	});

	Route::controller('company', 'CompanyController');

	Route::controller('api/addon', 'AddonController');

	Route::controller('api/agency', 'AgencyController');

	Route::controller('api/agent', 'AgentController');

	Route::controller('api/booking', 'BookingController');

	Route::controller('api/customer', 'CustomerController');

	Route::controller('api/location', 'LocationController');

	Route::controller('api/package', 'PackageController');

	Route::controller('api/session', 'DepartureController');

	Route::controller('api/ticket', 'TicketController');

	Route::controller('api/timetable', 'TimetableController');

	Route::controller('api/trip', 'TripController');
});
