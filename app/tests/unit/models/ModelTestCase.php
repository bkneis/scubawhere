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
		parent::setUp();
	}	
	
	/**
	 * Tests if a model is Created, Read, Updated, Soft Deleted, Restored & Force Deleted correctly
	 */
	abstract protected function testCRUD();	
	/**
	 * Tests model validation messages
	 */
	abstract protected function testValidation();
	/**
	 * Tests relationships between any other model. Tests should also check and CRUD functions that effect relationships.
	 */
	abstract protected function testRelationships();
	/**
	 * Tests any functions extending the model
	 */
	abstract protected function testFunctions();
	/**
	 * Tests anything else related to the model
	 */
	abstract protected function testEdges();	
	
}