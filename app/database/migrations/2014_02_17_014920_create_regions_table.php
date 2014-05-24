<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('regions', function($table)
		{
			$table->increments('id');
			$table->integer('country_id')->unsigned();
			$table->string('abbreviation', 8);
			$table->string('name', 128);
			$table->text('description');
			$table->timestamps();

			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('regions');
	}

}
