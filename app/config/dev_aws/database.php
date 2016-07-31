<?php

if (!defined('RDS_HOSTNAME')) {
    define('RDS_HOSTNAME', $_SERVER['RDS_HOSTNAME']);
    define('RDS_USERNAME', $_SERVER['RDS_USERNAME']);
    define('RDS_PASSWORD', $_SERVER['RDS_PASSWORD']);
    define('RDS_DB_NAME', $_SERVER['RDS_DB_NAME']);
    define('REDIS_HOSTNAME', $_SERVER['REDIS_HOSTNAME']);
    define('REDIS_PORT', $_SERVER['REDIS_PORT']);
}

return array(

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => array(

        'mysql' => array(
            'driver'    => 'mysql',
            'host'      => RDS_HOSTNAME,
            'database'  => RDS_DB_NAME,
            'username'  => RDS_USERNAME,
            'password'  => RDS_PASSWORD,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'engine'    => null,
        ),
    ),

    'redis' => array(
        'cluster' => true,
        'default' => array(
            'host'     => REDIS_HOSTNAME,
            'port'     => REDIS_PORT,
            'database' => 0,
        ),
    ),

);