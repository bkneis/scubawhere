<?php

class PackageModelTest extends ModelTestCase {

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
		$package_id = ModelTestHelper::createPackage($company_id);
		$package = Package::find($package_id);

		$this->assertNotEquals(0, $package->id, "Unexpected id value");
		$this->assertEquals($company_id, $package->company_id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $package->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $package->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $package->capacity, "Unexpected capacity value");
		$this->assertNotEquals("0000-00-00 00:00:00", $package->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $package->updated_at);

		//Update
		$package->name = ModelTestHelper::TEST_STRING_UPDATED;
		$package->description = ModelTestHelper::TEST_STRING_UPDATED;
		$package->capacity = ModelTestHelper::TEST_INTEGER_UPDATED;
		$package->save();
		$package = Package::find($package_id);

		$this->assertNotEquals(0, $package->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $package->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $package->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $package->capacity, "Unexpected capacity value");

		//Delete - soft, restore, force
		$package->delete();
		$package = Package::find($package_id);
		$this->assertNull($package, "Package not soft deleted");

		$package = Package::onlyTrashed()->where('id', '=', $package_id)->first();
		$this->assertNotNull($package, "Package soft deleted but cant be found");
		$this->assertNotNull($package->deleted_at);

		Package::onlyTrashed()->where('id', '=', $package_id)->restore();
		$package = Package::find($package_id);
		$this->assertNotNull($package, "Package not restored");
		$this->assertNull($package->deleted_at);

		Package::withTrashed()->where('id', '=', $package_id)->forceDelete();
		$package = Package::withTrashed()->where('id', '=', $package_id)->first();
		$this->assertNull($package, "Package not force deleted");
	}

	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}

	public function testRelationships(){
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$package_id = ModelTestHelper::createPackage($company_id);
		$package = Package::find($package_id);

		$this->assertNotNull($package->company, "Unexpected company relationship value");
	}

	public function testFunctions(){
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$package_id = ModelTestHelper::createPackage($company_id);
		$package = Package::find($package_id);

		$this->assertFalse($package->has_bookings, "Unexpected has_bookings value");
	}

	public function testEdges(){
		$this->assertTrue(true);
	}

}
