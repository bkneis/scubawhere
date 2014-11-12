<?php

class AddonModelTest extends ModelTestCase {
		
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
		$addon_id = ModelTestHelper::createAddon($company_id);
		$addon = Addon::find($addon_id);
		
		$this->assertNotEquals(0, $addon->id, "Unexpected id value");
		$this->assertEquals($company_id, $addon->company_id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $addon->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $addon->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $addon->price, "Unexpected price value");
		$this->assertEquals(ModelTestHelper::TEST_BOOL, $addon->compulsory, "Unexpected compulsory value");
		$this->assertNotEquals("0000-00-00 00:00:00", $addon->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $addon->updated_at);
				
		//Update		
		$addon->name = ModelTestHelper::TEST_STRING_UPDATED;
		$addon->description = ModelTestHelper::TEST_STRING_UPDATED;
		$addon->price = ModelTestHelper::TEST_INTEGER_UPDATED;
		$addon->compulsory = ModelTestHelper::TEST_BOOL_UPDATED;
		$addon->save();		
		$addon = Addon::find($addon_id);
		
		$this->assertNotEquals(0, $addon->id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $addon->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $addon->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $addon->price, "Unexpected price value");
		$this->assertEquals(ModelTestHelper::TEST_BOOL_UPDATED, $addon->compulsory, "Unexpected compulsory value");
				
		//Delete
		$addon->delete();
		$addon = Addon::find($addon_id);		
		$this->assertNull($addon, "Addon not deleted");
		
		$this->markTestIncomplete('This test needs to be completed! - SOFT DELETIONS');
	}
	
	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testRelationships(){
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$addon_id = ModelTestHelper::createAddon($company_id);
		$addon = Addon::find($addon_id);
		
		$this->assertNotNull($addon->company, "Unexpected company relationship value");
	}
	
	public function testFunctions(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
