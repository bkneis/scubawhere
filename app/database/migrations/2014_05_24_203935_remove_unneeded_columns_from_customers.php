<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnneededColumnsFromCustomers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customers', function($table)
		{
			$table->dropForeign('customers_agency_id_foreign');

			$table->dropColumn('certified');
			$table->dropColumn('agency_id');
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
			$table->boolean('certified')->default(false);
			$table->integer('agency_id')->unsigned()->nullable()->default(null);

			$table->foreign('agency_id')->references('id')->on('agencies')->onUpdate('cascade')->onDelete('restrict');
		});
	}

}
