<?php

return array(
	'default' => 'mysqltest',
	'connections' => array(
		'mysqltest' => array(
			'driver'    => 'mysql',
			'host'      => getenv('TEST_DATABASE_HOST'),
			'database'  => getenv('TEST_DATABASE_NAME'),
			'username'  => getenv('TEST_DATABASE_USER'),
			'password'  => getenv('TEST_DATABASE_PASSWORD'),
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),
	)
);