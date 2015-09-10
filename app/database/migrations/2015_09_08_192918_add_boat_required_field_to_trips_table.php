<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBoatRequiredFieldToTripsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('trips', function($table)
		{
			$table->boolean('boat_required')->default(true)->after('duration');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('trips', function($table)
		{
			$table->dropColumn('boat_required');
		});
	}

}
