<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	public static $databaseMigrated = false;	
	
	public static $testModels = array(
		'Accommodation', 'Addon', 'Agency', 'Agent',
		'Boat', 'Booking', 'Bookingdetail',
		'Certificate', 'Company', 'Continent', 'Country', 'Customer',
		'Departure',
		'Location',
		'Package', 'Packagefacade', 'Payment', /*'Paymentgateway',*/
		'Ticket', 'Timetable', 'Trip', 'Triptype'
	);
	
	/**
	 * Prepare for each test
	 */
	public function setUp()
	{
		parent::setUp();
		
		//Check if we need to migrate the database		
		if (!self::$databaseMigrated) {
			//Migrate the database
			Artisan::call('migrate');
			
			//@TODO seed tables
			//Artisan::call('db::seed');
			
			//Assert tables are empty
			foreach (self::$testModels as $model){
				$this->assertCount(0, call_user_func(array($model, 'all')), $model." table should be empty!");
			}
			
			self::$databaseMigrated = true;
		}
		
		//Reset event listeners on all models
		foreach (self::$testModels as $model){
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
