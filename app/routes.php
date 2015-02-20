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
	return Redirect::to('../');
});

Route::controller('password', 'PasswordController');

Route::controller('register', 'RegisterController');

Route::post('login', 'AuthenticationController@postLogin');

Route::get('logout', 'AuthenticationController@getLogout');

// Needs to be unauthorized, because it's needed in registration
Route::controller('country', 'CountryController');

Route::controller('currency', 'CurrencyController');

Route::controller('agency', 'AgencyController');

Route::group(array('before' => 'auth|auth.basic'), function()
{
	Route::get('token', function()
	{
		return Session::getToken();
	});

	Route::controller('company', 'CompanyController');

	Route::controller('accommodation', 'AccommodationController');

	Route::controller('addon', 'AddonController');

	Route::controller('agent', 'AgentController');

	Route::controller('boat', 'BoatController');

	Route::controller('boatroom', 'BoatroomController');

	Route::controller('booking', 'BookingController');

	Route::controller('customer', 'CustomerController');

	Route::controller('location', 'LocationController');

	Route::controller('package', 'PackageController');

	Route::controller('payment', 'PaymentController');

	Route::controller('session', 'DepartureController');

	Route::controller('ticket', 'TicketController');

	Route::controller('timetable', 'TimetableController');

	Route::controller('trip', 'TripController');
});
