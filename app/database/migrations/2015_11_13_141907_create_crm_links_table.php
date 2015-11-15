<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmLinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_links', function($table){

			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('campaign_id')->unsigned();

			$table->string('link', 128);

			$table->timestamps();

			$table->foreign('campaign_id')->references('id')->on('crm_campaigns')->onUpdate('cascade')->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_links');
	}

}
