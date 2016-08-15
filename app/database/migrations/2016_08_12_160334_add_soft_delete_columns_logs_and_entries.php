<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteColumnsLogsAndEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('logs', function($table)
        {
            $table->softDeletes();
        });

        Schema::table('log_entries', function($table)
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
        Schema::table('logs', function($table)
        {
            $table->dropColumn('deleted_at');
        });

        Schema::table('log_entries', function($table)
        {
            $table->dropColumn('deleted_at');
        });
	}

}
