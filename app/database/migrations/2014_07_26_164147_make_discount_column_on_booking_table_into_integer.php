<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeDiscountColumnOnBookingTableIntoInteger extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared("ALTER TABLE `bookings` CHANGE COLUMN `discount` `discount` int(11) NULL DEFAULT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("ALTER TABLE `bookings` CHANGE COLUMN `discount` `discount` decimal(10,2) NULL DEFAULT NULL;");
	}

}
