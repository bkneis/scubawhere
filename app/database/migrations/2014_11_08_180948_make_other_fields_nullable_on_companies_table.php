<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeOtherFieldsNullableOnCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `vat_number` `vat_number` varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `registration_number` `registration_number` varchar(128) NULL DEFAULT NULL;");

		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `website` `website` varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `logo` `logo` varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `photo` `photo` varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `video` `video` varchar(128) NULL DEFAULT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `vat_number` `vat_number` varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `registration_number` `registration_number` varchar(128) NOT NULL;");

		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `website` `website` varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `logo` `logo` varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `photo` `photo` varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `video` `video` varchar(128) NOT NULL;");
	}

}
