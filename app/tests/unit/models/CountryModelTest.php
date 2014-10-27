<?php

class CountryModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbSeedTable('countries');
		TestHelper::dbSeedTable('continents');
		TestHelper::dbSeedTable('currencies');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$country = Country::find($country_id);
		
		$this->assertNotEquals(0, $country->id, "Unexpected id value");
		$this->assertEquals($continent_id, $country->continent_id, "Unexpected continent_id value");
		$this->assertEquals($currency_id, $country->currency_id, "Unexpected currency_id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR, $country->abbreviation, "Unexpected abbreviation value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $country->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $country->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $country->flag, "Unexpected flag value");
		$this->assertNotEquals("0000-00-00 00:00:00", $country->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $country->updated_at);
				
		//Update
		$country->abbreviation = ModelTestHelper::TEST_ABBR_UPDATED;
		$country->name = ModelTestHelper::TEST_STRING_UPDATED;
		$country->description = ModelTestHelper::TEST_STRING_UPDATED;
		$country->flag = ModelTestHelper::TEST_STRING_UPDATED;
		$country->save();		
		$country = Country::find($country_id);
		
		$this->assertNotEquals(0, $country->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR_UPDATED, $country->abbreviation, "Unexpected abbreviation value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $country->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $country->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $country->flag, "Unexpected flag value");
				
		//Delete
		$country->delete();
		$country = Country::find($country_id);
		
		$this->assertNull($country, "Country not deleted");
	}
	
	public function testValidation(){
		$this->assertTrue(true);
	}
	
	public function testRelationships(){
		//$this->refreshTables();
		//TODO
	}
	
	public function testFunctions(){
		$this->assertTrue(true);
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
