<?php

class CountriesTableSeeder extends CSVSeeder {

	public function __construct(){
		$this->tableName = 'countries';
		$this->csvFile = app_path().'/database/seeds/csv/countries.csv';
	}

}
