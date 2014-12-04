<?php

return array(

	'debug' => true,

	'url' => 'http://www.scubawhere.app',

	'providers' => append_config(array(

		'Clockwork\Support\Laravel\ClockworkServiceProvider',
		'Sisou\Ezmonitor\EzmonitorServiceProvider',

	)),

	'aliases' => append_config(array(

		'Clockwork' => 'Clockwork\Support\Laravel\Facade',

	)),

);
