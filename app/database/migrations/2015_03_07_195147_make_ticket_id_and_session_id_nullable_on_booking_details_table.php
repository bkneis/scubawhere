<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeTicketIdAndSessionIdNullableOnBookingDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared("ALTER TABLE `booking_details` CHANGE `ticket_id` `ticket_id` INT(10) UNSIGNED NULL;");
		DB::unprepared("ALTER TABLE `booking_details` CHANGE `session_id` `session_id` INT(10) UNSIGNED NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("ALTER TABLE `booking_details` CHANGE `ticket_id` `ticket_id` INT(10) UNSIGNED NOT NULL;");
		DB::unprepared("ALTER TABLE `booking_details` CHANGE `session_id` `session_id` INT(10) UNSIGNED NOT NULL;");
	}

}
