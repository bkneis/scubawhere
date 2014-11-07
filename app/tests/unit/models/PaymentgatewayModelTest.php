<?php

class PaymentgatewayModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbSeedTable('paymentgateways');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$paymentgateway_id = ModelTestHelper::createPaymentgateway();
		$paymentgateway = Paymentgateway::find($paymentgateway_id);
		
		$this->assertNotEquals(0, $paymentgateway->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $paymentgateway->name, "Unexpected name value");
		$this->assertNotEquals("0000-00-00 00:00:00", $paymentgateway->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $paymentgateway->updated_at);
				
		//Update
		$paymentgateway->name = ModelTestHelper::TEST_STRING_UPDATED;
		$paymentgateway->save();		
		$paymentgateway = Paymentgateway::find($paymentgateway_id);
		
		$this->assertNotEquals(0, $paymentgateway->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $paymentgateway->name, "Unexpected name value");
				
		//Delete
		$paymentgateway->delete();
		$paymentgateway = Paymentgateway::find($paymentgateway_id);
		
		$this->assertNull($paymentgateway, "Paymentgateway not deleted");
	}
	
	public function testValidation(){
		$this->assertTrue(true);
	}
	
	public function testFunctions(){
		$this->assertTrue(true);
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
