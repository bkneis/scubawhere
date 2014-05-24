<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrencyEverywhere extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('companies', function($table)
		{
			$table->string('currency', 3)->after('country_id');
		});

		Schema::table('packages', function($table)
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
		Schema::table('companies', function($table)
		{
			$table->dropColumn('currency');
		});

		Schema::table('packages', function($table)
		{
			$table->dropColumn('currency');
		});
	}

}
