<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameIndexesOnBoatBoatroomTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('boat_boatroom', function($table)
		{
			$table->dropForeign('accommodation_boat_boat_id_foreign');

			$table->dropIndex('accommodation_boat_boat_id_foreign');

			$table->foreign('boat_id')->references('id')->on('boats')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// This migration cannot be reversed here, because it relies on the names of the columns, which have been changed in another migration
	}

}
