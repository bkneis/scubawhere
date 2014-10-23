<?php

class CertificatesTableSeeder extends CSVSeeder {

	public function __construct(){
		$this->tableName = 'certificates';
		$this->csvFile = app_path().'/database/seeds/csv/certificates.csv';
	}

}
