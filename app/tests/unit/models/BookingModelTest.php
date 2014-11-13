<?php

class BookingModelTest extends ModelTestCase {
		
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
		$booking = Booking::find($booking_id);
		
		$this->assertNotEquals(0, $booking->id, "Unexpected id value");
		$this->assertEquals($company_id, $booking->company_id, "Unexpected id value");
		$this->assertEquals($agent_id, $booking->agent_id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_REFERENCE, $booking->reference, "Unexpected reference value");
		$this->assertEquals(ModelTestHelper::TEST_SOURCE, $booking->source, "Unexpected source value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $booking->price, "Unexpected price value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER, $booking->discount, "Unexpected discount value");
		$this->assertEquals(ModelTestHelper::TEST_BOOL, $booking->confirmed, "Unexpected confirmed value");
		$this->assertEquals(ModelTestHelper::TEST_DATE, $booking->reserved, "Unexpected reserved value");
		$this->assertEquals(ModelTestHelper::TEST_ADDRESS, $booking->pick_up_location, "Unexpected pick_up_location value");
		$this->assertEquals(ModelTestHelper::TEST_DATE, $booking->pick_up_time, "Unexpected pick_up_time value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $booking->comment, "Unexpected comment value");
		$this->assertNotEquals("0000-00-00 00:00:00", $booking->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $booking->updated_at);
				
		//Update		
		$booking->reference = ModelTestHelper::TEST_REFERENCE_UPDATED;
		$booking->source = ModelTestHelper::TEST_SOURCE_UPDATED;
		$booking->price = ModelTestHelper::TEST_INTEGER_UPDATED;
		$booking->discount = ModelTestHelper::TEST_INTEGER_UPDATED;
		$booking->confirmed = ModelTestHelper::TEST_BOOL_UPDATED;
		$booking->reserved = ModelTestHelper::TEST_DATE_UPDATED;
		$booking->pick_up_location = ModelTestHelper::TEST_ADDRESS_UPDATED;
		$booking->pick_up_time = ModelTestHelper::TEST_DATE_UPDATED;
		$booking->comment = ModelTestHelper::TEST_STRING_UPDATED;
		
		$this->assertNotEquals(0, $booking->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_REFERENCE_UPDATED, $booking->reference, "Unexpected reference value");
		$this->assertEquals(ModelTestHelper::TEST_SOURCE_UPDATED, $booking->source, "Unexpected source value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $booking->price, "Unexpected price value");
		$this->assertEquals(ModelTestHelper::TEST_INTEGER_UPDATED, $booking->discount, "Unexpected discount value");
		$this->assertEquals(ModelTestHelper::TEST_BOOL_UPDATED, $booking->confirmed, "Unexpected confirmed value");
		$this->assertEquals(ModelTestHelper::TEST_DATE_UPDATED, $booking->reserved, "Unexpected reserved value");
		$this->assertEquals(ModelTestHelper::TEST_ADDRESS_UPDATED, $booking->pick_up_location, "Unexpected pick_up_location value");
		$this->assertEquals(ModelTestHelper::TEST_DATE_UPDATED, $booking->pick_up_time, "Unexpected pick_up_time value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $booking->comment, "Unexpected comment value");
				
		//Delete
		$booking->delete();
		$booking = Booking::find($booking_id);
		
		$this->assertNull($booking, "Booking not deleted");
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
		$booking = Booking::find($booking_id);
	
		$this->assertNotNull($booking->company, "Unexpected company relationship value");
		$this->assertNotNull($booking->agent, "Unexpected agent relationship value");
	}
	
	public function testFunctions(){
		//We must have an authenticated Company to grab its currency value
		$this->be(TestHelper::createAuthenticationCompany());
		
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$agent_id = ModelTestHelper::createAgent($company_id);
		$booking_id = ModelTestHelper::createBooking($company_id, $agent_id);
		$booking = Booking::find($booking_id);
		
		$this->assertEquals("0.00", $booking->decimal_price, "Unexpected decimal_price value");//0.00 due to discount
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
