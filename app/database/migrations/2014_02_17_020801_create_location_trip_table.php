<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationTripTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('location_trip', function($table)
		{
			$table->increments('id');
			$table->integer('location_id')->unsigned();
			$table->integer('trip_id')->unsigned();
			$table->timestamps();

			$table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('restrict');
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
		Schema::drop('location_trip');
	}

}
