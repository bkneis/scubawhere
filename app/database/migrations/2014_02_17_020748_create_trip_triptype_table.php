<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripTriptypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trip_triptype', function($table)
		{
			$table->increments('id');
			$table->integer('trip_id')->unsigned();
			$table->integer('triptype_id')->unsigned();
			$table->timestamps();

			$table->foreign('trip_id')->references('id')->on('trips')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('triptype_id')->references('id')->on('triptypes')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('trip_triptype');
	}

}
