<?php

class PaymentModelTest extends ModelTestCase {
		
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
		$agent_id = ModelTestHelper::createAgent($company_id);
		$booking_id = ModelTestHelper::createBooking($company_id, $agent_id);
		$paymentgateway_id = ModelTestHelper::createPaymentgateway();		
		$payment_id = ModelTestHelper::createPayment($booking_id, $currency_id, $paymentgateway_id);
		$payment = Payment::find($payment_id);
		
		$this->assertNotEquals(0, $payment->id, "Unexpected id value");		
		$this->assertEquals($booking_id, $payment->booking_id, "Unexpected id value");
		$this->assertEquals($currency_id, $payment->currency_id, "Unexpected id value");
		$this->assertEquals($paymentgateway_id, $payment->paymentgateway_id, "Unexpected id value");			
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $payment->amount, "Unexpected amount value");		
		$this->assertNotEquals("0000-00-00 00:00:00", $payment->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $payment->updated_at);
				
		//Update		
		$payment->amount = ModelTestHelper::TEST_INTEGER_UPDATED;
		$payment->save();		
		$payment = Payment::find($payment_id);
		
		$this->assertNotEquals(0, $payment->id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $payment->amount, "Unexpected amount value");
				
		//Delete
		$payment->delete();
		$payment = Payment::find($payment_id);		
		$this->assertNull($payment, "Payment not deleted");
	}
	
	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testRelationships(){	
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$agent_id = ModelTestHelper::createAgent($company_id);
		$booking_id = ModelTestHelper::createBooking($company_id, $agent_id);
		$paymentgateway_id = ModelTestHelper::createPaymentgateway();		
		$payment_id = ModelTestHelper::createPayment($booking_id, $currency_id, $paymentgateway_id);
		$payment = Payment::find($payment_id);
	
		$this->assertNotNull($payment->booking, "Unexpected booking relationship value");
		$this->assertNotNull($payment->currency, "Unexpected currency relationship value");
		$this->assertNotNull($payment->paymentgateway, "Unexpected paymentgateway relationship value");
	}
	
	public function testFunctions(){
		$this->assertTrue(true);
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
