<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoatTicketTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('boat_ticket', function($table)
		{
			$table->increments('id');
			$table->integer('boat_id')->unsigned();
			$table->integer('ticket_id')->unsigned();
			$table->integer('accommodation_id')->unsigned();
			$table->timestamps();

			$table->foreign('boat_id')->references('id')->on('boats')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('accommodation_id')->references('id')->on('accommodations')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('boat_ticket');
	}

}
