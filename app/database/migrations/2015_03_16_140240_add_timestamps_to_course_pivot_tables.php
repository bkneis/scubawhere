<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsToCoursePivotTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('course_ticket', function($table)
		{
			$table->timestamps();
		});

		Schema::table('course_package', function($table)
		{
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('course_ticket', function($table)
		{
			$table->dropTimestamps();
		});

		Schema::table('course_package', function($table)
		{
			$table->dropTimestamps();
		});
	}

}
