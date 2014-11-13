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
			TestHelper::dbRefresh(true);
			self::$dbInitialised = true;
		}
		
		//TODO keep an eye on this - might be needed but it does slow things down
		TestHelper::refreshListeners();
	}

	
	
	/**
	 * Custom assertion to handle decimal values.
	 * A delta is used to check accurracy and negate any 'junk'
	 * at the end of the expected value.
	 * @param number $expected value
	 * @param number $actual value
	 * @param string $message to display on fail
	 * @param number $delta range that actual value must fall within
	 */
	public function assertEqualsDecimal($expected, $actual, $message = "", $delta = 0)
	{		
		$this->assertGreaterThan($expected - $delta, $actual, $message." - value not within delta");
		$this->assertLessThan($expected + $delta, $actual, $message." - value not within delta");
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
