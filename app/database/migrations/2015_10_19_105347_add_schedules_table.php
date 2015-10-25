<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schedules', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('company_id')->unsigned();
			$table->tinyInteger('weeks');
			$table->text('schedule');

			$table->timestamps();

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::table('training_sessions', function($table)
		{
			$table->foreign('schedule_id')->references('id')->on('schedules')->onUpdate('cascade')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('training_sessions', function($table)
		{
			$table->dropForeign('training_sessions_schedule_id_foreign');
		});

		Schema::drop('schedules');
	}

}
