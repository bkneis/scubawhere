<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLatitudeAndLongitudeFieldsToDoupleType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared("ALTER TABLE `companies` CHANGE `latitude` `latitude` DOUBLE(10,7) NOT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE `longitude` `longitude` DOUBLE(10,7) NOT NULL;");

		DB::unprepared("ALTER TABLE `locations` CHANGE `latitude` `latitude` DOUBLE(10,7) NOT NULL;");
		DB::unprepared("ALTER TABLE `locations` CHANGE `longitude` `longitude` DOUBLE(10,7) NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("ALTER TABLE `companies` CHANGE `latitude` `latitude` FLOAT(10,6) NOT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE `longitude` `longitude` FLOAT(10,6) NOT NULL;");

		DB::unprepared("ALTER TABLE `locations` CHANGE `latitude` `latitude` FLOAT(10,6) NOT NULL;");
		DB::unprepared("ALTER TABLE `locations` CHANGE `longitude` `longitude` FLOAT(10,6) NOT NULL;");
	}

}
