<?php

class AgencyModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbSeedTable('agencies');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$agency_id = ModelTestHelper::createAgency();
		$agency = Agency::find($agency_id);
		
		$this->assertNotEquals(0, $agency->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR, $agency->abbreviation, "Unexpected abbreviation value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $agency->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $agency->description, "Unexpected description value");
		$this->assertNotEquals("0000-00-00 00:00:00", $agency->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $agency->updated_at);
				
		//Update
		$agency->abbreviation = ModelTestHelper::TEST_ABBR_UPDATED;
		$agency->name = ModelTestHelper::TEST_STRING_UPDATED;
		$agency->description = ModelTestHelper::TEST_STRING_UPDATED;
		$agency->save();		
		$agency = Agency::find($agency_id);
		
		$this->assertNotEquals(0, $agency->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR_UPDATED, $agency->abbreviation, "Unexpected abbreviation value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $agency->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $agency->description, "Unexpected description value");
				
		//Delete
		$agency->delete();
		$agency = Agency::find($agency_id);
		
		$this->assertNull($agency, "Agency not deleted");
	}
	
	public function testValidation(){
		$this->assertTrue(true);
	}
	
	public function testRelationships(){
		//$this->refreshTables();
		//TODO
		$this->markTestIncomplete('This test is incomplete!');
	}
	
	public function testFunctions(){
		$this->assertTrue(true);
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
