<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusFieldToBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function($table)
		{
			$table->dropColumn(array('confirmed', 'saved'));

			$table->string('status', 128)->after('discount')->nullable()->default(null); // Either: null, 'saved', 'reserved', 'confirmed', 'on hold', 'cancelled'
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
			$table->dropColumn('status');

			$table->boolean('confirmed')->after('discount')->default(false);
			$table->boolean('saved')->after('reserved')->default(false);
		});
	}

}
