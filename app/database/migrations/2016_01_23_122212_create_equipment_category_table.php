<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('equipment_categories', function($table){

			$table->engine = 'InnoDB';

			$table->increments('id');
            
            $table->integer('company_id')->unsigned();

			$table->string('name', 128);
            
            $table->string('description', 256)->nullable();

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
		Schema::drop('equipment_categories');
	}

}
