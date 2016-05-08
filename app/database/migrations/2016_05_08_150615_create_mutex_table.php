<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMutexTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * This migration is to create the mutexs table. This table is responsible for containing the mutexes of each model.
	 * When a user tries to edit a model, these mutexs are checked to ensure another user is not currently editing it.
	 *
	 * model - String of the model the user wishes to edit, e.g. /api/boats/edit - model = boats
	 * model_id - The id of the model being edited
	 * use - Boolean value, 0 means the item is not being edited, 1 represents it it
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mutexes', function($table) {

			$table->engine = "InnoDB";

			$table->increments('id');
			$table->integer('company_id')->unsigned();
			$table->string('model');
			$table->integer('model_id')->unsigned();
			$table->boolean('use')->default(1);

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
		Schema::drop('mutexes');
	}

}
