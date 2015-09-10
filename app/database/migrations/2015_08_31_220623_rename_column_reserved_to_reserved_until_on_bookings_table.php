<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnReservedToReservedUntilOnBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function($table)
		{
			$table->renameColumn('reserved', 'reserved_until');
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
			$table->renameColumn('reserved_until', 'reserved');
		});
	}

}
