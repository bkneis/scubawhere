<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InjectCustomersBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function($table)
		{
			$table->dropForeign('bookings_customer_id_foreign');
			$table->dropColumn('customer_id');
		});

		Schema::create('booking_customer', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('booking_id')->unsigned();
			$table->integer('customer_id')->unsigned();

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('bookings', function($table)
		{
			$table->integer('customer_id')->after('company_id')->unsigned();

			$table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('restrict');
		});

		Schema::drop('booking_customer');
	}

}
