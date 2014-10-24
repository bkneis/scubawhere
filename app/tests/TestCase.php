<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Flag to check if at least one refresh has been ran for the <code>TestCase</code>
	 * @var bool flag
	 */
	public static $databaseRefreshed = false;
	
	
	
	/**
	 * Default <code>setUp()</code> for each <code>TestCase</code>
	 * Will ensure the database has been refreshed at least once (but does not reset migrations)
	 * Also refreshes any event listeners on the models
	 */
	public function setUp()
	{
		parent::setUp();		
		//Make sure at least one refresh is done
		if (!self::$databaseRefreshed) {
			echo("\nDB ".getenv('DATABASE_NAME').": Performing initial setup.....");
			$this->dbRefresh(true);
			self::$databaseRefreshed = true;			
		}
		$this->refreshListeners();
	}
	
	
	
	/**
	 * Helper function to call any needed migrations on the test database
	 */
	public function dbMigrate(){		
		echo("\nDB ".getenv('DATABASE_NAME').": Migrating.....");
		//Migrate the database
		Artisan::call('migrate');
	}
	
	
	
	/**
	 * Helper function to clear all unseeded tables in the test database (including pivots)
	 */
	public function dbClear(){
		echo("\nDB ".getenv('DATABASE_NAME').": Clearing tables.....");
		$this->clearModelTables(TestSettings::$modelTables);
		$this->clearTables(TestSettings::$pivotTables);
	}
	
	
	
	/**
	 * Helper function to clear and reseed all seeded tables in the test database
	 */
	public function dbSeed(){
		echo("\nDB ".getenv('DATABASE_NAME').": Seeding.....");
		//Turn foreign key checks off
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');// <- USE WITH CAUTION!
		//Seed tables
		Artisan::call('db:seed');
		//Turn foreign key checks on
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');// <- SHOULD RESET ANYWAY BUT JUST TO MAKE SURE!
		//Assert seeded tables have entries
		foreach (TestSettings::$seededModelTables as $model => $table){
			$this->assertNotCount(0, call_user_func(array($model, 'all')), $model." table should not be empty!");
		}
	}
	
	
	
	/**
	 * Helper function to set up a 'clean' test database
	 * It will do the following:
	 * Reset all migrations (optional)
	 * Perform any required migrations
	 * Clear all unseeded & pivot tables
	 * Reseed any seeded tables
	 * @param bool $includeReset flag to set whether or not rollback all migrations first
	 */
	public function dbRefresh($includeReset = false){
		echo("\nDB ".getenv('DATABASE_NAME').": Refreshing.....");
		
		//@TODO Fix issue with rollbacks before this can be used
// 		if ($includeReset) {
// 			echo("\nDB ".getenv('DATABASE_NAME').": Rolling back migrations.....");
// 			//Turn foreign key checks off <- USE WITH CAUTION!
// 			DB::statement('SET FOREIGN_KEY_CHECKS=0;');
// 			//Rollback all migrations			
// 			Artisan::call('migrate:reset');
// 			//Turn foreign key checks on <- SHOULD RESET ANYWAY BUT JUST TO MAKE SURE!
// 			DB::statement('SET FOREIGN_KEY_CHECKS=1;');
// 		}

		$this->dbMigrate();
		$this->dbClear();
		$this->dbSeed();
		echo("\nDB ".getenv('DATABASE_NAME').": Refresh complete!\n");
		self::$databaseRefreshed = true;
	}
	
	
	
	/**
	 * Private helper to quickly clear tables with models
	 * This function will also assert that all of the tables are empty
	 * @param array $modelTableArray to clear
	 */
	private function clearModelTables(array $modelTableArray){
		
		//Get count for each table & truncate it if its not empty
		foreach ($modelTableArray as $model => $table){
			if (count(DB::table($table)->get()) != 0) {
				//Turn foreign key checks off <- USE WITH CAUTION!
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				//Delete all entries & reset indexes
				DB::table($table)->truncate();
				//Turn foreign key checks on <- SHOULD RESET ANYWAY BUT JUST TO MAKE SURE!
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
			}
		}
		
		//Assert all tables are empty
		foreach ($modelTableArray as $model => $table){
			$this->assertCount(0, DB::table($table)->get(), $table." table should be empty!");
		}
	}
	
	
	
	/**
	 * Private helper to quickly clear tables without models
	 * This function will also assert that all of the tables are empty
	 * @param array $tableArray to clear
	 */
	private function clearTables(array $tableArray){
		//Truncate every table in the array
		foreach ($tableArray as $table){			
			//Turn foreign key checks off <- USE WITH CAUTION!
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');
			//Delete all entries & reset indexes
			DB::table($table)->truncate();
			//Turn foreign key checks on <- SHOULD RESET ANYWAY BUT JUST TO MAKE SURE!
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');			
		}
		
		//Assert all tables are empty
		foreach ($tableArray as $table){
			$this->assertCount(0, DB::table($table)->get(), $table." table should be empty!");
		}
	}
	
	
	
	/**
	 * Drops & resets any event listeners associated with models.
	 * Called in the <code>TestCase</code> <code>setUp()</code> function
	 * Workaround for an issue when testing: https://github.com/laravel/framework/issues/1181
	 */
	private function refreshListeners(){
		//Reset event listeners on all models
		foreach (TestSettings::$modelTables as $model => $table){			
			call_user_func(array($model, 'flushEventListeners'));			
			call_user_func(array($model, 'boot'));
		}
		foreach (TestSettings::$seededModelTables as $model => $table){			
			call_user_func(array($model, 'flushEventListeners'));			
			call_user_func(array($model, 'boot'));
		}		
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
