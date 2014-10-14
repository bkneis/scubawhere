<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtColumnsToAddonsAndTripsTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addons', function($table)
		{
			$table->softDeletes();
		});

		Schema::table('trips', function($table)
		{
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('addons', function($table)
		{
			$table->dropSoftDeletes();
		});

		Schema::table('trips', function($table)
		{
			$table->dropSoftDeletes();
		});
	}

}
