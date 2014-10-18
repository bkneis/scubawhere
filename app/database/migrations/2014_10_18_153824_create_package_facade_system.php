<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageFacadeSystem extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// 1. Remove foreign key between booking_details and packages and rename column
		Schema::table('booking_details', function($table)
		{
			$table->dropForeign('booking_details_package_id_foreign');

			$table->renameColumn('package_id', 'packagefacade_id');
		});

		// 2. Create new facade table
		Schema::create('packagefacades', function($table){
			$table->increments('id');

			$table->integer('package_id')->unsigned();

			$table->timestamps();

			$table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
		});

		// 3. Add new foreign key to booking_details table
		Schema::table('booking_details', function($table)
		{
			$table->foreign('packagefacade_id')->references('id')->on('packagefacades')->onUpdate('cascade')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// 1. Remove foreign key between booking_details and packagefacades and rename column
		Schema::table('booking_details', function($table)
		{
			$table->dropForeign('booking_details_packagefacade_id_foreign');

			$table->renameColumn('packagefacade_id', 'package_id');
		});

		// 2. Remove facade table
		Schema::drop('packagefacades');

		// 3. Add new foreign key to booking_details table
		Schema::table('booking_details', function($table)
		{
			$table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('set null');
		});
	}

}
