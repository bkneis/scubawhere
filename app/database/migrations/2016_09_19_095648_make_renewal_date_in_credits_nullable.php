<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeRenewalDateInCreditsNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE credits MODIFY renewal_date DATE NULL;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE credits MODIFY renewal_date DATE NOT NULL;');
	}

}
