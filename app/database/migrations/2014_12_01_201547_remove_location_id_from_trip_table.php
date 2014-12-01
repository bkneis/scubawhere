<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveLocationIdFromTripTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('trips', function($table)
		{
			$table->dropForeign('trips_location_id_foreign');

			$table->dropColumn('location_id');
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
			$table->integer('location_id')->unsigned()->after('duration');

			$table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('restrict');
		});
	}

}
