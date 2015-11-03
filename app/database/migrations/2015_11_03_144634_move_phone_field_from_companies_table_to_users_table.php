<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MovePhoneFieldFromCompaniesTableToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table)
		{
			$table->string('phone', 128)->after('email');
		});

		$users = User::with('company')->get();

		$users->each(function($user)
		{
			$user->phone = $user->company->phone;
			$user->updateUniques();
		});

		Schema::table('companies', function($table)
		{
			$table->dropColumn('phone');
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
			$table->string('phone', 128)->after('timezone');
		});

		$companies = Company::all();

		$companies->each(function($company)
		{
			$company->phone = $company->users()->whereNotNull('phone')->first()->phone;
			$company->updateUniques();
		});

		Schema::table('users', function($table)
		{
			$table->dropColumn('phone');
		});
	}

}
