<?php

class ContinentModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		//Make sure we start with fresh tables for each test
		if (parent::$dbInitialised) {			
			TestHelper::dbSeedTable('Continents');
		}
		parent::setUp();		
	}
		
	public function testCRUD(){
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$continent = Continent::find($continent_id);
		
		$this->assertNotEquals(0, $continent->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR, $continent->abbreviation, "Unexpected abbreviation value");
		$this->assertEquals(ModelTestHelper::TEST_NAME, $continent->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_DESCRIPTION, $continent->description, "Unexpected description value");
				
		//Update
		$continent->abbreviation = ModelTestHelper::TEST_ABBR_UPDATE;
		$continent->name = ModelTestHelper::TEST_NAME_UPDATE;
		$continent->description = ModelTestHelper::TEST_DESCRIPTION_UPDATE;
		$continent->save();		
		$continent = Continent::find($continent_id);
		
		$this->assertNotEquals(0, $continent->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR_UPDATE, $continent->abbreviation, "Unexpected abbreviation value");
		$this->assertEquals(ModelTestHelper::TEST_NAME_UPDATE, $continent->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_DESCRIPTION_UPDATE, $continent->description, "Unexpected description value");
				
		//Delete
		$continent->delete();
		$continent = Continent::find($continent_id);
		
		$this->assertNull($continent, "Continent not deleted");
	}
	
	public function testValidation(){
		$this->assertTrue(true);
	}
	
	public function testRelationships(){
		//TODO
	}
	
	public function testFunctions(){
		$this->assertTrue(true);
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}
	
}
