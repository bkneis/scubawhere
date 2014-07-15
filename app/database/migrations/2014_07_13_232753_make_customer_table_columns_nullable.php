<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCustomerTableColumnsNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `email`      `email`      varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `birthday`   `birthday`   date NULL DEFAULT NULL;");

		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `address_1`  `address_1`  varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `address_2`  `address_2`  varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `city`       `city`       varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `county`     `county`     varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `postcode`   `postcode`   varchar(16)  NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `phone`      `phone`      varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `country_id` `country_id` int(10) UNSIGNED NULL DEFAULT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `email`      `email`      varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `birthday`   `birthday`   date NOT NULL;");

		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `address_1`  `address_1`  varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `address_2`  `address_2`  varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `city`       `city`       varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `county`     `county`     varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `postcode`   `postcode`   varchar(16)  NOT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `phone`      `phone`      varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `customers` CHANGE COLUMN `country_id` `country_id` int(10) UNSIGNED NOT NULL;");
	}

}
