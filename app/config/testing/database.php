<?php

return array(
	'default' => 'mysqltest',
	'connections' => array(
		'mysqltest' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'test'/*.getenv('TEST_ENV_NUMBER')*/,
			'username'  => getenv('MYSQL_USER'),
			'password'  => getenv('MYSQL_PASSWORD'),
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),
	)
);
