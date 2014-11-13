<?php

class DepartureModelTest extends ModelTestCase {
		
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
				
		//Delete - soft, restore, force
		$departure->delete();		
		$departure = Departure::find($departure_id);
		$this->assertNull($departure, "Departure not soft deleted");
		
		$departure = Departure::onlyTrashed()->where('id', '=', $departure_id)->first();
		$this->assertNotNull($departure, "Departure soft deleted but cant be found");
		$this->assertNotNull($departure->deleted_at);
		
		Departure::onlyTrashed()->where('id', '=', $departure_id)->restore();
		$departure = Departure::find($departure_id);
		$this->assertNotNull($departure, "Departure not restored");
		$this->assertNull($departure->deleted_at);
				
		Departure::withTrashed()->where('id', '=', $departure_id)->forceDelete();
		$departure = Departure::withTrashed()->where('id', '=', $departure_id)->first();
		$this->assertNull($departure, "Departure not force deleted");
	}
	
	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testRelationships(){
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
	
		$this->assertNotNull($departure->trip, "Unexpected trip relationship value");
		$this->assertNotNull($departure->boat, "Unexpected boat relationship value");
	}
	
	public function testFunctions(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
