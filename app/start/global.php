<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
	if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException)
	{
		Log::error('NotFoundHttpException - Route: ' . Request::url() );
	}
	else
	{
		Log::error($exception);
	}
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

/*
|--------------------------------------------------------------------------
| ScubaWhere Extensions
|--------------------------------------------------------------------------
|
| In this section we will place all the extensions proprietary to
| ScubaWherethat that don't fit anywhere else into the code.
|
*/

// Register app response time tracking
$app_start_time = microtime(true);
App::finish(function() use ($app_start_time) {

	// Do not log file requests
	if( strpos(Request::path(), '.') !== false )
		return true;

	// Do not log /blog requests
	if( strpos(Request::path(), 'blog') !== false )
		return true;

	// Do not log status page requests
	if(Request::path() === 'status')
		return true;

	// Set performance log file location
	Log::useFiles(storage_path().'/logs/performance.log');

	// Log app execution duration with HTTP method and requested route
	Log::info( round( (microtime(true) - $app_start_time) * 1000, 3 ) . ' ' . Request::method() . ' ' . Request::path() );

	// Restore original log file location - not necessary, because this runs after the response has been sent and the application finished
	// Log::useFiles(storage_path().'/logs/laravel.log');
});

Validator::extend('valid_json', function($attribute, $value, $parameters)
{
	return json_decode($value) != null;
}, ':attribute must be valid JSON');

Validator::extend('valid_currency', function($attribute, $value, $parameters)
{
	try
	{
		$currency = new PhilipBrown\Money\Currency($value);
	}
	catch(InvalidCurrencyException $e)
	{
		return false;
	}

	return true;
}, ':attribute must be a valid currency code');

Validator::extend('after_local_now', function($attribute, $value, $parameters)
{
	$local = ScubaWhere\Helper::localTime();
	$test  = new DateTime($value);

	return $local < $test;
}, ':attribute datetime must lie in the future');

// From http://stackoverflow.com/questions/19131731/laravel-4-logging-sql-queries
if (Config::get('database.log', false))
{
	Event::listen('illuminate.query', function($query, $bindings, $time, $name)
	{
		$data = compact('bindings', 'time', 'name');

		// Format binding data for sql insertion
		foreach ($bindings as $i => $binding)
		{
			if ($binding instanceof \DateTime)
			{
				$bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
			}
			else if (is_string($binding))
			{
				$bindings[$i] = "'$binding'";
			}
		}

		// Insert bindings into query
		$query = str_replace(array('%', '?'), array('%%', '%s'), $query);
		$query = vsprintf($query, $bindings);

		Log::info($query, $data);
	});
}
