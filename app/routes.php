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
	// Handles requests to /api
	return Redirect::to('../');
});

Route::controller('password', 'PasswordController');
Route::controller('register', 'RegisterController');

Route::post('login', 'AuthenticationController@postLogin');
Route::get('logout', 'AuthenticationController@getLogout');

// These controllers need to be unauthorized, because they are required in registration
Route::controllers([
	'agency'   => 'AgencyController',
	'country'  => 'CountryController',
	'currency' => 'CurrencyController'
]);

Route::group(array('before' => 'auth|auth.basic|csrf'), function()
{
	Route::get('token', function()
	{
		return Session::getToken();
	});

	Route::controllers([
		'accommodation' => 'AccommodationController',
		'addon'         => 'AddonController',
		'agent'         => 'AgentController',
		'boat'          => 'BoatController',
		'boatroom'      => 'BoatroomController',
		'booking'       => 'BookingController',
		'class'         => 'TrainingController',
		'company'       => 'CompanyController',
		'course'        => 'CourseController',
		'customer'      => 'CustomerController',
		'location'      => 'LocationController',
		'package'       => 'PackageController',
		'payment'       => 'PaymentController',
		'refund'        => 'RefundController',
		'report'        => 'ReportController',
		'search'        => 'SearchController',
		'session'       => 'DepartureController',
		'ticket'        => 'TicketController',
		'timetable'     => 'TimetableController',
		'trip'          => 'TripController'
	]);
});
