<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug' => true,

	// To be changed for release
	'key' => 'KnkmprpiozesA3Kjl7Ea2kV90EWJI5Ng',

	'providers' => append_config(array(

		'Sisou\Ezmonitor\EzmonitorServiceProvider',
		'BackupManager\Laravel\Laravel4ServiceProvider',

	)),

);
