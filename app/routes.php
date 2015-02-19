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

Route::controller('password', 'PasswordController');

Route::controller('register', 'RegisterController');

Route::post('login', 'AuthenticationController@postLogin');

Route::get('logout', 'AuthenticationController@getLogout');

// Needs to be unauthorized, because it's needed in registration
Route::controller('api/country', 'CountryController');

Route::controller('api/currency', 'CurrencyController');

Route::controller('api/agency', 'AgencyController');

Route::group(array('before' => 'auth|auth.basic'), function()
{
	Route::get('token', function()
	{
		return Session::getToken();
	});

	Route::controller('api/company', 'CompanyController');

	Route::controller('api/accommodation', 'AccommodationController');

	Route::controller('api/addon', 'AddonController');

	Route::controller('api/agent', 'AgentController');

	Route::controller('api/boat', 'BoatController');

	Route::controller('api/boatroom', 'BoatroomController');

	Route::controller('api/booking', 'BookingController');

	Route::controller('api/customer', 'CustomerController');

	Route::controller('api/location', 'LocationController');

	Route::controller('api/package', 'PackageController');

	Route::controller('api/payment', 'PaymentController');

	Route::controller('api/session', 'DepartureController');

	Route::controller('api/ticket', 'TicketController');

	Route::controller('api/timetable', 'TimetableController');

	Route::controller('api/trip', 'TripController');
});
