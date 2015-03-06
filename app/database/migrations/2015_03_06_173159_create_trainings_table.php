<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trainings', function($table)
		{
			$table->increments('id');

			$table->integer('company_id')->unsigned();
			$table->string('name');
			$table->text('description');
			$table->decimal('duration', 11, 1);

			$table->timestamps();
			$table->softDeletes();

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
		Schema::drop('trainings');
	}

}
