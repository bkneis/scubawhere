<?php

return array(

	'debug' => true,

	'url' => 'http://www.scubawhere.app',

	'providers' => append_config(array(

		'Clockwork\Support\Laravel\ClockworkServiceProvider',
		'Sisou\Ezmonitor\EzmonitorServiceProvider',

	)),

);
