<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function($table)
		{
			$table->renameColumn('comments', 'comment');

			$table->dropColumn( array('pick_up', 'drop_off') );

			$table->datetime('pick_up_time')->after('reserved')->nullable()->default(null);
			$table->string('pick_up_location', 100)->after('reserved')->nullable()->default(null);
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
			$table->renameColumn('comment', 'comments');

			$table->dropColumn( array('pick_up_location', 'pick_up_time') );

			$table->string('pick_up', 100)->after('reserved');
			$table->string('drop_off', 100)->after('reserved');
		});
	}

}
