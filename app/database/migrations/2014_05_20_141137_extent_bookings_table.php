<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExtentBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bookings', function($table)
		{
			$table->string('reference', 8)->after('id')->unique();
			$table->string('currency', 3)->after('price');


			$table->dropColumn( array('created_at', 'updated_at', 'manual', 'paid') );

			$table->integer('agent_id')->unsigned()->nullable()->default(null)->after('customer_id');
			$table->string('source', 10)->after('agent_id')->nullable()->default(null); // Either 'telephone', 'email', 'facetoface' or 'frontend' (also possible: 'widget', 'other')

			$table->decimal('paid_cash', 10, 2)->default(0);
			$table->decimal('paid_creditcard', 10, 2)->default(0);
			$table->decimal('paid_cheque', 10, 2)->default(0);
			$table->decimal('paid_banktransfer', 10, 2)->default(0);

			$table->decimal('pay_online', 10, 2)->default(0);
			$table->decimal('pay_later', 10, 2)->default(0);

			$table->dateTime('reserved')->nullable()->default(null);

			$table->string('pick_up', 100);
			$table->string('drop_off', 100);

			$table->text('comments');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('bookings', function($table) {
			$table->dropColumn( array('reference', 'currency', 'agent_id', 'source', 'paid_cash', 'paid_creditcard', 'paid_cheque', 'paid_banktransfer', 'pay_online', 'pay_later', 'reserved', 'pick_up', 'drop_off', 'comments') );
		});

		Schema::table('bookings', function($table) {
			$table->boolean('manual')->default(true);
			$table->boolean('paid')->default(false);

			$table->timestamps();
		});
	}

}
