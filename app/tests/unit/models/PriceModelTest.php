<?php

class PriceModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function testCRUD(){
		
		//We must have an authenticated Company to grab its currency value
		$this->be(TestHelper::createAuthenticationCompany());
		
		//Create/Read
		$owner_id = 1;		
		$price_id = ModelTestHelper::createPrice($owner_id);
		$price = Price::find($price_id);
		
		$this->assertNotEquals(0, $price->id, "Unexpected id value");
		$this->assertEquals($owner_id, $price->owner_id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING, $price->owner_type, "Unexpected owner_tyoe value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER * 100, $price->price, "Unexpected price value");		
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
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED * 100, $price->price, "Unexpected price value");		
		$this->assertEquals(ModelTestHelper::TEST_DAY_UPDATED, $price->from, "Unexpected from value");
		$this->assertEquals(ModelTestHelper::TEST_DAY_UPDATED, $price->until, "Unexpected until value");
				
		//Delete
		$price->delete();
		$price = Price::find($price_id);
		$this->assertNull($price, "Price not deleted");
	}
	
	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testRelationships(){
		$this->assertTrue(true);
	}
	
	public function testFunctions(){		
		//We must have an authenticated Company to grab its currency value
		$this->be(TestHelper::createAuthenticationCompany());
		
		$owner_id = 1;
		$price_id = ModelTestHelper::createPrice($owner_id);
		$price = Price::find($price_id);
		
		$this->assertEquals("10.00", $price->decimal_price, "Unexpected decimal_price value");
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
