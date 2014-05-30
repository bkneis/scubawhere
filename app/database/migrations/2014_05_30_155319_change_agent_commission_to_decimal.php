<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAgentCommissionToDecimal extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('agents', function($table)
		{
			$table->dropColumn('commission');
		});

		Schema::table('agents', function($table)
		{
			$table->decimal('commission', 10, 2)->after('billing_email'); // Percentage
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('agents', function($table)
		{
			$table->dropColumn('commission');
		});

		Schema::table('agents', function($table)
		{
			$table->integer('commission')->after('billing_email'); // Percentage
		});
	}

}
