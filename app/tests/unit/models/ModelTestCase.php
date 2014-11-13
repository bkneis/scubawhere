<?php

/**
 * Abstract class defining functions used to test models.
 * Any classes extending this one should also ensure <code>parent::setUp()</code> is
 * called in the child <code>setUp()</code> function.
 * Any models that do not have functionality that the tests decscribe should implement 
 * the test with <code>$this->assertTrue(true)</code> to ensure it is still being called correctly.
 */
abstract class ModelTestCase extends TestCase {
	
	/**
	 * Calls <code>setUp()</code> on parent <code>TestCase</code>
	 */
	public function setUp()
	{
		if (parent::$dbInitialised) {
			TestHelper::dbClear();
		}
		parent::setUp();
	}	
	/**
	 * Tests if a model is Created, Read, Updated, Soft Deleted, Restored & Force Deleted correctly
	 */
	abstract public function testCRUD();	
	/**
	 * Tests model validation messages
	 */
	abstract public function testValidation();
	/**
	 * Tests model belongsTo relationships only
	 */
	abstract public function testRelationships();
	/**
	 * Tests any functions and attributes extending the model
	 */
	abstract public function testFunctions();
	/**
	 * Tests anything else related to the model
	 */
	abstract public function testEdges();
	
	
	
}