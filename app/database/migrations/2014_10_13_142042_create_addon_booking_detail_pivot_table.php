<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddonBookingDetailPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('addon_bookingdetail', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('addon_id')->unsigned();
			$table->integer('bookingdetail_id')->unsigned();

			$table->timestamps();

			$table->foreign('addon_id')->references('id')->on('addons')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('bookingdetail_id')->references('id')->on('booking_details')->onUpdate('cascade')->onDelete('cascade');
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
			$table->dropForeign('addon_bookingdetail_addon_id_foreign');
			$table->dropForeign('addon_bookingdetail_bookingdetail_id_foreign');
		});

		Schema::drop('addon_bookingdetail');
	}

}
