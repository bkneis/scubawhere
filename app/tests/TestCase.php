<?php

use Monolog\Handler\TestHandler;
class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Flag to check if at least one refresh has been ran for the <code>TestCase</code>
	 * @var bool flag
	 */
	public static $dbInitialised = false;

	
	
	/**
	 * Default <code>setUp()</code> for each <code>TestCase</code>
	 * Will ensure the database has been refreshed at least once (but does not reset migrations)
	 * Also refreshes any event listeners on the models
	 */
	public function setUp()
	{
		parent::setUp();		
		//Make sure at least one refresh is done
		if (!self::$dbInitialised) {
			echo("\nDB ".getenv('DATABASE_NAME').": Initialising.....");
			TestHelper::dbRefresh(true);
			self::$dbInitialised = true;
			echo("\nDB ".getenv('DATABASE_NAME').": Initialisation complete!");
		}
		
		//TODO keep an eye on this - might be needed but it does slow things down
		TestHelper::refreshListeners();
	}

	
	
	/**
	 * Creates the application.
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;
		$testEnvironment = 'testing';	
		return require __DIR__.'/../../bootstrap/start.php';
	}

}
