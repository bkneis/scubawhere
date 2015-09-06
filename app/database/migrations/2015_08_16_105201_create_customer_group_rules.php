<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerGroupRules extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_group_rules', function($table){

			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('crm_group_id')->unsigned();

			$table->integer('agency_id')->unsigned()->nullable()->default(null);

			$table->integer('certificate_id')->unsigned()->nullable()->default(null);

			$table->integer('ticket_id')->unsigned()->nullable()->default(null);

			$table->integer('training_id')->unsigned()->nullable()->default(null);

			$table->timestamps();

			$table->foreign('crm_group_id')->references('id')->on('crm_groups')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('certificate_id')->references('id')->on('certificates')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('agency_id')->references('id')->on('agencies')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('training_id')->references('id')->on('trainings')->onUpdate('cascade')->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_group_rules');
	}

}
