<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyCampaignsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_campaigns', function($table){

			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('company_id')->unsigned();

			$table->longText('subject');

			$table->longText('message');

			$table->dateTime('sent_at');

			$table->integer('num_sent');

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
		Schema::drop('crm_campaigns');
	}

}
