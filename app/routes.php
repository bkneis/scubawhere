<?php

/*
|--------------------------------------------------------------------------
| Utility routes
|--------------------------------------------------------------------------
|
| These routes are utility routes used for convience or occasional asset
| locating.
|
*/
Route::get('/', function()
{
	// Handles requests to /api
	return Redirect::to('../');
});

Route::get('terms', function()
{
	// Get scubawhere's terms and condition pdf and open in a browser
	return Redirect::to("../common/scubawhereRMS_Terms_and_Conditions_for_operators.pdf");
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| These 2 routs are responsible for logging the user in and out of the system.
| Once the route is reached, the context will be set which is used by the other
| controllers to encapsulate a users data. For example, if a dive center were
| to log in and they had an id of 1. All queries to the database should be
| made through the context or the onlyOwners scope to ensure the data belongs
| to that user.
|
*/
Route::post('login', 'AuthenticationController@postLogin');
Route::get('logout', 'AuthenticationController@getLogout');


/*
|--------------------------------------------------------------------------
| Un-Authenticated Controller
|--------------------------------------------------------------------------
|
| These controllers are outside the authenticated route group as there are
| required to be accessed by users other than dive centres or by dive centres
| performing account management, such as forgotten password, registration etc.
|
*/
Route::controllers([
	'password'     => 'PasswordController',
	'register'     => 'RegisterController',
    'crm_tracking' => 'CrmTrackingController'
]);

/*Route::group(array('before' => 'api-web-auth'), function() {

	Route::get('token', function()
	{
		return Session::getToken();
	});

	Route::controllers([
		'accommodation' => 'AccommodationController'
	]);

});*/


/*
|--------------------------------------------------------------------------
| Authenticated Controllers
|--------------------------------------------------------------------------
|
| This is the main section to RMS's routing. Most controllers belong to here.
| It uses a route group with the auth and csrf middleware so that csrf tokens
| are generated each request and the user needs to be authenticated.
|
*/
Route::group(array('before' => 'auth|auth.basic|csrf'), function()
{
	Route::get('token', function()
	{
		return Session::getToken();
	});

	Route::controllers([
		'addon'             => 'AddonController',
		'agent'             => 'AgentController',
		'agency'            => 'AgencyController',
		'boat'              => 'BoatController',
		'boatroom'          => 'BoatroomController',
		'booking'           => 'BookingController',
		'campaign'          => 'CrmCampaignController',
        'campaign_template' => 'CrmTemplateController',
		'certificate'       => 'CertificateController',
		'class'             => 'TrainingController',
		'class-session'     => 'TrainingSessionController',
		'company'           => 'CompanyController',
		'country'           => 'CountryController',
		'course'            => 'CourseController',
		'currency'          => 'CurrencyController',
		'customer'          => 'CustomerController',
		'customer-group'    => 'CrmGroupController',
		'location'          => 'LocationController',
        'log'               => 'LogController',
		'package'           => 'PackageController',
		'payment'           => 'PaymentController',
		'refund'            => 'RefundController',
		'report'            => 'ReportController',
		'schedule'          => 'ScheduleController',
		'session'           => 'DepartureController',
		'ticket'            => 'TicketController',
		'timetable'         => 'TimetableController',
		'trip'              => 'TripController',
		//'user'              => 'UserController'
	]);

	// @todo move this to manifest resource controller
	Route::get('accommodation/availability', 'AccommodationController@getAvailability');

	Route::resource('accommodation', 'AccommodationController',
		array('only' => array('show', 'index', 'store', 'update', 'destroy'))
	);

	Route::resource('manifest', 'ManifestController',
		array('only' => array('index'))
	);

	Route::get('user/companies', 'UserController@getCompanies');
	Route::get('user/active-company', 'UserController@getActiveCompany');
	Route::post('user/switch-company', 'UserController@postSwitchCompany');

	Route::resource('user', 'UserController',
		array('only' => array('store', 'update'))
	);

});

/*
|--------------------------------------------------------------------------
| Admin Routes 
|--------------------------------------------------------------------------
|
| These routes are not only authenticated by for admin users only, and
| should only be accessiable to scubawhere. Currently the middleware
| auth.admin just checks that the username is admin, but this will be
| extended in the future.
|
*/
Route::group(array('before' => 'auth.admin'), function() 
{
	Route::controller('admin', 'AdminController');
});
