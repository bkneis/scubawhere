<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsToCustomers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customers', function (Blueprint $table) {
			$table->string('online_source')->nullable()->after('notes');
			$table->boolean('medication')->default(false)->after('online_source');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customers', function (Blueprint $table) {
			$table->dropColumn('online_source');
			$table->dropColumn('medication');
		});
	}

}
