<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommisionableBoolToBookables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addons', function (Blueprint $table) {
			$table->boolean('commissionable')->default(true)->after('parent_id');
		});
		Schema::table('courses', function (Blueprint $table) {
			$table->boolean('commissionable')->default(true)->after('certificate_id');
		});
		Schema::table('packages', function (Blueprint $table) {
			$table->boolean('commissionable')->default(true)->after('parent_id');
		});
		Schema::table('tickets', function (Blueprint $table) {
			$table->boolean('commissionable')->default(true)->after('parent_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('addons', function (Blueprint $table) {
			$table->dropColumn('commissionable');
		});
		Schema::table('courses', function (Blueprint $table) {
			$table->dropColumn('commissionable');
		});
		Schema::table('packages', function (Blueprint $table) {
			$table->dropColumn('commissionable');
		});
		Schema::table('tickets', function (Blueprint $table) {
			$table->dropColumn('commissionable');
		});
	}

}
