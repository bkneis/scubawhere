<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_tokens', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('campaign_id')->unsigned();
			$table->string('token', 128);
            $table->dateTime('opened_at');
            $table->integer('customer_id')->unsigned();

			$table->foreign('campaign_id')->references('id')->on('crm_campaigns')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('crm_tokens');
	}

}
