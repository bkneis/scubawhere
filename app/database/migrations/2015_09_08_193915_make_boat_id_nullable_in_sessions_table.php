<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeBoatIdNullableInSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared("ALTER TABLE `sessions` CHANGE `boat_id` `boat_id` INT(10) UNSIGNED NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("ALTER TABLE `sessions` CHANGE `boat_id` `boat_id` INT(10) UNSIGNED NOT NULL;");
	}

}
