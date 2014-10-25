<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('currencies', function($table)
		{
			$table->increments('id');			
			$table->string('name', 64);
			$table->string('alpha_code', 4);
			$table->integer('numeric_code')->unsigned();
			$table->string('symbol', 4);
			$table->integer('minor_unit')->unsigned();
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
		Schema::drop('currencies');
	}

}
