<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('refunds', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->integer('booking_id')->unsigned();
			$table->integer('amount');
			$table->integer('currency_id')->unsigned();
			$table->integer('paymentgateway_id')->unsigned()->nullable()->default(null);
			$table->date('received_at');

			$table->timestamps();

			$table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('restrict');
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
		Schema::drop('refunds');
	}

}
