<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPackagefacadeIdToAddonBookingdetailAndAccommodationBookingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addon_bookingdetail', function($table)
		{
			$table->integer('packagefacade_id')->unsigned()->after('quantity')->nullable()->default(null);

			$table->foreign('packagefacade_id')->references('id')->on('packagefacades')->onUpdate('cascade')->onDelete('restrict');
		});

		Schema::table('accommodation_booking', function($table)
		{
			$table->integer('packagefacade_id')->unsigned()->after('end')->nullable()->default(null);

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
		Schema::table('addon_bookingdetail', function($table)
		{
			$table->dropColumn('packagefacade_id');
		});

		Schema::table('accommodation_booking', function($table)
		{
			$table->dropColumn('packagefacade_id');
		});
	}

}
