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

Route::group(array('before' => 'auth.basic'), function()
{
	Route::get('token', function()
	{
		return Session::getToken();
	});

	Route::controller('company', 'CompanyController');

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

###########################################
########## S T A T U S   P A G E ##########
###########################################

// TODO Move this in a dedicated controller

Route::get('status', function()
{
	return View::make('status.security');
});
Route::post('status', function()
{

	if( !Input::has('password') || Input::get('password') !== 'show me')
		return View::make('status.security');

	try
	{
		$log = File::get(storage_path().'/logs/performance.log');
	}
	catch (Illuminate\Filesystem\FileNotFoundException $e)
	{
		die("The log file does not exist.");
	}

	// Find and replace unnecessary brackets
	$log = str_replace( array(' [', '[', ']'), '', $log );

	// Split log file by line
	$log = explode("\n", $log);

	// The log file contains an empty line at the end, which throws an error when PHP tries to get the nonexistent 2nd value of $line
	if( $log[ count($log) - 1 ] === '' )
		array_pop($log);

	$data = array();

	// Extract required values
	foreach($log as $line)
	{
		// Split line by space
		$line = explode(" ", $line);

		// Assign values to $data array
		// Example log line: '2014-11-18 00:24:52 local_soren.INFO: 66.042 GET /status  '
		array_push($data, array(
			'date'     => $line[0],
			'time'     => $line[1],
			// skip $line[2] - reduntant machine name
			'duration' => $line[3],
			'method'   => $line[4],
			'route'    => $line[5]
		));
	}

	return View::make( 'status.status', array('data' => json_encode($data)) );
});
