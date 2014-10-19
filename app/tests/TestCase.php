<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {
	
	
	/**
	 * Default preparation for each test
	 */
	public function setUp()
	{
		parent::setUp();
	
		//Migrate to the temporary db
		Artisan::call('migrate');
		
		//@TODO seed tables
	
		$models = array(
			'Accomodation', 'Addon', 'Agency', 'Agent',
			'Boat', 'Booking', 'Bookingdetail',
			'Certificate', 'Company', 'Continent', 'Country', 'Customer',
			'Departure',
			'Location',
			'Package', 'Packagefacade', 'Payment', 'Paymentgateway'.
			'Ticket', 'Timetable', 'Trip', 'Triptype'
		);
	
		//Reset event listeners on all models
		foreach ($models as $model){
			//Flush any exiting listeners
			call_user_func(array($model, 'flushEventListeners'));
			//Reregister listeners
			call_user_func(array($model, 'boot'));
		}
	
		//Assert tables are empty
		foreach ($models as $model){
			$this->assertCount(0, call_user_func(array($model, 'all')), $model." table should be empty!");
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
