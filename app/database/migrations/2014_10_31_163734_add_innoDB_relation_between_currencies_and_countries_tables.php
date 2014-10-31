<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInnoDBRelationBetweenCurrenciesAndCountriesTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Artisan::call('db:seed', array('--class' => 'CurrenciesTableSeeder'));

		DB::table('countries')->where('name', 'United Kingdom')->update( array('currency_id' => 52) );
		DB::table('countries')->where('name', 'Australia')->update( array('currency_id' => 8) );
		DB::table('countries')->where('name', 'USA')->update( array('currency_id' => 150) );

		// For the seeded tables
		DB::table('countries')->where('name', 'United States')->update( array('currency_id' => 150) );

		Schema::table('countries', function($table)
		{
			$table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('countries', function($table)
		{
			$table->dropForeign('countries_currency_id_foreign');
		});
	}

}
