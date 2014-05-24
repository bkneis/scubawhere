<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccommodationBoatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accommodation_boat', function($table)
		{
			$table->increments('id');
			$table->integer('accommodation_id')->unsigned();
			$table->integer('boat_id')->unsigned();
			$table->integer('capacity');
			$table->timestamps();

			$table->foreign('accommodation_id')->references('id')->on('accommodations')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('boat_id')->references('id')->on('boats')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('accommodation_boat');
	}

}
