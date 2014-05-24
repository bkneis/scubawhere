<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sessions', function($table)
		{
			$table->increments('id');
			$table->integer('trip_id')->unsigned();
			$table->datetime('start');
			$table->integer('boat_id')->unsigned();
			$table->timestamps();

			$table->foreign('trip_id')->references('id')->on('trips')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('boat_id')->references('id')->on('boats')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sessions');
	}

}
