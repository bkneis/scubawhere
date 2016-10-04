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

Route::controller('test', 'TestController');

Route::controller('password', 'PasswordController');
Route::controller('register', 'RegisterController');
Route::get('terms', function()
{
	return Redirect::to("../common/scubawhereRMS_Terms_and_Conditions_for_operators.pdf");

	// The above opens the PDF in the browser (if supported), while the below directly downloads

	/*$file= public_path(). "/common/scubawhereRMS_Terms_and_Conditions_for_operators.pdf";
	$headers = array(
		'Content-Type: application/pdf',
	);
	return Response::download($file, null, $headers);*/
});

Route::post('login', 'AuthenticationController@postLogin');
Route::get('logout', 'AuthenticationController@getLogout');

//Route::get('get_crm_image', 'CrmTrackingController@getScubaImage');

// These controllers need to be unauthorized, because they are required in registration
Route::controllers([
	'agency'   => 'AgencyController',
	'country'  => 'CountryController',
	'currency' => 'CurrencyController',
    'crm_tracking' => 'CrmTrackingController'
]);

Route::group(array('before' => 'auth|auth.basic|csrf'), function()
{
	Route::get('token', function()
	{
		return Session::getToken();
	});

	Route::controllers([
		'accommodation'  => 'AccommodationController',
		'addon'          => 'AddonController',
		'agent'          => 'AgentController',
		'boat'           => 'BoatController',
		'boatroom'       => 'BoatroomController',
		'booking'        => 'BookingController',
		'campaign'       => 'CrmCampaignController',
        'campaign_template' => 'CrmTemplateController',
		'certificate'    => 'CertificateController',
		'class'          => 'TrainingController',
		'class-session'  => 'TrainingSessionController',
		'company'        => 'CompanyController',
		'course'         => 'CourseController',
		'customer'       => 'CustomerController',
		'customer-group' => 'CrmGroupController',
        'equipment'      => 'EquipmentController',
        'equipment-category' => 'EquipmentCategoryController',
        'equipment-price' => 'EquipmentPriceController',
		'location'       => 'LocationController',
        'log'            => 'LogController',
		'package'        => 'PackageController',
		'payment'        => 'PaymentController',
		'refund'         => 'RefundController',
		'report'         => 'ReportController',
		'schedule'       => 'ScheduleController',
		'search'         => 'SearchController',
		'session'        => 'DepartureController',
		'ticket'         => 'TicketController',
		'timetable'      => 'TimetableController',
		'trip'           => 'TripController'
	]);
});

Route::group(array('before' => 'auth.admin'), function() 
{
	Route::controller('admin', 'AdminController');
});
