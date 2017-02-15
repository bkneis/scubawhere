<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommissionableFlagsToBookingPivotTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addon_bookingdetail', function (Blueprint $table) {
		    $table->boolean('commissionable')->default(true)->after('quantity');
		});
		Schema::table('packagefacades', function (Blueprint $table) {
			$table->boolean('commissionable')->default(true)->after('package_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('addon_bookingdetail', function (Blueprint $table) {
			$table->dropColumn('commissionable');
		});
		Schema::table('packagefacades', function (Blueprint $table) {
			$table->dropColumn('commissionable');
		});
	}

}
