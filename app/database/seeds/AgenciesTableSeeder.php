<?php

class AgenciesTableSeeder extends CSVSeeder {

	public function __construct(){
		$this->tableName = 'agencies';
		$this->csvFile = app_path().'/database/seeds/csv/agencies.csv';
	}

}
