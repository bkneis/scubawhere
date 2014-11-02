<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Remove all price and currency fields from tables
		Schema::table('tickets', function($table)
		{
			$table->dropColumn( array('price', 'currency') );
		});

		Schema::table('packages', function($table)
		{
			$table->dropColumn( array('price', 'currency') );
		});

		Schema::create('prices', function($table)
		{
			$table->engine = "InnoDB";

			$table->increments('id');

			$table->integer('owner_id');
			$table->string('owner_type');

			$table->integer('price');
			$table->string('currency', 3);

			$table->tinyInteger('fromDay');
			$table->tinyInteger('fromMonth');
			$table->tinyInteger('untilDay');
			$table->tinyInteger('untilMonth');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('prices');

		Schema::table('tickets', function($table)
		{
			$table->integer('price')->after('description');
			$table->string('currency', 3)->after('price');
		});

		Schema::table('packages', function($table)
		{
			$table->integer('price')->after('description');
			$table->string('currency', 3)->after('price');
		});
	}

}
