<?php

/**
 * Helper class providing constants & functions to be used in tests.
 */
class TestHelper{
		
	/**
	 * Helper function to call any needed migrations on the test database
	 */
	public static function dbMigrate(){
		if (App::runningUnitTests()) {
			echo("\nDB ".getenv('DATABASE_NAME').": Migrating.....");
			//Migrate the database
			Artisan::call('migrate');
		}
	}
	
	
	
	/**
	 * Helper function to clear all unseeded tables in the test database (including pivots)
	 */
	public static function dbClear(){
		if (App::runningUnitTests()) {
			echo("\nDB ".getenv('DATABASE_NAME').": Clearing tables.....");
			self::clearModelTables(TestSettings::$modelTables);
			self::clearTables(TestSettings::$pivotTables);
		}
	}
	
	
	
	/**
	 * Helper function to clear a table in the test database (including pivots)
	 * @param string $table the name of the table to be
	 */
	public static function dbClearTable($table){
		if (App::runningUnitTests()) {
			echo("\nDB ".getenv('DATABASE_NAME').": Clearing ".$table." table.....");
			if (count(DB::table($table)->get()) != 0) {
				//Turn foreign key checks off <- USE WITH CAUTION!
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				//Delete all entries & reset indexes
				DB::table($table)->truncate();
				//Turn foreign key checks on <- SHOULD RESET ANYWAY BUT JUST TO MAKE SURE!
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
			}
		}
	}
	
	
	
	/**
	 * Helper function to clear and reseed all seeded tables in the test database
	 */
	public static function dbSeed(){
		if (App::runningUnitTests()) {
			echo("\nDB ".getenv('DATABASE_NAME').": Seeding.....");
			//Turn foreign key checks off
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');// <- USE WITH CAUTION!
			//Seed tables
			Artisan::call('db:seed');
			//Turn foreign key checks on
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');// <- SHOULD RESET ANYWAY BUT JUST TO MAKE SURE!
		}
	}
	
	
	
	/**
	 * Helper function to clear and reseed a single seeded table in the test database
	 * @param string $seederPrefix first part of the seeder class name (eg PREFIXTableSeeder)
	 */
	public static function dbSeedTable($seederPrefix){
		if (App::runningUnitTests()) {
			echo("\nDB ".getenv('DATABASE_NAME').": Seeding ".$seederPrefix.".....");
			//Turn foreign key checks off
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');// <- USE WITH CAUTION!
			//Seed tables
			Artisan::call('db:seed', array('--class='.$seederPrefix.'TableSeeder'));
			//Turn foreign key checks on
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');// <- SHOULD RESET ANYWAY BUT JUST TO MAKE SURE!
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
	public static function dbRefresh($includeReset = false){
		if (App::runningUnitTests()) {
				
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
	
			self::dbMigrate();
			self::dbClear();
			self::dbSeed();
		}
	}
	
	
	
	/**
	 * Private helper to quickly clear tables with models
	 * This function will also assert that all of the tables are empty
	 * @param array $modelTableArray to clear
	 */
	private static function clearModelTables(array $modelTableArray){
		if (App::runningUnitTests()) {
			//Get count for each table & truncate it if its not empty
			foreach ($modelTableArray as $model => $table){
				self::dbClearTable($table);
			}
		}
	}
	
	
	
	/**
	 * Private helper to quickly clear tables without models
	 * This function will also assert that all of the tables are empty
	 * @param array $tableArray to clear
	 */
	private static function clearTables(array $tableArray){
		if (App::runningUnitTests()) {
			//Truncate every table in the array
			foreach ($tableArray as $table){
				self::dbClearTable($table);
			}
		}
	}
	
	
	
	/**
	 * Drops & resets any event listeners associated with models.
	 * Called in the <code>TestCase</code> <code>setUp()</code> function
	 * Workaround for an issue when testing: https://github.com/laravel/framework/issues/1181
	 */
	public static function refreshListeners(){
		if (App::runningUnitTests()) {
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
	}
	
}