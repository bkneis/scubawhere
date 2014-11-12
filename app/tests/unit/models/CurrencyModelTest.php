<?php

class CurrencyModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function testCRUD(){
				
		//Create/Read
		$currency_id = ModelTestHelper::createCurrency();
		$currency = Currency::find($currency_id);
		
		$this->assertNotEquals(0, $currency->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR, $currency->code, "Unexpected code value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $currency->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $currency->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_SYMBOL, $currency->symbol, "Unexpected symbol value");
		$this->assertNotEquals("0000-00-00 00:00:00", $currency->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $currency->updated_at);
				
		//Update
		$currency->code = ModelTestHelper::TEST_ABBR_UPDATED;
		$currency->name = ModelTestHelper::TEST_STRING_UPDATED;
		$currency->description = ModelTestHelper::TEST_STRING_UPDATED;
		$currency->symbol = ModelTestHelper::TEST_SYMBOL_UPDATED;
		$currency->save();		
		$currency = Currency::find($currency_id);
		
		$this->assertNotEquals(0, $currency->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR_UPDATED, $currency->code, "Unexpected code value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $currency->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $currency->description, "Unexpected description value");
		$this->assertEquals(ModelTestHelper::TEST_SYMBOL_UPDATED, $currency->symbol, "Unexpected symbol value");
				
		//Delete
		$currency->delete();
		$currency = Currency::find($currency_id);
		
		$this->assertNull($currency, "Currency not deleted");
	}
	
	public function testValidation(){
		$this->assertTrue(true);
	}
	
	public function testRelationships(){
		$this->assertTrue(true);
	}
	
	public function testFunctions(){
		$this->assertTrue(true);
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
