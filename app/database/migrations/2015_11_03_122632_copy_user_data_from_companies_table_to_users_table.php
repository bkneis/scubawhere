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
				'phone'      => $company->phone, // Just to pass validation (User model was updated to require a phone number after this migration)
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

			$user->phone = null;

			if(!$user->save())
			{
				print_r($user->errors()->all());
				die('DB writing error!');
			}
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
