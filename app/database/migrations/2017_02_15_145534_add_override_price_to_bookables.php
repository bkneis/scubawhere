<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOverridePriceToBookables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addon_bookingdetail', function (Blueprint $table) {
		    $table->integer('override_price')->after('commissionable')->nullable();
		});
		Schema::table('packagefacades', function (Blueprint $table) {
		    $table->integer('override_price')->after('commissionable')->nullable();
		});
		Schema::table('accommodation_booking', function (Blueprint $table) {
		    $table->integer('override_price')->after('commissionable')->nullable();
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
		    $table->dropColumn('override_price');
		});
		Schema::table('packagefacades', function (Blueprint $table) {
			$table->dropColumn('override_price');
		});
		Schema::table('accommodation_booking', function (Blueprint $table) {
			$table->dropColumn('override_price');
		});
	}

}
