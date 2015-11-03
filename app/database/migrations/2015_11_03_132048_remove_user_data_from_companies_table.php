<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUserDataFromCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('companies', function($table)
		{
			$table->dropColumn(['username', 'password', 'email', 'remember_token']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('companies', function($table)
		{
			$table->string('username', 128)->after('id');
			$table->string('password', 60)->after('username');
			$table->string('email', 128)->after('password');
			$table->string('remember_token', 100)->nullable()->after('views');
		});
	}

}
