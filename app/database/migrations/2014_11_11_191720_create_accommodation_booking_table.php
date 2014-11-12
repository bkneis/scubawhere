<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccommodationBookingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('booking_details', function($table)
		{
			$table->dropForeign('booking_details_accommodation_id_foreign');

			$table->dropColumn('accommodation_id');
		});

		Schema::create('accommodation_booking', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('accommodation_id')->unsigned();
			$table->integer('booking_id')->unsigned();
			$table->integer('customer_id')->unsigned();

			$table->date('date');
			$table->integer('nights');

			$table->timestamps();

			$table->foreign('accommodation_id')->references('id')->on('accommodations')->onUpdate('cascade')->onDelete('restrict');
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
		Schema::table('booking_details', function($table)
		{
			$table->integer('accommodation_id')->unsigned()->after('packagefacade_id')->nullable()->default(null);

			$table->foreign('accommodation_id')->references('id')->on('accommodations')->onUpdate('cascade')->onDelete('restrict');
		});

		Schema::drop('accommodation_booking');
	}

}
