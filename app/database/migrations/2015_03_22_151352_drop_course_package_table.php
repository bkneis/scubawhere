<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCoursePackageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('course_package');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('course_package', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('course_id')->unsigned();
			$table->integer('package_id')->unsigned();
			$table->integer('quantity');

			$table->foreign('course_id')->references('id')->on('courses')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
		});
	}

}
