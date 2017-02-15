<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommissionableFlagToBookingAccommodations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('accommodation_booking', function (Blueprint $table) {
		    $table->boolean('commissionable')->default(true)->after('packagefacade_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('accommodation_booking', function (Blueprint $table) {
		    $table->dropColumn('commissionable');
		});
	}

}
