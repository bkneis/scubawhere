<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThaiBahtToCurrencies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$thai_baht = new Currency();
		$thai_baht->code = 'THB';
		$thai_baht->name = 'Thai Baht';
		$thai_baht->save();

		$thailand = Country::where('name', 'THAILAND')->first();
		$thailand->currency()->associate($thai_baht);
		$thailand->save();
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// No need to reverse it
	}

}
