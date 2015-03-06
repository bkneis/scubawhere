<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCourseIdAndTrainingSessionIdToBookingDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('booking_details', function($table)
		{
			$table->integer('course_id')->unsigned()->nullable()->default(null)->after('packagefacade_id');
			$table->integer('training_session_id')->unsigned()->nullable()->default(null)->after('course_id');

			$table->foreign('course_id')->references('id')->on('courses')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('training_session_id')->references('id')->on('training_sessions')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('booking_details', function($table)
		{
			$table->dropColumn('course_id', 'training_session_id');
		});
	}

}
