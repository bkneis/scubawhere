<?php

class DepartureModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbClearTable('sessions');
		TestHelper::dbClearTable('companies');
		TestHelper::dbClearTable('agents');
		TestHelper::dbClearTable('locations');
		TestHelper::dbClearTable('trips');
		TestHelper::dbClearTable('boats');
		TestHelper::dbClearTable('timetables');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$agent_id = ModelTestHelper::createAgent($company_id);
		$location_id = ModelTestHelper::createLocation();
		$trip_id = ModelTestHelper::createTrip($company_id, $location_id);
		$boat_id = ModelTestHelper::createBoat($company_id);
		$timetable_id = ModelTestHelper::createTimetable($company_id);		
		$departure_id = ModelTestHelper::createDeparture($trip_id, $boat_id, $timetable_id);
		$departure = Departure::find($departure_id);
		
		$this->assertNotEquals(0, $departure->id, "Unexpected id value");
		$this->assertEquals($trip_id, $departure->trip_id, "Unexpected id value");
		$this->assertEquals($boat_id, $departure->boat_id, "Unexpected id value");
		$this->assertEquals($timetable_id, $departure->timetable_id, "Unexpected id value");						
		$this->assertEquals(ModelTestHelper::TEST_DATE, $departure->start, "Unexpected start value");
		$this->assertNotEquals("0000-00-00 00:00:00", $departure->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $departure->updated_at);
				
		//Update		
		$departure->start = ModelTestHelper::TEST_DATE_UPDATED;
		$departure->save();		
		$departure = Departure::find($departure_id);
		
		$this->assertNotEquals(0, $departure->id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_DATE_UPDATED, $departure->start, "Unexpected start value");
				
		//Delete
		$departure->delete();
		$departure = Departure::find($departure_id);		
		$this->assertNull($departure, "Departure not deleted");
		
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
