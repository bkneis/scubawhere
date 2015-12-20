<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToRepresentEmailSentToAllCustomers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('crm_campaigns', function($table)
		{
			$table->boolean('sendallcustomers');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('crm_campaigns', function($table)
		{
			$table->dropColumn('sendallcustomers');
		});
	}

}
