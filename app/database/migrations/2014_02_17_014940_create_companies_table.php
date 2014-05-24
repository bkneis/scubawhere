<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('companies', function($table)
		{
			$table->increments('id');
			$table->string('username', 128)->unique();
			$table->string('password', 60);
			$table->string('email', 128)->unique();
			$table->boolean('verified')->default(false);
			$table->string('name', 128);
			$table->text('description');
			$table->string('address_1', 128);
			$table->string('address_2', 128);
			$table->string('city', 128);
			$table->string('county', 128);
			$table->string('postcode', 16);
			$table->integer('region_id')->unsigned();
			$table->integer('country_id')->unsigned();
			$table->float('latitude', 10, 6);
			$table->float('longitude', 10, 6);
			$table->string('phone', 32);
			$table->string('contact', 128);
			$table->string('website', 128);
			$table->boolean('agency')->default(false);
			$table->string('logo', 64)->defaul('default.png');
			$table->string('photo', 64)->default('default.png');
			$table->string('video', 1024);
			$table->integer('views')->default(0);
			$table->timestamps();

			$table->foreign('region_id')->references('id')->on('regions')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('companies');
	}

}
