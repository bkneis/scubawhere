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
		
		$this->call('AgenciesTableSeeder');
		$this->call('CertificatesTableSeeder');
		$this->call('ContinentsTableSeeder');
		$this->call('CountriesTableSeeder');
		$this->call('CurrenciesTableSeeder');
		$this->call('PaymentgatewaysTableSeeder');
		$this->call('TriptypesTableSeeder');		
	}

}
