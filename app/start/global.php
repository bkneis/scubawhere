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
	app_path().'/models/entities',
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
	if($exception instanceof \Scubawhere\Exceptions\BaseException) {
		return $exception->response();
	}

	if($exception instanceof \Scubawhere\Exceptions\Http\HttpBaseException) {
		return $exception->response();
	}

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
	return !Scubawhere\Helper::isPast($value);
}, ':attribute datetime must be in the future');

Validator::extend('time', function($attribute, $value, $parameters)
{
	// Check that $value is 8 characters long ('HH:MM:SS')
	if( !strlen($value) === 8 )
		return false;

	// Split $value into parts
	$parts = explode(':', $value);

	// Check that the time consists of 3 parts
	if( count($parts) > 3 ) return false;

	foreach($parts as $part)
	{
		// Check that each part is 2 characters long
		if( strlen($part) !== 2)
			return false;

		// Check that each part is a number
		if( !is_numeric($part) )
			return false;

		// Check that the integer value of the part equals the part
		$intval = intval($part);

		if( $intval < 10 )
			$intval = '0'.$intval;

		if( strval($intval) !== $part )
			return false;

		// Check that each part is not bigger than 59
		if( $part > 59 )
			return false;
	}

	// Check that the hour part is not bigger than 23
	if( $parts[0] > 23 )
		return false;

	return true;

}, ':attribute must be a time of 24h format HH:MM:SS');

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
