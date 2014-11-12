<?php

class CompanyModelTest extends ModelTestCase {
		
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
		$company = Company::find($company_id);
		
		$this->assertNotEquals(0, $company->id, "Unexpected id value");
		$this->assertEquals($country_id, $company->country_id, "Unexpected id value");
		$this->assertEquals($currency_id, $company->currency_id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_USERNAME, $company->username, "Unexpected username value");
		$this->assertTrue(Hash::check(ModelTestHelper::TEST_PASSWORD, $company->password), "Unexpected password value");
		$this->assertEquals(ModelTestHelper::TEST_EMAIL, $company->email, "Unexpected email value");
		$this->assertEquals(ModelTestHelper::TEST_BOOL, $company->verified, "Unexpected verified value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->description, "Unexpected description value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->address_1, "Unexpected address_1 value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->address_1, "Unexpected address_2 value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->city, "Unexpected city value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->county, "Unexpected county value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->postcode, "Unexpected postcode value");		
		$this->assertEquals(ModelTestHelper::TEST_EMAIL, $company->business_email, "Unexpected business_email value");
		$this->assertEquals(ModelTestHelper::TEST_PHONE, $company->business_phone, "Unexpected business_phone value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->vat_number, "Unexpected vat_number value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->registration_number, "Unexpected registration_number value");		
		$this->assertEqualsDecimal(ModelTestHelper::TEST_DECIMAL, $company->latitude, "Unexpected latitude value", 0.0001);//Delta required for decimals
		$this->assertEqualsDecimal(ModelTestHelper::TEST_DECIMAL, $company->longitude, "Unexpected longitude value", 0.0001);//Delta required for decimals
		$this->assertEquals(ModelTestHelper::TEST_PHONE, $company->phone, "Unexpected phone value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->contact, "Unexpected contact value");
		$this->assertEquals(ModelTestHelper::TEST_URL, $company->website, "Unexpected website value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->logo, "Unexpected logo value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->photo, "Unexpected photo value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $company->video, "Unexpected video value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $company->views, "Unexpected views value");
		$this->assertNotEquals("0000-00-00 00:00:00", $company->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $company->updated_at);
				
		//Update		
		$company->username = ModelTestHelper::TEST_USERNAME_UPDATED;
		$company->password = Hash::make(ModelTestHelper::TEST_PASSWORD_UPDATED);
		$company->email = ModelTestHelper::TEST_EMAIL_UPDATED;
		$company->verified = ModelTestHelper::TEST_BOOL_UPDATED;
		$company->name = ModelTestHelper::TEST_STRING_UPDATED;
		$company->description = ModelTestHelper::TEST_STRING_UPDATED;
		$company->address_1 = ModelTestHelper::TEST_STRING_UPDATED;
		$company->address_2 = ModelTestHelper::TEST_STRING_UPDATED;
		$company->city = ModelTestHelper::TEST_STRING_UPDATED;
		$company->county = ModelTestHelper::TEST_STRING_UPDATED;
		$company->postcode = ModelTestHelper::TEST_STRING_UPDATED;		
		$company->business_email = ModelTestHelper::TEST_EMAIL_UPDATED;
		$company->business_phone = ModelTestHelper::TEST_PHONE_UPDATED;
		$company->vat_number = ModelTestHelper::TEST_STRING_UPDATED;
		$company->registration_number = ModelTestHelper::TEST_STRING_UPDATED;
		$company->latitude = ModelTestHelper::TEST_DECIMAL_UPDATED;
		$company->longitude = ModelTestHelper::TEST_DECIMAL_UPDATED;
		$company->phone = ModelTestHelper::TEST_PHONE_UPDATED;
		$company->contact = ModelTestHelper::TEST_STRING_UPDATED;
		$company->website = ModelTestHelper::TEST_URL_UPDATED;
		$company->logo = ModelTestHelper::TEST_STRING_UPDATED;
		$company->photo = ModelTestHelper::TEST_STRING_UPDATED;
		$company->video = ModelTestHelper::TEST_STRING_UPDATED;
		$company->views = ModelTestHelper::TEST_INTEGER_UPDATED;
		$company->save();		
		$company = Company::find($company_id);
		
		$this->assertNotEquals(0, $company->id, "Unexpected id value");				
		$this->assertEquals(ModelTestHelper::TEST_USERNAME_UPDATED, $company->username, "Unexpected username value");
		$this->assertTrue(Hash::check(ModelTestHelper::TEST_PASSWORD_UPDATED, $company->password), "Unexpected password value");
		$this->assertEquals(ModelTestHelper::TEST_EMAIL_UPDATED, $company->email, "Unexpected email value");
		$this->assertEquals(ModelTestHelper::TEST_BOOL_UPDATED, $company->verified, "Unexpected verified value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->description, "Unexpected description value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->address_1, "Unexpected address_1 value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->address_1, "Unexpected address_2 value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->city, "Unexpected city value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->county, "Unexpected county value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->postcode, "Unexpected postcode value");		
		$this->assertEquals(ModelTestHelper::TEST_EMAIL_UPDATED, $company->business_email, "Unexpected business_email value");
		$this->assertEquals(ModelTestHelper::TEST_PHONE_UPDATED, $company->business_phone, "Unexpected business_phone value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->vat_number, "Unexpected vat_number value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->registration_number, "Unexpected registration_number value");		
		$this->assertEqualsDecimal(ModelTestHelper::TEST_DECIMAL_UPDATED, $company->latitude, "Unexpected latitude value", 0.0001);//Delta required for decimals
		$this->assertEqualsDecimal(ModelTestHelper::TEST_DECIMAL_UPDATED, $company->longitude, "Unexpected longitude value", 0.0001);//Delta required for decimals
		$this->assertEquals(ModelTestHelper::TEST_PHONE_UPDATED, $company->phone, "Unexpected phone value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->contact, "Unexpected contact value");
		$this->assertEquals(ModelTestHelper::TEST_URL_UPDATED, $company->website, "Unexpected website value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->logo, "Unexpected logo value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->photo, "Unexpected photo value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $company->video, "Unexpected video value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $company->views, "Unexpected views value");
				
		//Delete
		$company->delete();
		$company = Company::find($company_id);		
		$this->assertNull($company, "Company not deleted");
	}
	
	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testRelationships(){	
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$company = Company::find($company_id);
	
		$this->assertNotNull($company->country, "Unexpected country relationship value");
		$this->assertNotNull($company->currency, "Unexpected currency relationship value");
	}
	
	public function testFunctions(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
