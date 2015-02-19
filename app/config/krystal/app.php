<?php

return array(

	'debug' => true,

	'url' => 'http://api-test.scubawhere.com',
	'rms_url' => 'http://rms-test.scubawhere.com',

	'providers' => append_config(array(

		'Sisou\Ezmonitor\EzmonitorServiceProvider',

	))

);
