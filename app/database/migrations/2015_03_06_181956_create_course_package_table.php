<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursePackageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_package', function($table)
		{
			$table->integer('course_id')->unsigned();
			$table->integer('package_id')->unsigned();
			$table->integer('quantity');

			$table->foreign('course_id')->references('id')->on('courses')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('course_package');
	}

}
