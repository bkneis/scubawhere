<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('boats', function($table)
		{
			$table->increments('id');
			$table->integer('company_id')->unsigned();
			$table->string('name', 128);
			$table->text('description');
			$table->integer('capacity');
			$table->string('photo', 64)->default('default.png');
			$table->timestamps();

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('boats');
	}

}
