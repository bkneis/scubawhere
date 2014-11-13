<?php

class AgentModelTest extends ModelTestCase {
		
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
		$agent = Agent::find($agent_id);
		
		$this->assertNotEquals(0, $agent->id, "Unexpected id value");
		$this->assertEquals($company_id, $agent->company_id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING, $agent->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_URL, $agent->website, "Unexpected website value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $agent->branch_name, "Unexpected branch_name value");
		$this->assertEquals(ModelTestHelper::TEST_ADDRESS, $agent->branch_address, "Unexpected branch_address value");
		$this->assertEquals(ModelTestHelper::TEST_PHONE, $agent->branch_phone, "Unexpected branch_phone value");
		$this->assertEquals(ModelTestHelper::TEST_EMAIL, $agent->branch_email, "Unexpected branch_email value");
		$this->assertEquals(ModelTestHelper::TEST_ADDRESS, $agent->billing_address, "Unexpected billing_address value");
		$this->assertEquals(ModelTestHelper::TEST_PHONE, $agent->billing_phone, "Unexpected billing_phone value");
		$this->assertEquals(ModelTestHelper::TEST_EMAIL, $agent->billing_email, "Unexpected billing_email value");
		$this->assertEquals(ModelTestHelper::TEST_DECIMAL, $agent->commission, "Unexpected commission value");
		$this->assertEquals(ModelTestHelper::TEST_TERMS, $agent->terms, "Unexpected terms value");		
		$this->assertNotEquals("0000-00-00 00:00:00", $agent->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $agent->updated_at);
				
		//Update		
		$agent->name = ModelTestHelper::TEST_STRING_UPDATED;
		$agent->website = ModelTestHelper::TEST_URL_UPDATED;
		$agent->branch_name = ModelTestHelper::TEST_STRING_UPDATED;
		$agent->branch_address = ModelTestHelper::TEST_ADDRESS_UPDATED;
		$agent->branch_phone = ModelTestHelper::TEST_PHONE_UPDATED;
		$agent->branch_email = ModelTestHelper::TEST_EMAIL_UPDATED;
		$agent->billing_address = ModelTestHelper::TEST_ADDRESS_UPDATED;
		$agent->billing_phone = ModelTestHelper::TEST_PHONE_UPDATED;
		$agent->billing_email = ModelTestHelper::TEST_EMAIL_UPDATED;
		$agent->commission = ModelTestHelper::TEST_DECIMAL_UPDATED;
		$agent->terms = ModelTestHelper::TEST_TERMS_UPDATED;
		$agent->save();		
		$agent = Agent::find($agent_id);
		
		$this->assertNotEquals(0, $agent->id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $agent->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_URL_UPDATED, $agent->website, "Unexpected website value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $agent->branch_name, "Unexpected branch_name value");
		$this->assertEquals(ModelTestHelper::TEST_ADDRESS_UPDATED, $agent->branch_address, "Unexpected branch_address value");
		$this->assertEquals(ModelTestHelper::TEST_PHONE_UPDATED, $agent->branch_phone, "Unexpected branch_phone value");
		$this->assertEquals(ModelTestHelper::TEST_EMAIL_UPDATED, $agent->branch_email, "Unexpected branch_email value");
		$this->assertEquals(ModelTestHelper::TEST_ADDRESS_UPDATED, $agent->billing_address, "Unexpected billing_address value");
		$this->assertEquals(ModelTestHelper::TEST_PHONE_UPDATED, $agent->billing_phone, "Unexpected billing_phone value");
		$this->assertEquals(ModelTestHelper::TEST_EMAIL_UPDATED, $agent->billing_email, "Unexpected billing_email value");
		$this->assertEquals(ModelTestHelper::TEST_DECIMAL_UPDATED, $agent->commission, "Unexpected commission value");
		$this->assertEquals(ModelTestHelper::TEST_TERMS_UPDATED, $agent->terms, "Unexpected terms value");	
				
		//Delete
		$agent->delete();
		$agent = Agent::find($agent_id);
		
		$this->assertNull($agent, "Agent not deleted");
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
		$agent = Agent::find($agent_id);
	
		$this->assertNotNull($agent->company, "Unexpected company relationship value");
	}
	
	public function testFunctions(){
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$agent_id = ModelTestHelper::createAgent($company_id);
		$agent = Agent::find($agent_id);
		
		$this->assertFalse($agent->has_bookings, "Unexpected has_bookings value");
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
