<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentCommissionRulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('agent_commission_rules', function (Blueprint $table) {
		    $table->integer('agent_id')->unsigned();
			$table->string('type');
			$table->integer('item_id')->nullable()->unsigned();
			$table->integer('commission')->nullable()->unsigned();
			$table->integer('commission_value')->nullable()->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('agent_commission_rules');
	}

}
