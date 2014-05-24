<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('countries', function($table)
		{
			$table->increments('id');
			$table->integer('continent_id')->unsigned();
			$table->string('abbreviation', 8);
			$table->string('name', 128);
			$table->text('description');
			$table->string('flag', 64);
			$table->timestamps();

			$table->foreign('continent_id')->references('id')->on('continents')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('countries');
	}

}
