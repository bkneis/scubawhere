<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtColumnsToTicketsAndPackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tickets', function($table)
		{
			$table->softDeletes();
		});

		Schema::table('packages', function($table)
		{
			$table->softDeletes();
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
			$table->dropColumn('deleted_at');
		});

		Schema::table('packages', function($table)
		{
			$table->dropColumn('deleted_at');
		});
	}

}
