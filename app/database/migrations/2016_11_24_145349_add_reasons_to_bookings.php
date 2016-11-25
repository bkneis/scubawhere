<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReasonsToBookings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function (Blueprint $table) {
			$table->string('discount_reason')->nullable()->after('comment');
			$table->string('cancel_reason')->nullable()->after('discount_reason');
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
			$table->dropColumn('discount_reason');
			$table->dropColumn('cancel_reason');
		});
	}

}
