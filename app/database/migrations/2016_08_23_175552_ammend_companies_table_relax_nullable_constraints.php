<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AmmendCompaniesTableRelaxNullableConstraints extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE `companies` MODIFY `description` TEXT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `address_1` VARCHAR(128) NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `city` VARCHAR(128) NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `postcode` VARCHAR(16) NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `country_id` INT(10) UNSIGNED NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `currency_id` INT(10) UNSIGNED NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `business_email` VARCHAR(255) NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `business_phone` VARCHAR(255) NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `latitude` DOUBLE(10,7) NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `longitude` DOUBLE(10,7) NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `timezone` VARCHAR(128) NULL;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `companies` MODIFY `description` TEXT NOT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `address_1` VARCHAR(128) NOT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `city` VARCHAR(128) NOT NOT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `postcode` VARCHAR(16) NOT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `country_id` INT(10) UNSIGNED NOT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `currency_id` INT(10) UNSIGNED NOT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `business_email` VARCHAR(255) NOT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `business_phone` VARCHAR(255) NOT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `latitude` DOUBLE(10,7) NOT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `longitude` DOUBLE(10,7) NOT NULL;');
		DB::statement('ALTER TABLE `companies` MODIFY `timezone` VARCHAR(128) NOT NULL;');
	}

}
