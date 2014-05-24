<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sessions', function($table)
		{
			$table->integer('timetable_id')->after('boat_id')->unsigned()->nullable()->default(null);
			$table->softDeletes();

			$table->foreign('timetable_id')->references('id')->on('timetables')->onUpdate('cascade')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sessions', function($table)
		{
			$table->dropForeign('sessions_timetable_id_foreign');
			$table->dropColumn('timetable_id');
			$table->dropColumn('deleted_at');
		});
	}

}
