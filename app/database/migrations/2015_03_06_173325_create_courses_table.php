<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('courses', function($table)
		{
			$table->increments('id');

			$table->integer('company_id')->unsigned();
			$table->string('name');
			$table->text('description');
			$table->integer('capacity');
			$table->integer('training_id')->unsigned();
			$table->integer('training_quantity');

			$table->timestamps();
			$table->softDeletes();

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
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
		Schema::drop('courses');
	}

}
