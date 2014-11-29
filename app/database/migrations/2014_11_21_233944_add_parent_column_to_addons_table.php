<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentColumnToAddonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addons', function($table)
		{
			$table->integer('parent_id')->unsigned()->after('compulsory')->nullable()->default(null);

			$table->foreign('parent_id')->references('id')->on('addons')->onUpdate('cascade')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('addons', function($table)
		{
			$table->dropForeign('addons_parent_id_foreign');

			$table->dropColumn('parent_id');
		});
	}

}
