<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentIdToBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function($table)
		{
			$table->integer('parent_id')->unsigned()->nullable()->default(null)->after('comment');

			$table->foreign('parent_id')->references('id')->on('bookings')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('bookings', function($table)
		{
			$table->dropForeign('bookings_parent_id_foreign');

			$table->dropColumn('parent_id');
		});
	}

}
