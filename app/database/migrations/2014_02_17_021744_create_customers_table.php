<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customers', function($table){
			$table->increments('id');
			$table->string('email', 128)->unique();
			$table->string('firstname', 128);
			$table->string('lastname', 128);
			$table->boolean('verified')->default(false);
			$table->datetime('birthday');
			$table->integer('gender')->default(1);
			$table->string('address_1', 128);
			$table->string('address_2', 128);
			$table->string('city', 128);
			$table->string('county', 128);
			$table->string('postcode', 16);
			$table->integer('region_id')->unsigned();
			$table->integer('country_id')->unsigned();
			$table->string('phone', 32);
			$table->integer('company_id')->nullable();
			$table->boolean('certified')->default(false);
			$table->integer('agency_id')->unsigned()->nullable()->default(null);
			$table->integer('certificate_id')->unsigned()->nullable()->default(null);
			$table->datetime('last_dive')->nullable()->default(null);
			$table->timestamps();

			$table->foreign('region_id')->references('id')->on('regions')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('agency_id')->references('id')->on('agencies')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('certificate_id')->references('id')->on('certificates')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('customers');
	}

}
