<?php

class AccommodationModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbClearTable('accommodations');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id);
		$accommodation_id = ModelTestHelper::createAccommodation($company_id);
		$accommodation = Accommodation::find($accommodation_id);
		
		$this->assertNotEquals(0, $accommodation->id, "Unexpected id value");
		$this->assertEquals($company_id, $accommodation->company_id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $accommodation->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $accommodation->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $accommodation->photo, "Unexpected photo value");
		$this->assertNotEquals("0000-00-00 00:00:00", $accommodation->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $accommodation->updated_at);
				
		//Update		
		$accommodation->name = ModelTestHelper::TEST_STRING_UPDATED;
		$accommodation->description = ModelTestHelper::TEST_STRING_UPDATED;
		$accommodation->photo = ModelTestHelper::TEST_STRING_UPDATED;
		$accommodation->save();		
		$accommodation = Accommodation::find($accommodation_id);
		
		$this->assertNotEquals(0, $accommodation->id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $accommodation->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $accommodation->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $accommodation->photo, "Unexpected photo value");
				
		//Delete
		$accommodation->delete();
		$accommodation = Accommodation::find($accommodation_id);
		
		$this->assertNull($accommodation, "Accommodation not deleted");
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
