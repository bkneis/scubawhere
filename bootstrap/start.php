<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application;

/*
|--------------------------------------------------------------------------
| Detect The Application Environment
|--------------------------------------------------------------------------
|
| Laravel takes a dead simple approach to your application environments
| so you can just specify a machine name for the host that matches a
| given environment, then we will automatically detect it for you.
|
*/

/*$env = $app->detectEnvironment(array(

	'local_soren'       => array('packer-virtualbox-iso'),
	'local_bryan'       => array('marvin'),
	'krystal'           => array('poseidon.krystal.co.uk'),
	'digitalocean'      => array('rms.scubawhere.com'),
	'production_aws'	=> array('scubawhere-rms.eu-central-1.elasticbeanstalk.com'),
	'local_gdawg'	    => array('gary-MS-7821')

));*/
$env = $app->detectEnvironment(function() {

	/*if($_SERVER['SERVER_NAME'] == 'scubawhererms-1.puzntmrpqp.eu-central-1.elasticbeanstalk.com')
	{
		return 'production_aws';
	}
	elseif($_SERVER['AWS_ENV'] == 'dev')
	{
		return 'production_aws';
	}*/

	if(isset($_SERVER['AWS_ENV'])) return 'production_aws';

	switch(gethostname()) {
		case 'marvin':
			return 'local_bryan';
		case 'poseidon.krystal.co.uk':
			return 'krystal';
		case 'rms.scubawhere.com':
			return 'digitalocean';
		case 'digitalocean':
			return 'local_gdawg';
		default:
			return 'production';
	}
});

/*
|--------------------------------------------------------------------------
| Bind Paths
|--------------------------------------------------------------------------
|
| Here we are binding the paths configured in paths.php to the app. You
| should not be changing these here. If you need to change these you
| may do so within the paths.php file and they will be bound here.
|
*/

$app->bindInstallPaths(require __DIR__.'/paths.php');

/*
|--------------------------------------------------------------------------
| Load The Application
|--------------------------------------------------------------------------
|
| Here we will load this Illuminate application. We will keep this in a
| separate location so we can isolate the creation of an application
| from the actual running of the application with a given request.
|
*/

$framework = $app['path.base'].
                 '/vendor/laravel/framework/src';

require $framework.'/Illuminate/Foundation/start.php';

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
