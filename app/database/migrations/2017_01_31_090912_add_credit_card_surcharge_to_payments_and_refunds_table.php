<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreditCardSurchargeToPaymentsAndRefundsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('payments', function (Blueprint $table) {
			$table->integer('surcharge')->unsigned()->after('notes')->nullable();
		});
		Schema::table('refunds', function (Blueprint $table) {
			$table->integer('surcharge')->unsigned()->after('notes')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('payments', function (Blueprint $table) {
			$table->dropColumn('surcharge');
		});
		Schema::table('refunds', function (Blueprint $table) {
			$table->dropColumn('surcharge');
		});
	}

}
