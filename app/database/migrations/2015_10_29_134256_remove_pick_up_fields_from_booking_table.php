<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePickUpFieldsFromBookingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function($table)
		{
			$table->dropColumn(['pick_up_location', 'pick_up_date', 'pick_up_time']);
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
			$table->string('pick_up_location', 100)->after('cancellation_fee')->nullable()->default(null);
			$table->date('pick_up_date')->after('pick_up_location')->nullable()->default(null);
			$table->time('pick_up_time')->after('pick_up_date')->nullable()->default(null);
		});
	}

}
