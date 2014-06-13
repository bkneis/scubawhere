<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyIdToTickets extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('tickets')->truncate();

		Schema::table('tickets', function($table)
		{
			$table->integer('company_id')->unsigned()->after('id');

			$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tickets', function($table)
		{
			$table->dropForeign('tickets_company_id_foreign');
			$table->dropColumn('company_id');
		});
	}

}