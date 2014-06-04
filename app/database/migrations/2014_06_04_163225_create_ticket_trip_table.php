<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketTripTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// First, remove trip_id column from ticket table
		Schema::table('tickets', function($table)
		{
			$table->dropForeign('tickets_trip_id_foreign');
			$table->dropColumn('trip_id');
		});

		// Secondly, create ticket_trip pivot table
		Schema::create('ticket_trip', function($table){

			$table->engine = 'InnoDB';

			$table->integer('ticket_id')->unsigned();
			$table->integer('trip_id')->unsigned();

			$table->timestamps();

			$table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('trip_id')->references('id')->on('trips')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ticket_trip');

		Schema::table('tickets', function($table)
		{
			$table->integer('trip_id')->unsigned()->after('id');

			$table->foreign('trip_id')->references('id')->on('trips')->onUpdate('cascade')->onDelete('cascade');
		});
	}

}
