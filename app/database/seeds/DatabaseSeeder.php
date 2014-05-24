<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');
		$this->call('ContinentsTableSeeder');
		$this->call('CountriesTableSeeder');
		$this->call('RegionsTableSeeder');
		$this->call('AgenciesTableSeeder');
		$this->call('CertificatesTableSeeder');
	}

}
