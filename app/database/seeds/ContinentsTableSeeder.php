<?php

class ContinentsTableSeeder extends CSVSeeder {

	public function __construct(){
		$this->tableName = 'continents';
		$this->csvFile = app_path().'/database/seeds/csv/continents.csv';
	}

}
