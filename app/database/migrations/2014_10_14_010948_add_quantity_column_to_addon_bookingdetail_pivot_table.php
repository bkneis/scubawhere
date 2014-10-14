<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantityColumnToAddonBookingdetailPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addon_bookingdetail', function($table)
		{
			$table->integer('quantity')->after('bookingdetail_id')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('addon_bookingdetail', function($table)
		{
			$table->dropColumn('quantity');
		});
	}

}
