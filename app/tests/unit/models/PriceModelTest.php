<?php

class PriceModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbClearTable('prices');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$owner_id = 1;		
		$price_id = ModelTestHelper::createPrice($owner_id);
		$price = Price::find($price_id);
		
		$this->assertNotEquals(0, $price->id, "Unexpected id value");
		$this->assertEquals($owner_id, $price->owner_id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING, $price->owner_type, "Unexpected owner_tyoe value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $price->price, "Unexpected price value");		
		$this->assertEquals(ModelTestHelper::TEST_DAY, $price->from, "Unexpected from value");
		$this->assertEquals(ModelTestHelper::TEST_DAY, $price->until, "Unexpected until value");
				
		$this->assertNotEquals("0000-00-00 00:00:00", $price->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $price->updated_at);
				
		//Update		
		$price->owner_type = ModelTestHelper::TEST_STRING_UPDATED;
		$price->new_decimal_price = ModelTestHelper::TEST_INTEGER_UPDATED;
		$price->price = ModelTestHelper::TEST_INTEGER_UPDATED;
		$price->from = ModelTestHelper::TEST_DAY_UPDATED;
		$price->until = ModelTestHelper::TEST_DAY_UPDATED;
		$price->save();
		$price = Price::find($price_id);
		
		$this->assertNotEquals(0, $price->id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $price->owner_type, "Unexpected owner_type value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $price->price, "Unexpected price value");		
		$this->assertEquals(ModelTestHelper::TEST_DAY_UPDATED, $price->from, "Unexpected from value");
		$this->assertEquals(ModelTestHelper::TEST_DAY_UPDATED, $price->until, "Unexpected until value");
				
		//Delete
		$price->delete();
		$price = Price::find($price_id);
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
		$this->assertTrue(true);
	}	
	
}
