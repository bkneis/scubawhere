<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrencyFieldToTicketsAndAddonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tickets', function($table)
		{
			$table->string('currency', 3)->after('price');
		});

		Schema::table('addons', function($table)
		{
			$table->string('currency', 3)->after('price');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tickets', function($table)
		{
			$table->dropColumn('currency');
		});

		Schema::table('addons', function($table)
		{
			$table->dropColumn('currency');
		});
	}

}
