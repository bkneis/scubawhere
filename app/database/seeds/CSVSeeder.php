<?php

class CSVSeeder extends Seeder
{
	/**
	 * Name of the table to seed
	 * @var string
	 */
	protected $tableName;

	/**
	 * Path to the .csv file where the data for the table resides
	 * @var string
	 */
	protected $csvFile;

	/**
	 * Run the seeder to import data from a .csv file
	 */
	public function run()
	{
		//Clear anything out from the current table & reset any auto increments
		DB::table($this->tableName)->truncate();
		//Read the data from the .csv file
		$data = $this->readCSV($this->$csvFile, ',');
		//Seed the table if data was returned
		if ($seedData !== false) {
			DB::table($this->tableName)->insert($data);
		}		
	}

	/**
	 * Read data from a given CSV file and return as array
	 * @param string $csvFile path
	 * @param string $deliminator separating fields
	 * @return array if read is successful, bool false if not
	 */
	private function readCSV($csvFile, $deliminator = ",")
	{
		//Check the file exists & we can read it
		if(!file_exists($csvFile) || !is_readable($csvFile)) {			
			return false;
		}

		$headings = null;
		$data = array();

		//Open up the file and read in the data into an array
		if(($fileHandle = fopen($csvFile, 'r')) !== false) {
			while(($row = fgetcsv($fileHandle, 1000, $deliminator)) !== false) {
				if(!$headings) {
					$headings = $row;
				} else {
					$data[] = array_combine($headings, $row);
				}
			}
			fclose($fileHandle);
		}
		return $data;
	}
}