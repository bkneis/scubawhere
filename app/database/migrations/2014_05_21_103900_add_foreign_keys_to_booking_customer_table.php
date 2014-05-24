<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToBookingCustomerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('booking_customer', function($table)
		{
			$table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('booking_customer', function($table)
		{
			$table->dropForeign('booking_customer_booking_id_foreign');
			$table->dropForeign('booking_customer_customer_id_foreign');
		});
	}

}
