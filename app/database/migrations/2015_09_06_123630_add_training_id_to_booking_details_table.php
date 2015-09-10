<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrainingIdToBookingDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('booking_details', function($table)
		{
			$table->integer('training_id')->unsigned()->nullable()->default(null)->after('course_id');

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
		Schema::table('booking_details', function($table)
		{
			$table->dropForeign('booking_details_training_id_foreign');

			$table->dropColumn('training_id');
		});
	}

}
