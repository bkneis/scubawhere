<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultCreditsOptions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE `credits` CHANGE COLUMN `booking_credits` `booking_credits` INTEGER NOT NULL DEFAULT 50;');
		DB::statement('ALTER TABLE `credits` CHANGE COLUMN `email_credits` `email_credits` INTEGER NOT NULL DEFAULT 200;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `credits` CHANGE COLUMN `booking_credits` `booking_credits` INTEGER NOT NULL DEFAULT 350;');
		DB::statement('ALTER TABLE `credits` CHANGE COLUMN `email_credits` `email_credits` INTEGER NOT NULL DEFAULT 2000;');
	}

}
