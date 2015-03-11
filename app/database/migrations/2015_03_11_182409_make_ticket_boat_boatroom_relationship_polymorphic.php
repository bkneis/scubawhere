<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeTicketBoatBoatroomRelationshipPolymorphic extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('boat_ticket');

		Schema::create('ticketables', function($table)
		{
			$table->integer('ticket_id')->unsigned();
			$table->integer('ticketable_id')->unsigned();
			$table->string('ticketable_type');

			$table->timestamps();

			$table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ticketables');

		Schema::create('boat_ticket', function($table)
		{
			$table->integer('boat_id')->unsigned()->nullable()->default(null);
			$table->integer('ticket_id')->unsigned();
			$table->integer('boatroom_id')->unsigned()->nullable()->default(null);

			$table->timestamps();

			$table->foreign('boat_id')->references('id')->on('boats')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('boatroom_id')->references('id')->on('boatrooms')->onUpdate('cascade')->onDelete('restrict');
		});
	}

}
