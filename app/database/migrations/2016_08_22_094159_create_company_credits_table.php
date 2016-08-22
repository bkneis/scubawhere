<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyCreditsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('credits', function($table) {

			$table->increments('id');
			$table->integer('company_id')->unsigned();
			$table->integer('booking_credits');
			$table->integer('email_credits');
			$table->date('renewal_date');
			$table->timestamps();

			$table->foreign('company_id')->references('id')
										 ->on('companies')
										 ->onUpdate('cascade')
										 ->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('credits');
	}

}
