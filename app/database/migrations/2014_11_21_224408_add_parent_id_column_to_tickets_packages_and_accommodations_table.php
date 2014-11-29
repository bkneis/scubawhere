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
			$table->dropForeign('accommodations_parent_id_foreign');

			$table->dropColumn('parent_id');
		});

		Schema::table('tickets', function($table)
		{
			$table->dropForeign('tickets_parent_id_foreign');

			$table->dropColumn('parent_id');
		});

		Schema::table('packages', function($table)
		{
			$table->dropForeign('packages_parent_id_foreign');

			$table->dropColumn('parent_id');
		});
	}

}
