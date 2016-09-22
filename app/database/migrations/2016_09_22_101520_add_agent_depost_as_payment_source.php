<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAgentDepostAsPaymentSource extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('INSERT INTO paymentgateways (name) values ("Agent Deposit");');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('DELETE FROM paymentgateways WHERE name="Agent Deposit";');
	}

}
