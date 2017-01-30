<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOverridePriceToBookingdetail extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('booking_details', function (Blueprint $table) {
			$table->integer('override_price')->after('temporary');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('booking_details', function (Blueprint $table) {
			$table->dropColumn('override_price');
		});
	}

}
