<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveUnneededIdColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('trip_triptype', function($table) {
			$table->dropColumn('id');
		});
		Schema::table('location_trip', function($table) {
			$table->dropColumn('id');
		});
		Schema::table('booking_details', function($table) {
			$table->dropColumn('id');
		});
		Schema::table('package_ticket', function($table) {
			$table->dropColumn('id');
		});
		Schema::table('boat_ticket', function($table) {
			$table->dropColumn('id');
		});
		Schema::table('accommodation_boat', function($table) {
			$table->dropColumn('id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('trip_type', function($table) {
			$table->increments('id');
		});
		Schema::table('location_trip', function($table) {
			$table->increments('id');
		});
		Schema::table('booking_details', function($table) {
			$table->increments('id');
		});
		Schema::table('package_ticket', function($table) {
			$table->increments('id');
		});
		Schema::table('boat_ticket', function($table) {
			$table->increments('id');
		});
		Schema::table('accomodation_boat', function($table) {
			$table->increments('id');
		});
	}

}
