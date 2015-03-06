<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('training_sessions', function($table)
		{
			$table->increments('id');

			$table->integer('training_id')->unsigned();
			$table->datetime('start');
			$table->integer('schedule_id')->unsigned()->nullable()->default(null);

			$table->timestamps();
			$table->softDeletes();

			$table->foreign('training_id')->references('id')->on('trainings')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('training_sessions');
	}

}
