<?php

class BoatroomModelTest extends ModelTestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbClearTable('boatrooms');
		TestHelper::dbClearTable('companies');
	}

	public function testCRUD(){
		$this->refreshTables();

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
		$this->assertEquals(ModelTestHelper::TEST_STRING, $boatroom->photo, "Unexpected photo value");
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
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $boatroom->photo, "Unexpected photo value");

		//Delete
		$boatroom->delete();
		$boatroom = Boatroom::find($boatroom_id);

		$this->assertNull($boatroom, "Boatroom not deleted");
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
