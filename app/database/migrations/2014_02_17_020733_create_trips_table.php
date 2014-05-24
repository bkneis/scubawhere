<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trips', function($table)
		{
			$table->increments('id');
			$table->integer('company_id')->unsigned();
			$table->string('name', 128);
			$table->text('description');
			$table->integer('duration');
			$table->integer('location_id')->unsigned()->nullable();
			$table->string('photo', 64);
			$table->string('video', 64);
			$table->integer('views')->default(0);
			$table->timestamps();

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('trips');
	}

}
