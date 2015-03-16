<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeForeignKeyTrainingIdOnCoursesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('courses', function($table)
		{
			$table->dropForeign('courses_training_id_foreign');

			$table->foreign('training_id')->references('id')->on('trainings')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('courses', function($table)
		{
			$table->dropForeign('courses_training_id_foreign');

			$table->foreign('training_id')->references('id')->on('trainings')->onUpdate('cascade')->onDelete('cascade');
		});
	}

}
