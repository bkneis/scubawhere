<?php

class BookingdetailModelTest extends ModelTestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function testCRUD(){

		//Create/Read
		$continent_id     = ModelTestHelper::createContinent();
		$currency_id      = ModelTestHelper::createCurrency();
		$country_id       = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id       = ModelTestHelper::createCompany($country_id, $currency_id);
		//We must have an authenticated Company
		$company          = Company::find($company_id);
		$this->be($company);
		$agent_id         = ModelTestHelper::createAgent($company_id);
		$location_id      = ModelTestHelper::createLocation();
		$trip_id          = ModelTestHelper::createTrip($company_id, $location_id);
		$boat_id          = ModelTestHelper::createBoat($company_id);
		$timetable_id     = ModelTestHelper::createTimetable($company_id);
		$package_id       = ModelTestHelper::createPackage($company_id);

		$booking_id       = ModelTestHelper::createBooking($company_id, $agent_id);
		$customer_id      = ModelTestHelper::createCustomer($country_id, $company_id);
		$ticket_id        = ModelTestHelper::createTicket($company_id);
		$session_id       = ModelTestHelper::createDeparture($trip_id, $boat_id, $timetable_id);
		$boatroom_id      = ModelTestHelper::createBoatroom($company_id);
		$packagefacade_id = ModelTestHelper::createPackagefacade($package_id);
		$bookingdetail_id = ModelTestHelper::createBookingdetail($booking_id, $customer_id, $ticket_id, $session_id, $boatroom_id, $packagefacade_id);
		$bookingdetail    = Bookingdetail::find($bookingdetail_id);

		$this->assertNotEquals(0, $bookingdetail->id, "Unexpected id value");
		$this->assertEquals($booking_id, $bookingdetail->booking_id, "Unexpected booking_id value");
		$this->assertEquals($customer_id, $bookingdetail->customer_id, "Unexpected customer_id value");
		$this->assertEquals($ticket_id, $bookingdetail->ticket_id, "Unexpected ticket_id value");
		$this->assertEquals($session_id, $bookingdetail->session_id, "Unexpected session_id value");
		$this->assertEquals($boatroom_id, $bookingdetail->boatroom_id, "Unexpected boatroom_id value");
		$this->assertEquals($packagefacade_id, $bookingdetail->packagefacade_id, "Unexpected packagefacade_id value");
		$this->assertNotEquals("0000-00-00 00:00:00", $bookingdetail->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $bookingdetail->updated_at);

		//Update
		$this->markTestIncomplete('This test needs to be completed!');
		// ...
		$bookingdetail->save();
		$bookingdetail = Bookingdetail::find($bookingdetail_id);

		$this->assertNotEquals(0, $bookingdetail->id, "Unexpected id value");

		//Delete
		$bookingdetail->delete();
		$bookingdetail = Bookingdetail::find($bookingdetail_id);
		$this->assertNull($bookingdetail, "Bookingdetail not deleted");
	}

	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}

	public function testRelationships(){
		$continent_id     = ModelTestHelper::createContinent();
		$currency_id      = ModelTestHelper::createCurrency();
		$country_id       = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id       = ModelTestHelper::createCompany($country_id, $currency_id);
		//We must have an authenticated Company
		$company          = Company::find($company_id);
		$this->be($company);
		$agent_id         = ModelTestHelper::createAgent($company_id);
		$location_id      = ModelTestHelper::createLocation();
		$trip_id          = ModelTestHelper::createTrip($company_id, $location_id);
		$boat_id          = ModelTestHelper::createBoat($company_id);
		$timetable_id     = ModelTestHelper::createTimetable($company_id);
		$package_id       = ModelTestHelper::createPackage($company_id);

		$booking_id       = ModelTestHelper::createBooking($company_id, $agent_id);
		$customer_id      = ModelTestHelper::createCustomer($country_id, $company_id);
		$ticket_id        = ModelTestHelper::createTicket($company_id);
		$session_id       = ModelTestHelper::createDeparture($trip_id, $boat_id, $timetable_id);
		$boatroom_id      = ModelTestHelper::createBoatroom($company_id);
		$packagefacade_id = ModelTestHelper::createPackagefacade($package_id);
		$bookingdetail_id = ModelTestHelper::createBookingdetail($booking_id, $customer_id, $ticket_id, $session_id, $boatroom_id, $packagefacade_id);
		$bookingdetail    = Bookingdetail::find($bookingdetail_id);

		$this->assertNotNull($bookingdetail->booking, "Unexpected booking relationship value");
		$this->assertNotNull($bookingdetail->customer, "Unexpected customer relationship value");
		$this->assertNotNull($bookingdetail->ticket, "Unexpected ticket relationship value");
		$this->assertNotNull($bookingdetail->session, "Unexpected session relationship value");
		$this->assertNotNull($bookingdetail->boatroom, "Unexpected boatroom relationship value");
		$this->assertNotNull($bookingdetail->packagefacade, "Unexpected packagefacade relationship value");
	}

	public function testFunctions(){
		$this->assertTrue(true);
	}

	public function testEdges(){
		$this->assertTrue(true);
	}

}
