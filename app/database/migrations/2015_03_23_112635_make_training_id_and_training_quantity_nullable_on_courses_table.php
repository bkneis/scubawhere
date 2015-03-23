<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeTrainingIdAndTrainingQuantityNullableOnCoursesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared("ALTER TABLE `courses` CHANGE `training_id` `training_id` INT(10) UNSIGNED NULL;");
		DB::unprepared("ALTER TABLE `courses` CHANGE `training_quantity` `training_quantity` INT(10) NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("ALTER TABLE `courses` CHANGE `training_id` `training_id` INT(10) UNSIGNED NOT NULL;");
		DB::unprepared("ALTER TABLE `courses` CHANGE `training_quantity` `training_quantity` INT(10) NOT NULL;");
	}

}
