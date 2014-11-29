<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAllPriceFieldsToInteger extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function($table)
		{
			$table->dropColumn('price');
		});
		Schema::table('bookings', function($table)
		{
			$table->integer('price')->after('source');
		});

		Schema::table('packages', function($table)
		{
			$table->dropColumn('price');
		});
		Schema::table('packages', function($table)
		{
			$table->integer('price')->after('description');
		});

		Schema::table('tickets', function($table)
		{
			$table->dropColumn('price');
		});
		Schema::table('tickets', function($table)
		{
			$table->integer('price')->after('description');
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
			$table->dropColumn('price');
		});

		Schema::table('bookings', function($table)
		{
			$table->decimal('price', 10, 2)->after('source');
		});


		Schema::table('packages', function($table)
		{
			$table->dropColumn('price');
		});

		Schema::table('packages', function($table)
		{
			$table->decimal('price', 10, 2)->after('description');
		});


		Schema::table('tickets', function($table)
		{
			$table->dropColumn('price');
		});

		Schema::table('tickets', function($table)
		{
			$table->decimal('price', 10, 2)->after('description');
		});
	}

}
