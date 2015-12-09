<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmLinksTrackerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_link_tracker', function($table){

			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('customer_id')->unsigned();
            
            $table->integer('link_id')->unsigned();

			$table->integer('count')->default(0);

			$table->string('token', 128);

			$table->timestamps();

			$table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('link_id')->references('id')->on('crm_links')->onUpdate('cascade')->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_link_tracker');
	}

}
