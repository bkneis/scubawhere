<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQualificationNumberAndCylinderSizeToCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customers', function($table) {
			$table->string('cylinder_size')->after('height')->nullable();
			$table->string('notes')->after('cylinder_size')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customers', function($table) {
			$table->dropColumn('cylinder_size');
			$table->dropColumn('notes');
		});	
	}

}
