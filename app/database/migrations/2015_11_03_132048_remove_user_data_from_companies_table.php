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
			$table->dropColumn(['username', 'password', 'email', 'phone', 'remember_token']);
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
			$table->string('phone', 128)->after('timezone');
			$table->string('remember_token', 100)->nullable()->after('views');
		});

		// Now fill those fields again
		$users = User::whereNotNull('phone')->with('company')->get();

		$users->each(function($user)
		{
			$user->company->username = $user->username;
			$user->company->password = $user->password;
			$user->company->email    = $user->email;
			$user->company->phone    = $user->phone;

			$user->company->updateUniques();
		});
	}

}
