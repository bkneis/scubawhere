<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCertificateIdToCoursesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('courses', function($table)
		{
			$table->integer('certificate_id')->unsigned()->nullable()->default(null)->after('capacity');

			$table->foreign('certificate_id')->references('id')->on('certificates')->onUpdate('cascade')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('courses', function($table)
		{
			$table->dropForeign('courses_certificate_id_foreign');

			$table->dropColumn('certificate_id');
		});
	}

}
