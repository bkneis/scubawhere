<?php

/**
 * Helper class providing constants & functions to be used quickly create & test models.
 */
class ModelTestHelper{
	
	//Constants that can be used to quickly create & assert models
	
	const TEST_STRING = "Test string";	
	const TEST_USERNAME = "testuser";
	const TEST_PASSWORD = "testpassword";
	const TEST_EMAIL = "test@email.com";
	const TEST_DATE = "2000-01-01 12:34:56";
	
	//Create valid entries for each model & return its id
	//Each function also supplies a way to append data
	//to quickly create models with different values.	
	
	public static function createAccommodation($company_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createAddon($company_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createAgency($append = ""){
		//TODO
		return 0;
	}
	
	public static function createAgent($company_id, $append = ""){
		//TODO
		return 0;
	}
		
	public static function createBoat($company_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createBoooking($company_id, $agent_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createBoookingdetail($booking_id, $customer_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createCertificate($agency_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createCompany($country_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createContinent($append = ""){
		//TODO
		return 0;
	}
	
	public static function createCountry($continent_id, $currency_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createCurrency($continent_id, $currency_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createCustomer($country_id, $company_id, $certificate_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createDeparture($trip_id, $boat_id, $timetable_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createLocation($append = ""){
		//TODO
		return 0;
	}
	
	public static function createPackage($company_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createPackagefacade($package_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createPayment($booking_id, $paymentgateway_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createPayementgateway($append = ""){
		//TODO
		return 0;
	}
	
	public static function createTicket($company_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createTimetable($company_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createTrip($company_id, $location_id, $append = ""){
		//TODO
		return 0;
	}
	
	public static function createTriptype($append = ""){
		//TODO
		return 0;
	}
	
}