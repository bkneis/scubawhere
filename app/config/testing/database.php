<?php

return array(
	'default' => 'mysqltest',
	'connections' => array(
		'mysqltest' => array(
			'driver'    => 'mysql',
			'host'      => getenv('DATABASE_HOST'),
			'database'  => getenv('DATABASE_NAME'),
			'username'  => getenv('DATABASE_USERNAME'),
			'password'  => getenv('DATABASE_PASSWORD'),
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),
	)
);