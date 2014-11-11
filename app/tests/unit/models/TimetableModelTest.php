<?php

class TimetableModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbClearTable('timetables');
		TestHelper::dbClearTable('companies');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$timetable_id = ModelTestHelper::createTimetable($company_id);
		$timetable = Timetable::find($timetable_id);
		
		$this->assertNotEquals(0, $timetable->id, "Unexpected id value");
		$this->assertEquals($company_id, $timetable->company_id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $timetable->weeks, "Unexpected weeks value");
		$this->assertEquals(ModelTestHelper::TEST_JSON, $timetable->schedule, "Unexpected schedule value");		
		$this->assertNotEquals("0000-00-00 00:00:00", $timetable->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $timetable->updated_at);
				
		//Update		
		$timetable->weeks = ModelTestHelper::TEST_INTEGER_UPDATED;
		$timetable->schedule = ModelTestHelper::TEST_JSON_UPDATED;		
		$timetable->save();		
		$timetable = Timetable::find($timetable_id);
		
		$this->assertNotEquals(0, $timetable->id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $timetable->weeks, "Unexpected weeks value");
		$this->assertEquals(ModelTestHelper::TEST_JSON_UPDATED, $timetable->schedule, "Unexpected schedule value");		
				
		//Delete
		$timetable->delete();
		$timetable = Timetable::find($timetable_id);		
		$this->assertNull($timetable, "Timetable not deleted");		
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
