<?php

class TripModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function testCRUD(){
		
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
				
		//Delete - soft, restore, force
		$trip->delete();		
		$trip = Trip::find($trip_id);
		$this->assertNull($trip, "Trip not soft deleted");
		
		$trip = Trip::onlyTrashed()->where('id', '=', $trip_id)->first();
		$this->assertNotNull($trip, "Trip soft deleted but cant be found");
		$this->assertNotNull($trip->deleted_at);
		
		Trip::onlyTrashed()->where('id', '=', $trip_id)->restore();
		$trip = Trip::find($trip_id);
		$this->assertNotNull($trip, "Trip not restored");
		$this->assertNull($trip->deleted_at);
				
		Trip::withTrashed()->where('id', '=', $trip_id)->forceDelete();
		$trip = Trip::withTrashed()->where('id', '=', $trip_id)->first();
		$this->assertNull($trip, "Trip not force deleted");
	}
	
	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testRelationships(){	
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$location_id = ModelTestHelper::createLocation();
		$trip_id = ModelTestHelper::createTrip($company_id, $location_id);
		$trip = Trip::find($trip_id);
	
		$this->assertNotNull($trip->company, "Unexpected company relationship value");
		$this->assertNotNull($trip->location, "Unexpected location relationship value");
	}
	
	public function testFunctions(){
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$location_id = ModelTestHelper::createLocation();
		$trip_id = ModelTestHelper::createTrip($company_id, $location_id);
		$trip = Trip::find($trip_id);
		
		$this->assertTrue($trip->deletable, "Unexpected deletable value");
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
