<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeForeignKeyPackagefacadeIdOnBookingDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('booking_details', function($table)
		{
			$table->dropForeign('booking_details_packagefacade_id_foreign');

			$table->foreign('packagefacade_id')->references('id')->on('packagefacades')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('booking_details', function($table)
		{
			$table->dropForeign('booking_details_packagefacade_id_foreign');

			$table->foreign('packagefacade_id')->references('id')->on('packagefacades')->onUpdate('cascade')->onDelete('set null');
		});
	}

}
