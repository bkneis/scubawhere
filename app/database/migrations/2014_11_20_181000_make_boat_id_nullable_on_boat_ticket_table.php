<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeBoatIdNullableOnBoatTicketTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared("ALTER TABLE `boat_ticket` CHANGE `boat_id` `boat_id` INT(10) UNSIGNED NULL DEFAULT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("ALTER TABLE `boat_ticket` CHANGE `boat_id` `boat_id` INT(10) UNSIGNED NOT NULL;");
	}

}
