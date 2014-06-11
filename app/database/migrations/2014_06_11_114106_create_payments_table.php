<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('booking_id')->unsigned();

			$table->integer('amount');
			$table->string('currency', 3);

			$table->integer('paymentgateway_id')->nullable()->unsigned();

			/*
			$table->string('exchange_currency', 3)->nullable()->default(null);
			$table->decimal('exchange_rate', 10, 4)->nullable()->default(null);
			*/

			$table->timestamps();

			$table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('paymentgateway_id')->references('id')->on('paymentgateways')->onUpdate('cascade')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('payments');
	}

}
