<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHtmlStringToCampaignsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('crm_campaigns', function($table)
		{
			$table->longText('email_html')->after('subject');
			$table->dropColumn('message');

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
			$table->longText('message');
			$table->dropColumn('email_html');

		});
	}

}
