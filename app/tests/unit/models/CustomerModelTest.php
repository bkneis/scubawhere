<?php

class CustomerModelTest extends ModelTestCase {
		
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
		$customer_id = ModelTestHelper::createCustomer($country_id, $company_id);
		$customer = Customer::find($customer_id);
		
		$this->assertNotEquals(0, $customer->id, "Unexpected id value");		
		$this->assertEquals($country_id, $customer->country_id, "Unexpected id value");
		$this->assertEquals($company_id, $customer->company_id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_EMAIL, $customer->email, "Unexpected email value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $customer->firstname, "Unexpected firstname value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $customer->lastname, "Unexpected lastname value");
		$this->assertEquals(ModelTestHelper::TEST_BOOL, $customer->verified, "Unexpected verified value");		
		$this->assertEquals(ModelTestHelper::TEST_DAY, $customer->birthday, "Unexpected birthday value");
		$this->assertEquals(ModelTestHelper::TEST_GENDER, $customer->gender, "Unexpected gender value");				
		$this->assertEquals(ModelTestHelper::TEST_STRING, $customer->address_1, "Unexpected address_1 value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $customer->address_1, "Unexpected address_2 value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $customer->city, "Unexpected city value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $customer->county, "Unexpected county value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $customer->postcode, "Unexpected postcode value");
		$this->assertEquals(ModelTestHelper::TEST_PHONE, $customer->phone, "Unexpected phone value");
		$this->assertEquals(ModelTestHelper::TEST_DAY, $customer->last_dive, "Unexpected last_dive value");		
		$this->assertNotEquals("0000-00-00 00:00:00", $customer->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $customer->updated_at);
				
		//Update		
		$customer->email = ModelTestHelper::TEST_EMAIL_UPDATED;
		$customer->firstname = ModelTestHelper::TEST_STRING_UPDATED;
		$customer->lastname = ModelTestHelper::TEST_STRING_UPDATED;		
		$customer->verified = ModelTestHelper::TEST_BOOL_UPDATED;
		$customer->birthday = ModelTestHelper::TEST_DAY_UPDATED;
		$customer->gender = ModelTestHelper::TEST_GENDER_UPDATED;		
		$customer->address_1 = ModelTestHelper::TEST_STRING_UPDATED;
		$customer->address_2 = ModelTestHelper::TEST_STRING_UPDATED;
		$customer->city = ModelTestHelper::TEST_STRING_UPDATED;
		$customer->county = ModelTestHelper::TEST_STRING_UPDATED;
		$customer->postcode = ModelTestHelper::TEST_STRING_UPDATED;
		$customer->phone = ModelTestHelper::TEST_PHONE_UPDATED;
		$customer->last_dive = ModelTestHelper::TEST_DAY_UPDATED;
		$customer->save();		
		$customer = Customer::find($customer_id);
		
		$this->assertNotEquals(0, $customer->id, "Unexpected id value");				
		$this->assertEquals(ModelTestHelper::TEST_EMAIL_UPDATED, $customer->email, "Unexpected email value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $customer->firstname, "Unexpected firstname value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $customer->lastname, "Unexpected lastname value");
		$this->assertEquals(ModelTestHelper::TEST_BOOL_UPDATED, $customer->verified, "Unexpected verified value");		
		$this->assertEquals(ModelTestHelper::TEST_DAY_UPDATED, $customer->birthday, "Unexpected birthday value");
		$this->assertEquals(ModelTestHelper::TEST_GENDER_UPDATED, $customer->gender, "Unexpected gender value");				
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $customer->address_1, "Unexpected address_1 value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $customer->address_1, "Unexpected address_2 value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $customer->city, "Unexpected city value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $customer->county, "Unexpected county value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $customer->postcode, "Unexpected postcode value");
		$this->assertEquals(ModelTestHelper::TEST_PHONE_UPDATED, $customer->phone, "Unexpected phone value");
		$this->assertEquals(ModelTestHelper::TEST_DAY_UPDATED, $customer->last_dive, "Unexpected last_dive value");	
				
		//Delete
		$customer->delete();
		$customer = Customer::find($customer_id);		
		$this->assertNull($customer, "Customer not deleted");
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
