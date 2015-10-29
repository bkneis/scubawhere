<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickUpsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pick_ups', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('booking_id')->unsigned();

			$table->string('location', 100);
			$table->date('date');
			$table->time('time');

			$table->timestamps();

			$table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pick_ups');
	}

}
