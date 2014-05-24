<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('agents', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 128);
			$table->string('website', 128);

			$table->integer('company_id')->unsigned();

			$table->string('branch_name', 128);
			$table->text('branch_address', 128);
			$table->string('branch_phone', 32);
			$table->string('branch_email', 128);

			$table->text('billing_address', 128)->nullable();
			$table->string('billing_phone', 32)->nullable();
			$table->string('billing_email', 128)->nullable();

			$table->integer('commission'); // Percentage
			$table->string('terms', 10); // Either 'fullamount', 'deposit' or 'banned';

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('agents');
	}

}
