<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentIdColumnToTicketsPackagesAndAccommodationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('accommodations', function($table)
		{
			$table->integer('parent_id')->unsigned()->after('capacity')->nullable()->default(null);

			$table->foreign('parent_id')->references('id')->on('accommodations')->onUpdate('cascade')->onDelete('set null');
		});

		Schema::table('tickets', function($table)
		{
			$table->integer('parent_id')->unsigned()->after('description')->nullable()->default(null);

			$table->foreign('parent_id')->references('id')->on('tickets')->onUpdate('cascade')->onDelete('set null');
		});

		Schema::table('packages', function($table)
		{
			$table->integer('parent_id')->unsigned()->after('capacity')->nullable()->default(null);

			$table->foreign('parent_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('set null');
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
			$table->dropColumn('parent_id');
		});

		Schema::table('tickets', function($table)
		{
			$table->dropColumn('parent_id');
		});

		Schema::table('packages', function($table)
		{
			$table->dropColumn('parent_id');
		});
	}

}
