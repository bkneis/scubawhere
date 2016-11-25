<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelledAtToBookingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function(Blueprint $table)
		{
			$table->date('cancelled_at')->nullable()->before('created_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('bookings', function(Blueprint $table)
		{
			$table->dropColumn('cancelled_at');
		});
	}

}
