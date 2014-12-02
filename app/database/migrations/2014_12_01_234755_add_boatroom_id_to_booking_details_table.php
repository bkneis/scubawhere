<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBoatroomIdToBookingDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('booking_details', function($table)
		{
			$table->integer('boatroom_id')->unsigned()->nullable()->default(null)->after('session_id');

			$table->foreign('boatroom_id')->references('id')->on('boatrooms')->onUpdate('cascade')->onDelete('restrict');
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
			$table->dropForeign('booking_details_boatroom_id_foreign');

			$table->dropColumn('boatroom_id');
		});
	}

}
