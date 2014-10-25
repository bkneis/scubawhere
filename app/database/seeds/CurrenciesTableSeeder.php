<?php

class CurrenciesTableSeeder extends CSVSeeder {

	public function __construct(){
		$this->tableName = 'currencies';
		$this->csvFile = app_path().'/database/seeds/csv/currencies.csv';
	}

}
