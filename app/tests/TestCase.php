<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	public static $databaseRefreshed = false;
	
	//TODO make these associative with model => table name	
	public static $models = array(
		'Accommodation', 'Addon', 'Agent',
		'Boat', 'Booking', 'Bookingdetail',
		'Company', 'Customer',
		'Departure',
		'Location',
		'Package', 'Packagefacade', 'Payment',
		'Ticket', 'Timetable', 'Trip'
	);
	
	//TODO add pivots
	
	
	//TODO make these associative with model => table name
	public static $seededModels = array(
		'Agency', 'Certificate', 'Continent', 'Country', 'Paymentgateway', 'Triptype'
	);
	
	/**
	 * Prepare for each test
	 */
	public function setUp()
	{
		parent::setUp();
		//Make sure at least one refresh
		if (!self::$databaseRefreshed) {
			$this->dbRefresh();
			self::$databaseRefreshed = true;
		}
		$this->refreshListeners();
	}	
	
	public function dbMigrate(){		
		echo("\nMigrating database.....");		
		//Migrate the database
		Artisan::call('migrate');
	}
	
	public function dbClear(){
		echo("\nClearing database.....");
				
		//Get count for each table & truncate it if its not empty
		foreach (self::$models as $model){
			echo count(call_user_func(array($model, 'all')));
			if (count(call_user_func(array($model, 'all'))) != 0) {
				//Turn foreign key checks off
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');// <- USE WITH CAUTION!
				//Delete all entries & reset indexes
				DB::table($model)->truncate();
				//Turn foreign key checks on
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');// <- SHOULD RESET ANYWAY BUT JUST TO MAKE SURE!
			}
		}
		
		//Assert all tables are empty
		foreach (self::$models as $model){
			$this->assertCount(0, call_user_func(array($model, 'all')), $model." table should be empty!");
		}
	}
	
	public function dbSeed(){
		echo("\nSeeding database.....");
		//Turn foreign key checks off
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');// <- USE WITH CAUTION!
		//Seed tables
		Artisan::call('db:seed');
		//Turn foreign key checks on
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');// <- SHOULD RESET ANYWAY BUT JUST TO MAKE SURE!
		//Assert seeded tables have entries
		foreach (self::$seededModels as $model){
			$this->assertNotCount(0, call_user_func(array($model, 'all')), $model." table should not be empty!");
		}
	}
	
	public function dbRefresh(){
		echo("\nRefreshing database:");
		$this->dbMigrate();
		$this->dbClear();
		$this->dbSeed();
		echo("\nRefresh complete!\n");
		self::$databaseRefreshed = true;
	}
	
	
	private function refreshListeners(){
		//Reset event listeners on all models
		foreach (self::$models as $model){
			//Flush any exiting listeners
			call_user_func(array($model, 'flushEventListeners'));
			//Reregister listeners
			call_user_func(array($model, 'boot'));
		}
		foreach (self::$seededModels as $model){
			//Flush any exiting listeners
			call_user_func(array($model, 'flushEventListeners'));
			//Reregister listeners
			call_user_func(array($model, 'boot'));
		}		
	}
	
	
	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;
	
		$testEnvironment = 'testing';
	
		return require __DIR__.'/../../bootstrap/start.php';
	}

}
