<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommissionableFlagsToBookingDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('booking_details', function (Blueprint $table) {
		    $table->boolean('item_commissionable')->default(true)->after('override_price');
			$table->boolean('addons_commissionable')->default(true)->after('item_commissionable');
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
		    $table->dropColumn('item_commissionable');
			$table->dropColumn('addons_commissionable');
		});
	}

}
