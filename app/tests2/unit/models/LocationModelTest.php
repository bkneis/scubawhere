<?php

class LocationModelTest extends ModelTestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function testCRUD(){

		//Create/Read
		$location_id = ModelTestHelper::createLocation();
		$location = Location::find($location_id);

		$this->assertNotEquals(0, $location->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $location->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $location->description, "Unexpected description value");
		$this->assertEqualsDecimal(ModelTestHelper::TEST_LATITUDE, $location->latitude, "Unexpected latitude value", 0.000001);//Delta required for decimals
		$this->assertEqualsDecimal(ModelTestHelper::TEST_LONGITUDE, $location->longitude, "Unexpected longitude value", 0.000001);//Delta required for decimals
		$this->assertNotEquals("0000-00-00 00:00:00", $location->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $location->updated_at);

		//Update
		$location->name        = ModelTestHelper::TEST_STRING_UPDATED;
		$location->description = ModelTestHelper::TEST_STRING_UPDATED;
		$location->latitude    = ModelTestHelper::TEST_LATITUDE_UPDATED;
		$location->longitude   = ModelTestHelper::TEST_LONGITUDE_UPDATED;
		$location->save();
		$location = Location::find($location_id);

		$this->assertNotEquals(0, $location->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $location->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $location->description, "Unexpected description value");
		$this->assertEqualsDecimal(ModelTestHelper::TEST_LATITUDE_UPDATED, $location->latitude, "Unexpected latitude value", 0.000001);//Delta required for decimals
		$this->assertEqualsDecimal(ModelTestHelper::TEST_LONGITUDE_UPDATED, $location->longitude, "Unexpected longitude value", 0.000001);//Delta required for decimals

		//Delete
		$location->delete();
		$location = Location::find($location_id);
		$this->assertNull($location, "Location not deleted");
	}

	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}

	public function testRelationships(){
		$this->markTestIncomplete('This test needs to be completed!');
	}

	public function testFunctions(){
		$this->markTestIncomplete('This test needs to be completed!');
	}

	public function testEdges(){
		$this->markTestIncomplete('This test needs to be completed!');
	}

}
