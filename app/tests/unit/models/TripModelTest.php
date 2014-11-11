<?php

class TripModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbClearTable('companies');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$location_id = ModelTestHelper::createLocation();
		$trip_id = ModelTestHelper::createTrip($company_id, $location_id);
		$trip = Trip::find($trip_id);
		
		$this->assertNotEquals(0, $trip->id, "Unexpected id value");
		$this->assertEquals($company_id, $trip->company_id, "Unexpected id value");
		$this->assertEquals($location_id, $trip->location_id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING, $trip->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $trip->description, "Unexpected description value");		
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $trip->duration, "Unexpected duration value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING, $trip->photo, "Unexpected photo value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $trip->video, "Unexpected video value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $trip->views, "Unexpected views value");
		$this->assertNotEquals("0000-00-00 00:00:00", $trip->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $trip->updated_at);
				
		//Update
		$trip->name = ModelTestHelper::TEST_STRING_UPDATED;
		$trip->description = ModelTestHelper::TEST_STRING_UPDATED;
		$trip->duration = ModelTestHelper::TEST_INTEGER_UPDATED;
		$trip->photo = ModelTestHelper::TEST_STRING_UPDATED;
		$trip->video = ModelTestHelper::TEST_STRING_UPDATED;
		$trip->views = ModelTestHelper::TEST_INTEGER_UPDATED;
		$trip->save();		
		$trip = Trip::find($trip_id);
		
		$this->assertNotEquals(0, $trip->id, "Unexpected id value");				
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $trip->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $trip->description, "Unexpected description value");		
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $trip->duration, "Unexpected duration value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $trip->photo, "Unexpected photo value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $trip->video, "Unexpected video value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $trip->views, "Unexpected views value");
				
		//Delete
		$trip->delete();
		$trip = Trip::find($trip_id);		
		$this->assertNull($trip, "Trip not deleted");
		
		$this->markTestIncomplete('This test needs to be completed! - SOFT DELETIONS');
	}
	
	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testFunctions(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
