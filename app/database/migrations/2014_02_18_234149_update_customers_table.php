<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customers', function($table)
		{
			$table->dropColumn('company_id');
		});

		Schema::table('customers', function($table)
		{
			$table->integer('company_id')->after('phone')->unsigned()->nullable();

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('set null');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customers', function($table)
		{
			$table->dropForeign('customers_company_id_foreign');
			$table->dropColumn('company_id');
		});

		Schema::table('customers', function($table)
		{
			$table->integer('company_id')->after('phone')->nullable();
		});
	}

}
