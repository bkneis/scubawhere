<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAllAccommodationIdInstancesToBoatroomId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('accommodation_boat', function($table)
		{
			$table->dropForeign('accommodation_boat_accommodation_id_foreign');

			$table->renameColumn('accommodation_id', 'boatroom_id');

			$table->foreign('boatroom_id')->references('id')->on('boatrooms')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::rename('accommodation_boat', 'boat_boatroom');

		Schema::table('boat_ticket', function($table)
		{
			$table->dropForeign('boat_ticket_accommodation_id_foreign');

			$table->renameColumn('accommodation_id', 'boatroom_id');

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
		Schema::rename('boat_boatroom', 'accommodation_boat');

		Schema::table('accommodation_boat', function($table)
		{
			$table->dropForeign('boat_boatroom_boatroom_id_foreign');

			$table->renameColumn('boatroom_id', 'accommodation_id');

			$table->foreign('accommodation_id')->references('id')->on('boatrooms')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::table('boat_ticket', function($table)
		{
			$table->dropForeign('boat_ticket_boatroom_id_foreign');

			$table->renameColumn('boatroom_id', 'accommodation_id');

			$table->foreign('accommodation_id')->references('id')->on('boatrooms')->onUpdate('cascade')->onDelete('restrict');
		});
	}

}
