<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CopyUserDataFromCompaniesTableToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$companies = Company::all();

		$companies->each(function($company)
		{
			$data = [
				'username'   => $company->username,
				'password'   => $company->password,
				'email'      => $company->email,
				'company_id' => $company->id,
			];

			print_r($data);

			$user = new User($data);
			$user->password = $company->password;

			if(!$user->validate())
			{
				print_r($user->errors()->all());
				die('Validation error!');
			}

			$user->save();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('users')->truncate();
	}

}
