<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsToCrmTokens extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('crm_tokens', function($table)
		{
			$table->timestamps();
            $table->dropColumn('opened_at');
            $table->integer('opened_time');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('crm_tokens', function($table)
		{
			$table->dropColumn('updated_at');
            $table->dropColumn('created_at');
            $table->dropColumn('opened_time');
            $table->dateTime('opened_at');
		});
	}

}
