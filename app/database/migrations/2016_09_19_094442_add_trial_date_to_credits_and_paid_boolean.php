<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrialDateToCreditsAndPaidBoolean extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('credits', function($table) {
			$table->date('trial_date')->after('renewal_date');	
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('credits', function($table) {
			$table->drop_column('trial_date');	
		});
	}

}
