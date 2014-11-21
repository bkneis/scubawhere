<?php

class ModelRelationshipsTest extends TestCase {

	private static $relationshipsCreated = false;
	
	public function setUp()
	{		
		parent::setUp();
		if (!self::$relationshipsCreated){
			self::createRelationships();
			self::$relationshipsCreated = true;
		}		
	}

	public function createRelationships(){
		//We must have an authenticated Company to grab its currency value
		$this->be(TestHelper::createAuthenticationCompany());
		
		//Create one of everything, making sure they are all associated!
 		$this->price_id = ModelTestHelper::createPrice(1);
				
		$this->agency_id = ModelTestHelper::createAgency();
		$this->continent_id = ModelTestHelper::createContinent();
		$this->currency_id = ModelTestHelper::createCurrency();
		$this->location_id = ModelTestHelper::createLocation();
		$this->paymentgateway_id = ModelTestHelper::createPaymentgateway();
		$this->triptype_id = ModelTestHelper::createTriptype();
		
		$this->certificate_id = ModelTestHelper::createCertificate($this->agency_id);
				
		$this->country_id = ModelTestHelper::createCountry($this->continent_id, $this->currency_id);
		$this->company_id = ModelTestHelper::createCompany($this->country_id, $this->currency_id);	
		
		$this->accommodation_id = ModelTestHelper::createAccommodation($this->company_id);
		$this->addon_id = ModelTestHelper::createAddon($this->company_id);
		$this->agent_id = ModelTestHelper::createAgent($this->company_id);
		
		$this->ticket_id = ModelTestHelper::createTicket($this->company_id);
		
		$this->package_id = ModelTestHelper::createPackage($this->company_id);
		$this->packagefacade_id = ModelTestHelper::createPackagefacade($this->package_id);
		
		$this->customer_id = ModelTestHelper::createCustomer($this->country_id, $this->company_id);
		
		$this->trip_id = ModelTestHelper::createTrip($this->company_id, $this->location_id);
		$this->boat_id = ModelTestHelper::createBoat($this->company_id);
		$this->boatroom_id = ModelTestHelper::createBoatroom($this->company_id);
		$this->timetable_id = ModelTestHelper::createTimetable($this->company_id);
		$this->session_id = ModelTestHelper::createDeparture($this->trip_id, $this->boat_id, $this->timetable_id);
		
		$this->booking_id = ModelTestHelper::createBooking($this->company_id, $this->agent_id);
		$this->bookingdetail_id = ModelTestHelper::createBookingdetail($this->booking_id, $this->customer_id, $this->ticket_id, $this->session_id, $this->packagefacade_id);
		
		$this->payment_id = ModelTestHelper::createPayment($this->booking_id, $this->currency_id, $this->paymentgateway_id);			
	}
	
	
	
	public function testAccommodationRelationships(){
		
	}
	
	public function testAddonRelationships(){
		
	}
	
	
	
		

}
