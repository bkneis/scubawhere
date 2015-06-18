<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAvailableBetweenAndAvailableForBetweenColumnsToTicketsAndPackagesTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tickets', function($table)
		{
			$table->date('available_from')->nullable()->default(null);
			$table->date('available_until')->nullable()->default(null);
			$table->date('available_for_from')->nullable()->default(null);
			$table->date('available_for_until')->nullable()->default(null);
		});

		Schema::table('packages', function($table)
		{
			$table->date('available_from')->nullable()->default(null);
			$table->date('available_until')->nullable()->default(null);
			$table->date('available_for_from')->nullable()->default(null);
			$table->date('available_for_until')->nullable()->default(null);
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
			$table->dropColumn([
				'available_from',
				'available_until',
				'available_for_from',
				'available_for_until'
			]);
		});

		Schema::table('packages', function($table)
		{
			$table->dropColumn([
				'available_from',
				'available_until',
				'available_for_from',
				'available_for_until'
			]);
		});
	}

}
