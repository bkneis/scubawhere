<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickUpDateFieldToBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Change pick_up_time data type from DATETIME to TIME
		DB::unprepared("ALTER TABLE `bookings` CHANGE `pick_up_time` `pick_up_time` TIME NULL DEFAULT NULL;");

		Schema::table('bookings', function($table)
		{
			$table->date('pick_up_date')->after('pick_up_location')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Change pick_up_time data type from TIME to DATETIME
		DB::unprepared("ALTER TABLE `bookings` CHANGE `pick_up_time` `pick_up_time` DATETIME NULL DEFAULT NULL;");

		Schema::table('bookings', function($table)
		{
			$table->dropColumn('pick_up_date');
		});
	}

}
