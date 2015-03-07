<?php

return array(

	'debug' => true,

	'url' => 'http://rms.scubawhere.app',

	'providers' => append_config(array(

		'Clockwork\Support\Laravel\ClockworkServiceProvider',
		'Sisou\Ezmonitor\EzmonitorServiceProvider',
		'Way\Generators\GeneratorsServiceProvider',

	)),

	'aliases' => append_config(array(

		'Clockwork' => 'Clockwork\Support\Laravel\Facade',

	)),

);
