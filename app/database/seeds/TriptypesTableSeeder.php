<?php

class TriptypesTableSeeder extends CSVSeeder {
	
	public function __construct(){
		$this->tableName = 'triptypes';
		$this->csvFile = app_path().'/database/seeds/csv/triptypes.csv';
	}

}
