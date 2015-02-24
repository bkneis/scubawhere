<?php

class AccommodationModelTest extends ModelTestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function testCRUD(){
		//We must have an authenticated Company to grab its currency value
		$this->be(TestHelper::createAuthenticationCompany());

		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$accommodation_id = ModelTestHelper::createAccommodation($company_id);
		$accommodation = Accommodation::find($accommodation_id);

		$this->assertNotEquals(0, $accommodation->id, "Unexpected id value");
		$this->assertEquals($company_id, $accommodation->company_id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $accommodation->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $accommodation->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $accommodation->capacity, "Unexpected capacity value");
		$this->assertNotEquals("0000-00-00 00:00:00", $accommodation->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $accommodation->updated_at);

		//Update
		$accommodation->name = ModelTestHelper::TEST_STRING_UPDATED;
		$accommodation->description = ModelTestHelper::TEST_STRING_UPDATED;
		$accommodation->capacity = ModelTestHelper::TEST_INTEGER_UPDATED;
		$accommodation->save();
		$accommodation = Accommodation::find($accommodation_id);

		$this->assertNotEquals(0, $accommodation->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $accommodation->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $accommodation->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $accommodation->capacity, "Unexpected capacity value");

		//Delete - soft, restore, force
		$accommodation->delete();
		$accommodation = Accommodation::find($accommodation_id);
		$this->assertNull($accommodation, "Accommodation not soft deleted");

		$accommodation = Accommodation::onlyTrashed()->where('id', '=', $accommodation_id)->first();
		$this->assertNotNull($accommodation, "Accommodation soft deleted but cant be found");
		$this->assertNotNull($accommodation->deleted_at);

		Accommodation::onlyTrashed()->where('id', '=', $accommodation_id)->restore();
		$accommodation = Accommodation::find($accommodation_id);
		$this->assertNotNull($accommodation, "Accommodation not restored");
		$this->assertNull($accommodation->deleted_at);

		Accommodation::withTrashed()->where('id', '=', $accommodation_id)->forceDelete();
		$accommodation = Accommodation::withTrashed()->where('id', '=', $accommodation_id)->first();
		$this->assertNull($accommodation, "Accommodation not force deleted");
	}

	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}

	public function testRelationships(){
		//We must have an authenticated Company to grab its currency value
		$this->be(TestHelper::createAuthenticationCompany());

		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$accommodation_id = ModelTestHelper::createAccommodation($company_id);
		$accommodation = Accommodation::find($accommodation_id);

		$this->assertNotNull($accommodation->company, "Unexpected company relationship value");
	}

	public function testFunctions(){
		//We must have an authenticated Company to grab its currency value
		$this->be(TestHelper::createAuthenticationCompany());

		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$accommodation_id = ModelTestHelper::createAccommodation($company_id);
		$accommodation = Accommodation::find($accommodation_id);
	}

	public function testEdges(){
		$this->assertTrue(true);
	}

}
