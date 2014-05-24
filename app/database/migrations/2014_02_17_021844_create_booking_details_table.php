<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('booking_details', function($table){
			$table->increments('id');
			$table->integer('booking_id')->unsigned();
			$table->integer('ticket_id')->unsigned();
			$table->integer('session_id')->unsigned();
			$table->integer('package_id')->unsigned()->nullable();
			$table->timestamps();

			$table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('session_id')->references('id')->on('sessions')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('booking_details');
	}

}
