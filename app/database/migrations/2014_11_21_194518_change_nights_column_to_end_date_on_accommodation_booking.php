<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNightsColumnToEndDateOnAccommodationBooking extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('accommodation_booking', function($table)
		{
			$table->renameColumn('date', 'start');

			$table->dropColumn('nights');

			$table->date('end')->after('date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('accommodation_booking', function($table)
		{
			$table->renameColumn('start', 'date');

			$table->dropColumn('end');

			$table->integer('nights')->after('date');
		});
	}

}
