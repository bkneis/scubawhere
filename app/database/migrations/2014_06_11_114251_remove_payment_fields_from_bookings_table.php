<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePaymentFieldsFromBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function($table)
		{
			$table->dropColumn('paid_cash', 'paid_creditcard', 'paid_cheque', 'paid_banktransfer', 'pay_online', 'pay_later');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('bookings', function($table)
		{
			$table->decimal('paid_cash', 10, 2)->default(0);
			$table->decimal('paid_creditcard', 10, 2)->default(0);
			$table->decimal('paid_cheque', 10, 2)->default(0);
			$table->decimal('paid_banktransfer', 10, 2)->default(0);

			$table->decimal('pay_online', 10, 2)->default(0);
			$table->decimal('pay_later', 10, 2)->default(0);
		});
	}

}
