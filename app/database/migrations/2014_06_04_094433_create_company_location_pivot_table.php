<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyLocationPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_location', function($table){

			$table->engine = 'InnoDB';

			$table->integer('company_id')->unsigned();
			$table->integer('location_id')->unsigned();
			$table->timestamps();

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_location');
	}

}
