<?php

class BoatroomModelTest extends ModelTestCase {

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
		$boatroom_id = ModelTestHelper::createBoatroom($company_id);
		$boatroom = Boatroom::find($boatroom_id);

		$this->assertNotEquals(0, $boatroom->id, "Unexpected id value");
		$this->assertEquals($company_id, $boatroom->company_id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $boatroom->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $boatroom->description, "Unexpected description value");
		$this->assertNotEquals("0000-00-00 00:00:00", $boatroom->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $boatroom->updated_at);

		//Update
		$boatroom->name = ModelTestHelper::TEST_STRING_UPDATED;
		$boatroom->description = ModelTestHelper::TEST_STRING_UPDATED;
		$boatroom->photo = ModelTestHelper::TEST_STRING_UPDATED;
		$boatroom->save();
		$boatroom = Boatroom::find($boatroom_id);

		$this->assertNotEquals(0, $boatroom->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $boatroom->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $boatroom->description, "Unexpected description value");

		//Delete - soft, restore, force
		$boatroom->delete();
		$boatroom = Boatroom::find($boatroom_id);
		$this->assertNull($boatroom, "Boatroom not soft deleted");

		$boatroom = Boatroom::onlyTrashed()->where('id', '=', $boatroom_id)->first();
		$this->assertNotNull($boatroom, "Boatroom soft deleted but cannot be found");
		$this->assertNotNull($boatroom->deleted_at);

		Boatroom::onlyTrashed()->where('id', '=', $boatroom_id)->restore();
		$boatroom = Boatroom::find($boatroom_id);
		$this->assertNotNull($boatroom, "Boatroom not restored");
		$this->assertNull($boatroom->deleted_at);

		Boatroom::withTrashed()->where('id', '=', $boatroom_id)->forceDelete();
		$boatroom = Boatroom::withTrashed()->where('id', '=', $boatroom_id)->first();
		$this->assertNull($boatroom, "Boatroom not forceDeleted");
	}

	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}

	public function testRelationships(){
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$boatroom_id = ModelTestHelper::createBoatroom($company_id);
		$boatroom = Boatroom::find($boatroom_id);

		$this->assertNotNull($boatroom->company, "Unexpected company relationship value");
	}

	public function testFunctions(){
		$this->assertTrue(true);
	}

	public function testEdges(){
		$this->assertTrue(true);
	}

}
