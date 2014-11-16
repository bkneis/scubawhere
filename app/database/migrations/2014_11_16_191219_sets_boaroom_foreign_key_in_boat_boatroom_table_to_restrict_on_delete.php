<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetsBoaroomForeignKeyInBoatBoatroomTableToRestrictOnDelete extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('boat_boatroom', function($table)
		{
			$table->dropForeign('accommodation_boat_boatroom_id_foreign');

			$table->dropIndex('accommodation_boat_accommodation_id_foreign');

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
		Schema::table('boat_boatroom', function($table)
		{
			$table->dropForeign('boat_boatroom_boatroom_id_foreign');

			$table->foreign('boatroom_id')->references('id')->on('boatrooms')->onUpdate('cascade')->onDelete('cascade');
		});
	}

}
