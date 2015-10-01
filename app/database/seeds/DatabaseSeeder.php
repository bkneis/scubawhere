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
        
		// Disable foreign key checks
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		$this->call('AgenciesTableSeeder');
		$this->call('CertificatesTableSeeder');
		$this->call('ContinentsTableSeeder');
		$this->call('CountriesTableSeeder');
		$this->call('CurrenciesTableSeeder');
		$this->call('PaymentgatewaysTableSeeder');
		$this->call('TagsTableSeeder');
        
		// Disable foreign key checks
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}
