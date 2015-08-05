<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToCustomerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customers', function($table)
		{
			$table->integer('number_of_dives')->after('last_dive')->nullable()->default(null);
			$table->string('chest_size')->after('number_of_dives')->nullable()->default('');
			$table->string('shoe_size')->after('chest_size')->nullable()->default('');
			$table->string('height')->after('shoe_size')->nullable()->default('');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customers', function($table)
		{
			$table->dropColumn(['number_of_dives', 'chest_size', 'shoe_size', 'height']);
		});
	}

}
