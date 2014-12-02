<?php

class TagsTableSeeder extends CSVSeeder {

	public function __construct(){
		$this->tableName = 'tags';
		$this->csvFile = app_path().'/database/seeds/csv/tags.csv';
	}

}
