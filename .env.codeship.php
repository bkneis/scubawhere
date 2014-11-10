<?php
// Local database settings for testing
return array(
	'DATABASE_HOST'     => 'localhost',
	'DATABASE_NAME'     => 'test',
	'DATABASE_USERNAME' => getenv('MYSQL_USER'),
	'DATABASE_PASSWORD' => getenv('MYSQL_PASSWORD'),
);
