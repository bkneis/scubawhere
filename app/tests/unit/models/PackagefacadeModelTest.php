<?php

class PackagefacadeModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbClearTable('packagefacades');
		TestHelper::dbClearTable('companies');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$package_id = ModelTestHelper::createPackage($company_id);
		$packagefacade_id = ModelTestHelper::createPackagefacade($package_id);
		$packagefacade = Packagefacade::find($packagefacade_id);
		
		$this->assertNotEquals(0, $packagefacade->id, "Unexpected id value");		
		$this->assertNotEquals("0000-00-00 00:00:00", $packagefacade->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $packagefacade->updated_at);
				
		//Update
		//Nothing to update!
				
		//Delete
		$packagefacade->delete();
		$packagefacade = Packagefacade::find($packagefacade_id);		
		$this->assertNull($packagefacade, "Packagefacade not deleted");
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
