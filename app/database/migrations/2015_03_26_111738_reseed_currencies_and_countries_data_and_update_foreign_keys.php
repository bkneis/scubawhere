<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReseedCurrenciesAndCountriesDataAndUpdateForeignKeys extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// First step: get the current lists of countries and currencies to later assign the new correct IDs
		$oldCurrencies = Currency::lists('name', 'id'); // [id => name]
		echo "\nOld currencies: ".count($oldCurrencies);

		$oldCountries  = Country::lists('name', 'id');
		echo "\nOld countries: ".count($oldCountries);

		// Disable foreign key checks
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		// Reseed database
		Artisan::call('db:seed', array('--class=CurrenciesTableSeeder'));
		Artisan::call('db:seed', array('--class=CountriesTableSeeder'));

		// Get array of new country and currency IDs
		$newCurrencies = Currency::lists('id', 'name'); // [name => id]
		echo "\nNew currencies: ".count($newCurrencies);

		$newCountries = Country::lists('id', 'name');
		echo "\nNew countries: ".count($newCountries);

		// Search and replace all updated IDs
		// Company table (country_id & currency_id)
		$companies = Company::all();
		foreach($companies as $company)
		{
			if(array_key_exists($oldCurrencies[$company->currency_id], $newCurrencies))
				$company->currency_id = $newCurrencies[ $oldCurrencies[$company->currency_id] ];
			else
				echo "\n".'Could not reassign currency '.$company->currency_id.' => '.$oldCurrencies[$company->currency_id];

			if(array_key_exists(strtoupper($oldCountries[$company->country_id]), $newCountries))
				$company->country_id  = $newCountries[ strtoupper($oldCountries[$company->country_id]) ];
			else
				echo "\n".'Could not reassign country '.$company->country_id.' => '.$oldCountries[$company->country_id];

			$company->timestamps = false;
			$company->forceSave();
		}

		// Customer table (country_id)
		$customers = Customer::all();
		foreach($customers as $customer)
		{
			if(empty($customer->country_id))
				continue;

			if(array_key_exists(strtoupper($oldCountries[$customer->country_id]), $newCountries))
				$customer->country_id = $newCountries[ strtoupper($oldCountries[$customer->country_id]) ];
			else
				echo "\n".'Could not reassign country '.$customer->country_id.' => '.$oldCountries[$customer->country_id];

			$customer->timestamps = false;
			$customer->forceSave();
		}

		// Payments table (currency_id)
		$payments = Payment::all();
		foreach($payments as $payment)
		{
			if(array_key_exists($oldCurrencies[$payment->currency_id], $newCurrencies))
				$payment->currency_id = $newCurrencies[ $oldCurrencies[$payment->currency_id] ];
			else
				echo "\n".'Could not reassign currency '.$payment->currency_id.' => '.$oldCurrencies[$payment->currency_id];

			$payment->timestamps = false;
			$payment->forceSave();
		}

		// Refunds table (currency_id)
		$refunds = Refund::all();
		foreach($refunds as $refund)
		{
			if(array_key_exists($oldCurrencies[$refund->currency_id], $newCurrencies))
				$refund->currency_id = $newCurrencies[ $oldCurrencies[$refund->currency_id] ];
			else
				echo "\n".'Could not reassign currency '.$refund->currency_id.' => '.$oldCurrencies[$refund->currency_id];

			$refund->timestamps = false;
			$refund->forceSave();
		}

		// Re-enable foreign key checks
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Can't rewind, because old seeder data and thus old IDs are no longer available
	}

}
