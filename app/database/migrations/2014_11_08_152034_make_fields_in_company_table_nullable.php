<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeFieldsInCompanyTableNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `address_2` `address_2` varchar(128) NULL DEFAULT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `county` `county` varchar(128) NULL DEFAULT NULL;");

		Schema::table('companies', function($table)
		{
			$table->dropColumn('agency');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `address_2` `address_2` varchar(128) NOT NULL;");
		DB::unprepared("ALTER TABLE `companies` CHANGE COLUMN `county` `county` varchar(128) NOT NULL;");

		Schema::table('companies', function($table)
		{
			$table->boolean('agency')->after('website');
		});
	}

}
