<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentGatewaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('paymentgateways', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->string('name', 32);

			$table->timestamps();
		});

		DB::table('paymentgateways')->insert(array(
			array('name' => 'Cash'),
			array('name' => 'Credit Card'),
			array('name' => 'Cheque'),
			array('name' => 'Bank Transfer'),
			array('name' => 'To be payed online'),
			array('name' => 'PayPal')
		));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('paymentgateways');
	}

}
