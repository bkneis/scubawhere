<?php

class BoatModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbClearTable('boats');
		TestHelper::dbClearTable('companies');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$boat_id = ModelTestHelper::createBoat($company_id);
		$boat = Boat::find($boat_id);
		
		$this->assertNotEquals(0, $boat->id, "Unexpected id value");
		$this->assertEquals($company_id, $boat->company_id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $boat->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $boat->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $boat->capacity, "Unexpected capacity value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $boat->photo, "Unexpected photo value");
		$this->assertNotEquals("0000-00-00 00:00:00", $boat->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $boat->updated_at);
				
		//Update		
		$boat->name = ModelTestHelper::TEST_STRING_UPDATED;
		$boat->description = ModelTestHelper::TEST_STRING_UPDATED;
		$boat->capacity = ModelTestHelper::TEST_INTEGER_UPDATED;
		$boat->photo = ModelTestHelper::TEST_STRING_UPDATED;
		$boat->save();		
		$boat = Boat::find($boat_id);
		
		$this->assertNotEquals(0, $boat->id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $boat->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $boat->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $boat->capacity, "Unexpected capacity value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $boat->photo, "Unexpected photo value");
				
		//Delete
		$boat->delete();
		$boat = Boat::find($boat_id);
		
		$this->assertNull($boat, "Boat not deleted");
	}
	
	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testFunctions(){
		$this->assertTrue(true);
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
