<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCurrencyFieldsFromAllTablesExceptCompanies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addons', function($table)
		{
			$table->dropColumn('currency');
		});

		Schema::table('bookings', function($table)
		{
			$table->dropColumn('currency');
		});

		Schema::table('prices', function($table)
		{
			$table->dropColumn('currency');
		});

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		Schema::table('payments', function($table)
		{
			$table->dropColumn('currency');

			$table->integer('currency_id')->unsigned()->after('amount');

			$table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('restrict');
		});


		Schema::table('companies', function($table)
		{
			$table->dropColumn('currency');

			$table->integer('currency_id')->unsigned()->after('country_id');

			$table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('restrict');
		});

		// Set appropriate currency_id (GBP)
		DB::table('payments')->update( array('currency_id' => 52) );
		DB::table('companies')->update( array('currency_id' => 52) );

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
			$table->dropForeign('companies_currency_id_foreign');

			$table->dropColumn('currency_id');

			$table->string('currency', 3)->after('country_id');
		});


		Schema::table('payments', function($table)
		{
			$table->dropForeign('payments_currency_id_foreign');

			$table->dropColumn('currency_id');

			$table->string('currency', 3)->after('amount');
		});


		Schema::table('addons', function($table)
		{
			$table->string('currency', 3)->after('price');
		});

		Schema::table('bookings', function($table)
		{
			$table->string('currency', 3)->after('price');
		});

		Schema::table('prices', function($table)
		{
			$table->string('currency', 3)->after('price');
		});
	}

}
