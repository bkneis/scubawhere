<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultValuesToCredits extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `credits` MODIFY COLUMN `booking_credits` int NOT NULL DEFAULT '350';");
		DB::statement("ALTER TABLE `credits` MODIFY COLUMN `email_credits` int NOT NULL DEFAULT '504000';");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `credits` ALTER COLUMN `booking_credits` DROP DEFAULT;");
		DB::statement("ALTER TABLE `credits` ALTER COLUMN `email_credits` DROP DEFAULT;");
	}

}
