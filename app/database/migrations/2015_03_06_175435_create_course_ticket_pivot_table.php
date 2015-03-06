<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTicketPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_ticket', function($table)
		{
			$table->integer('course_id')->unsigned();
			$table->integer('ticket_id')->unsigned();
			$table->integer('quantity');

			$table->foreign('course_id')->references('id')->on('courses')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('course_ticket');
	}

}
