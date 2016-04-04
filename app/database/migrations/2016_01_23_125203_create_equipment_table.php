<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('equipment', function($table){

			$table->engine = 'InnoDB';

			$table->increments('id');
            
            $table->integer('company_id')->unsigned();
            
            $table->integer('category_id')->unsigned();

			$table->string('uuid', 128)->nullable();
            
            $table->string('size', 128)->nullable();

			$table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('equipment_categories')->onUpdate('cascade')->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('equipment');
	}

}
