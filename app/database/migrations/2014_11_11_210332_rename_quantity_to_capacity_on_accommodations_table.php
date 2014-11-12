<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameQuantityToCapacityOnAccommodationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('accommodations', function($table)
		{
			$table->renameColumn('quantity', 'capacity');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('accommodations', function($table)
		{
			$table->renameColumn('capacity', 'quantity');
		});
	}

}
