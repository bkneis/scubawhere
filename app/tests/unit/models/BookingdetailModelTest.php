<?php

class BookingdetailModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function refreshTables(){
		//Refresh any tables required for testing this model
		TestHelper::dbClearTable('booking_details');
		TestHelper::dbClearTable('continents');
		TestHelper::dbClearTable('currencies');
		TestHelper::dbClearTable('countries');
		TestHelper::dbClearTable('companies');
		TestHelper::dbClearTable('agents');
		TestHelper::dbClearTable('locations');
		TestHelper::dbClearTable('trips');
		TestHelper::dbClearTable('boats');
		TestHelper::dbClearTable('timetables');
		TestHelper::dbClearTable('packages');
		TestHelper::dbClearTable('bookings');
		TestHelper::dbClearTable('customers');
		TestHelper::dbClearTable('tickets');
		TestHelper::dbClearTable('continents');
		TestHelper::dbClearTable('sessions');
		TestHelper::dbClearTable('packagefacades');
	}
	
	public function testCRUD(){
		$this->refreshTables();
		
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$agent_id = ModelTestHelper::createAgent($company_id);
		$location_id = ModelTestHelper::createLocation();
		$trip_id = ModelTestHelper::createTrip($company_id, $location_id);
		$boat_id = ModelTestHelper::createBoat($company_id);
		$timetable_id = ModelTestHelper::createTimetable($company_id);
		$package_id = ModelTestHelper::createPackage($company_id);
		
		$booking_id = ModelTestHelper::createBooking($company_id, $agent_id);
		$customer_id = ModelTestHelper::createCustomer($country_id, $company_id);
		$ticket_id = ModelTestHelper::createTicket($company_id);
		$session_id = ModelTestHelper::createDeparture($trip_id, $boat_id, $timetable_id);
		$packagefacade_id = ModelTestHelper::createPackagefacade($package_id);		
		
		$bookingdetail_id = ModelTestHelper::createBoookingdetail($booking_id, $customer_id, $ticket_id, $session_id, $packagefacade_id);
		$bookingdetail = Bookingdetail::find($bookingdetail_id);
		
		$this->assertNotEquals(0, $bookingdetail->id, "Unexpected id value");
		$this->assertEquals($company_id, $bookingdetail->company_id, "Unexpected id value");
		
		$this->assertEquals(ModelTestHelper::TEST_BOOL, $bookingdetail->is_lead, "Unexpected is_lead value");
		$this->assertNotEquals("0000-00-00 00:00:00", $bookingdetail->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $bookingdetail->updated_at);
				
		//Update		
		$bookingdetail->is_lead = ModelTestHelper::TEST_BOOL_UPDATED;
		$bookingdetail->save();		
		$bookingdetail = Bookingdetail::find($bookingdetail_id);
		
		$this->assertNotEquals(0, $bookingdetail->id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_BOOL_UPDATED, $bookingdetail->is_lead, "Unexpected is_lead value");
				
		//Delete
		$bookingdetail->delete();
		$bookingdetail = Bookingdetail::find($bookingdetail_id);		
		$this->assertNull($bookingdetail, "Bookingdetail not deleted");
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
