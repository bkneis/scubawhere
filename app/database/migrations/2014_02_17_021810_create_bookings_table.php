<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bookings', function($table){
			$table->increments('id');
			$table->integer('company_id')->unsigned();
			$table->integer('customer_id')->unsigned();
			$table->boolean('manual')->default(true);
			$table->decimal('price', 10, 2);
			$table->decimal('discount', 10, 2)->nullable()->default(null);
			$table->boolean('confirmed')->default(false);
			$table->boolean('paid')->default(false);
			$table->timestamps();

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('restrict');
			$table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bookings');
	}

}
