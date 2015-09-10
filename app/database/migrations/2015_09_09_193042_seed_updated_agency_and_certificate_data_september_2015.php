<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedUpdatedAgencyAndCertificateDataSeptember2015 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Eloquent::unguard();

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		DB::table('certificates')->truncate();
		DB::table('agencies')->truncate();

		Artisan::call('db:seed', array('--class=AgenciesTableSeeder'));
		Artisan::call('db:seed', array('--class=CertificatesTableSeeder'));

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// This operation is not reversible
	}

}
