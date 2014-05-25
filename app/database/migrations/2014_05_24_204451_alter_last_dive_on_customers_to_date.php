<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLastDiveOnCustomersToDate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customers', function($table)
		{
			$table->dropColumn('last_dive');
		});

		Schema::table('customers', function($table)
		{
			$table->date('last_dive')->after('certificate_id')->nullable()->default(null);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customers', function($table)
		{
			$table->dropColumn('last_dive');
		});

		Schema::table('customers', function($table)
		{
			$table->datetime('last_dive')->after('certificate_id')->nullable()->default(null);
		});
	}

}
