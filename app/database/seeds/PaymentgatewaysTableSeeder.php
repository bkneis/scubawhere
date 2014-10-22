<?php

class PaymentgatewaysTableSeeder extends CSVSeeder {

	public function __construct(){
		$this->tableName = 'paymentgateways';
		$this->csvFile = app_path().'/database/seeds/csv/paymentgateways.csv';
	}

}
