<?php

class ContinentModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function testCRUD(){
		
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$continent = Continent::find($continent_id);
		
		$this->assertNotEquals(0, $continent->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR, $continent->abbreviation, "Unexpected abbreviation value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $continent->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $continent->description, "Unexpected description value");
		$this->assertNotEquals("0000-00-00 00:00:00", $continent->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $continent->updated_at);
				
		//Update
		$continent->abbreviation = ModelTestHelper::TEST_ABBR_UPDATED;
		$continent->name = ModelTestHelper::TEST_STRING_UPDATED;
		$continent->description = ModelTestHelper::TEST_STRING_UPDATED;
		$continent->save();		
		$continent = Continent::find($continent_id);
		
		$this->assertNotEquals(0, $continent->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR_UPDATED, $continent->abbreviation, "Unexpected abbreviation value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $continent->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $continent->description, "Unexpected description value");
				
		//Delete
		$continent->delete();
		$continent = Continent::find($continent_id);
		
		$this->assertNull($continent, "Continent not deleted");
	}
	
	public function testValidation(){
		$this->assertTrue(true);
	}
	
	public function testRelationships(){
		$this->assertTrue(true);
	}
	
	public function testFunctions(){
		$this->assertTrue(true);
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
