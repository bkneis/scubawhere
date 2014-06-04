<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBookingDetailsToIncludeCustomerIdAndLeadStatus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('booking_customer');

		Schema::table('booking_details', function($table)
		{
			$table->integer('customer_id')->unsigned()->after('booking_id');

			$table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('restrict');
		});

		Schema::table('booking_details', function($table)
		{
			$table->boolean('is_lead')->after('customer_id')->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('booking_details', function($table)
		{
			$table->dropForeign('booking_details_customer_id_foreign');
			$table->dropColumn('customer_id', 'is_lead');
		});

		Schema::create('booking_customer', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('booking_id')->unsigned();
			$table->integer('customer_id')->unsigned();

			$table->timestamps();

			$table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('restrict');
		});
	}

}
