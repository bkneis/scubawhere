<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDescriptionColumnFromCurrenciesAndCountriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('currencies', function($table)
		{
			$table->dropColumn('description');
		});

		Schema::table('countries', function($table)
		{
			$table->dropColumn('description');
		});

		Schema::table('continents', function($table)
		{
			$table->dropColumn('description');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('currencies', function($table)
		{
			$table->text('description')->after('name');
		});

		Schema::table('countries', function($table)
		{
			$table->text('description')->after('name');
		});

		Schema::table('continents', function($table)
		{
			$table->text('description')->after('name');
		});
	}

}
