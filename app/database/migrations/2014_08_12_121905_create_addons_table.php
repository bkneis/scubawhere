<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('addons', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 128);
			$table->string('description', 128)->nullable();
			$table->integer('price');
			$table->boolean('compulsory');

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
		Schema::drop('addons');
	}

}
