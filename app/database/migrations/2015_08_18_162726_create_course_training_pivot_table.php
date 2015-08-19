<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTrainingPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_training', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('course_id')->unsigned();
			$table->integer('training_id')->unsigned();
			$table->integer('quantity');

			$table->timestamps();

			$table->foreign('course_id')->references('id')->on('courses')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('training_id')->references('id')->on('trainings')->onUpdate('cascade')->onDelete('cascade');
		});

		// Carry existing course -> training associations over to pivot table
		$inserts = [];
		Course::all()->each(function($course) use (&$inserts)
		{
			if(!empty($course->training_id))
				$inserts[] = [
					'course_id'   => $course->id,
					'training_id' => $course->training_id,
					'quantity'    => $course->training_quantity,
					'created_at'  => $course->created_at,
					'updated_at'  => $course->created_at // Here we use the created_at date on purpose, because e.g. the name of a course can be updated after creation, be we need the creation date of the training-assignment
				];
		});

		if(!empty($inserts))
			DB::table('course_training')->insert($inserts);

		Schema::table('courses', function($table)
		{
			$table->dropForeign('courses_training_id_foreign');
			$table->dropColumn(['training_id', 'training_quantity']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('course_training');

		Schema::table('courses', function($table)
		{
			$table->integer('training_id')->unsigned()->after('capacity');
			$table->integer('training_quantity')->after('training_id');

			$table->foreign('training_id')->references('id')->on('trainings')->onUpdate('cascade')->onDelete('cascade');
		});
	}

}
