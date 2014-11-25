<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveIsLeadColumnFromBookingDetailsToBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('booking_details', function($table)
		{
			$table->dropColumn('is_lead');
		});

		Schema::table('bookings', function($table)
		{
			$table->integer('lead_customer_id')->unsigned()->nullable()->after('company_id');

			$table->foreign('lead_customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('restrict');
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
			$table->boolean('is_lead')->after('customer_id')->default(false);
		});

		Schema::table('bookings', function($table)
		{
			$table->dropForeign('bookings_lead_customer_id_foreign');
			$table->dropIndex('bookings_lead_customer_id_foreign');

			$table->dropColumn('lead_customer_id');
		});
	}

}
