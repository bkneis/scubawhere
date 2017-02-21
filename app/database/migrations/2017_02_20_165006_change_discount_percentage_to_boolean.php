<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDiscountPercentageToBoolean extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function (Blueprint $table) {
		    $table->dropColumn('discount_percentage');
		});
		Schema::table('bookings', function (Blueprint $table) {
			$table->boolean('discount_percentage')->default(true)->after('discount');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('bookings', function (Blueprint $table) {
			$table->dropColumn('discount_percentage');
		});
		Schema::table('bookings', function (Blueprint $table) {
			$table->double('discount_percentage')->nullable()->after('discount');
		});
	}

}
