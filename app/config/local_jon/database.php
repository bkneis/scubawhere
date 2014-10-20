<?php

return array(

	'connections' => array(

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => getenv('DATABASE_HOST'),
			'database'  => getenv('DATABASE_NAME'),
			'username'  => getenv('DATABASE_USER'),
			'password'  => getenv('DATABASE_PASSWORD'),
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),

	),

);
