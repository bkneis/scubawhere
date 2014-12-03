<?php

/**
 * Helper class providing constants & functions to be used quickly create & test models.
 */
class ModelTestHelper{

	//Constants that can be used to quickly create & assert models

	const TEST_ABBR                 = "TST";
	const TEST_ABBR_UPDATED         = "NTS";
	const TEST_ADDRESS              = "123 Test Lane, Aplace, Somewhere, ABC 123";
	const TEST_ADDRESS_UPDATED      = "456 Test Lane, Aplace, Somewhere, ABC 456";
	const TEST_BOOL                 = 0;
	const TEST_BOOL_UPDATED         = 1;
	const TEST_CURRENCY             = "GBP";
	const TEST_CURRENCY_UPDATED     = "USD";
	const TEST_DATE                 = "2020-02-02";
	const TEST_DATE_UPDATED         = "2021-02-02";
	const TEST_DATETIME             = "2020-01-01 12:34:56";
	const TEST_DATETIME_UPDATED     = "2021-01-01 12:34:56";
	const TEST_DAY                  = "1982-02-03";
	const TEST_DAY_UPDATED          = "1989-12-29";
	const TEST_DECIMAL              = 1.1;
	const TEST_DECIMAL_UPDATED      = 2.2;
	const TEST_EMAIL                = "test@email.com";
	const TEST_EMAIL_UPDATED        = "newtest@email.com";
	const TEST_GENDER               = 1;
	const TEST_GENDER_UPDATED       = 2;
	const TEST_INTEGER              = 100;
	const TEST_INTEGER_UPDATED      = 200;
	const TEST_JSON                 = '[{"this":"is", "some":"json"}]';
	const TEST_JSON_UPDATED         = '[{"this":"is", "updated":"json"}]';
	const TEST_LATITUDE             = 51.482536;
	const TEST_LATITUDE_UPDATED     = 52.274750;
	const TEST_LONGITUDE            = -2.528342;
	const TEST_LONGITUDE_UPDATED    =  7.919868;
	const TEST_TIMEZONE             = 'Europe/London';
	const TEST_TIMEZONE_UPDATED     = 'Europe/Berlin';
	const TEST_PASSWORD             = "testpassword";
	const TEST_PASSWORD_UPDATED     = "newtestpassword";
	const TEST_PHONE                = "07123 456 789";
	const TEST_PHONE_UPDATED        = "07456 789 123";
	const TEST_PRICE                = self::TEST_INTEGER * 100;
	const TEST_PRICE_UPDATED        = self::TEST_INTEGER_UPDATED * 100;
	const TEST_REFERENCE            = "ABCD1234";
	const TEST_REFERENCE_UPDATED    = "EFGH5678";
	const TEST_SOURCE               = "telephone";
	const TEST_SOURCE_UPDATED       = "email";
	const TEST_STRING               = "Test string";
	const TEST_STRING_UPDATED       = "New test string";
	const TEST_SYMBOL               = "£";
	const TEST_SYMBOL_UPDATED       = "$";
	const TEST_TERMS                = "fullamount";
	const TEST_TERMS_UPDATED        = "banned";
	const TEST_TIME                 = "10:10:10";
	const TEST_TIME_UPDATED         = "20:20:20";
	const TEST_TINY_INTEGER         = 2;
	const TEST_TINY_INTEGER_UPDATED = 4;
	const TEST_URL                  = "http://www.scubawhere.com";
	const TEST_URL_UPDATED          = "https://www.facebook.com";
	const TEST_USERNAME             = "testuser";
	const TEST_USERNAME_UPDATED     = "newtestuser";

	//Create valid entries for each model & return its id
	//Each function also supplies a way to append data
	//to quickly create models with different values.

	public static function createAccommodation($company_id, $append = ""){
		$entry = new Accommodation();

		$entry->company_id  = $company_id;

		$entry->name        = self::TEST_STRING.$append;
		$entry->description = self::TEST_STRING.$append;
		$entry->capacity    = self::TEST_INTEGER;

		$entry->save();
		return $entry->id;
	}

	public static function createAddon($company_id, $append = ""){
		$entry = new Addon();

		$entry->company_id        = $company_id;

		$entry->name              = self::TEST_STRING.$append;
		$entry->description       = self::TEST_STRING.$append;
		$entry->new_decimal_price = self::TEST_INTEGER;
		$entry->compulsory        = self::TEST_BOOL;

		$entry->save();
		return $entry->id;
	}

	public static function createAgency($append = ""){
		$entry = new Agency();

		$entry->abbreviation = self::TEST_ABBR;
		$entry->name         = self::TEST_STRING.$append;
		$entry->description  = self::TEST_STRING.$append;

		$entry->save();
		return $entry->id;
	}

	public static function createAgent($company_id, $append = ""){
		$entry = new Agent();

		$entry->company_id      = $company_id;

		$entry->name            = self::TEST_STRING.$append;
		$entry->website         = self::TEST_URL;
		$entry->branch_name     = self::TEST_STRING.$append;
		$entry->branch_address  = self::TEST_ADDRESS;
		$entry->branch_phone    = self::TEST_PHONE;
		$entry->branch_email    = self::TEST_EMAIL;
		$entry->billing_address = self::TEST_ADDRESS;
		$entry->billing_phone   = self::TEST_PHONE;
		$entry->billing_email   = self::TEST_EMAIL;
		$entry->commission      = self::TEST_DECIMAL;
		$entry->terms           = self::TEST_TERMS;

		$entry->save();
		return $entry->id;
	}

	public static function createBoat($company_id, $append = ""){
		$entry = new Boat();

		$entry->company_id  = $company_id;

		$entry->name        = self::TEST_STRING.$append;
		$entry->description = self::TEST_STRING.$append;
		$entry->capacity    = self::TEST_INTEGER;

		$entry->save();
		return $entry->id;
	}

	public static function createBoatroom($company_id, $append = ""){
		$entry = new Boatroom();

		$entry->company_id  = $company_id;

		$entry->name        = self::TEST_STRING.$append;
		$entry->description = self::TEST_STRING.$append;

		$entry->save();
		return $entry->id;
	}

	public static function createBooking($company_id, $agent_id, $append = ""){
		$entry = new Booking();

		$entry->company_id       = $company_id;
		$entry->agent_id         = $agent_id;

		$entry->reference        = self::TEST_REFERENCE;
		$entry->source           = self::TEST_SOURCE;
		$entry->price            = self::TEST_PRICE;
		$entry->discount         = self::TEST_INTEGER;
		$entry->confirmed        = self::TEST_BOOL;
		$entry->reserved         = self::TEST_DATETIME;
		$entry->pick_up_location = self::TEST_ADDRESS;
		$entry->pick_up_date     = self::TEST_DATE;
		$entry->pick_up_time     = self::TEST_TIME;
		$entry->comment          = self::TEST_STRING.$append;

		$entry->save();
		return $entry->id;
	}

	public static function createBookingdetail($booking_id, $customer_id, $ticket_id, $session_id, $boatroom_id, $packagefacade_id){
		$entry = new Bookingdetail();

		$entry->booking_id       = $booking_id;
		$entry->customer_id      = $customer_id;
		$entry->ticket_id        = $ticket_id;
		$entry->session_id       = $session_id;
		$entry->boatroom_id      = $boatroom_id;
		$entry->packagefacade_id = $packagefacade_id;

		$entry->save();
		return $entry->id;
	}

	public static function createCertificate($agency_id, $append = ""){
		$entry = new Certificate();

		$entry->agency_id = $agency_id;

		$entry->abbreviation = self::TEST_ABBR;
		$entry->name         = self::TEST_STRING.$append;
		$entry->description  = self::TEST_STRING.$append;

		$entry->save();
		return $entry->id;
	}

	public static function createCompany($country_id, $currency_id, $append = ""){
		$entry = new Company();

		$entry->country_id          = $country_id;
		$entry->currency_id         = $currency_id;

		$entry->username            = self::TEST_USERNAME.$append;
		$entry->password            = Hash::make(self::TEST_PASSWORD);
		$entry->email               = self::TEST_EMAIL.$append;
		$entry->verified            = self::TEST_BOOL;
		$entry->name                = self::TEST_STRING.$append;
		$entry->description         = self::TEST_STRING.$append;
		$entry->address_1           = self::TEST_STRING;
		$entry->address_2           = self::TEST_STRING;
		$entry->city                = self::TEST_STRING;
		$entry->county              = self::TEST_STRING;
		$entry->postcode            = self::TEST_STRING;
		$entry->business_email      = self::TEST_EMAIL.$append;
		$entry->business_phone      = self::TEST_PHONE;
		$entry->vat_number          = self::TEST_STRING;
		$entry->registration_number = self::TEST_STRING;
		$entry->latitude            = self::TEST_LATITUDE;
		$entry->longitude           = self::TEST_LONGITUDE;
		$entry->timezone            = self::TEST_TIMEZONE;
		$entry->phone               = self::TEST_PHONE;
		$entry->contact             = self::TEST_STRING;
		$entry->website             = self::TEST_URL;
		$entry->logo                = self::TEST_STRING.$append;
		$entry->photo               = self::TEST_STRING.$append;
		$entry->video               = self::TEST_STRING.$append;
		$entry->views               = self::TEST_INTEGER;

		$entry->save();
		return $entry->id;
	}

	public static function createContinent($append = ""){
		$entry = new Continent();

		$entry->abbreviation = self::TEST_ABBR;
		$entry->name         = self::TEST_STRING.$append;
		$entry->description  = self::TEST_STRING.$append;

		$entry->save();
		return $entry->id;
	}

	public static function createCountry($continent_id, $currency_id, $append = ""){
		$entry = new Country();

		$entry->continent_id = $continent_id;
		$entry->currency_id  = $currency_id;

		$entry->abbreviation = self::TEST_ABBR;
		$entry->name         = self::TEST_STRING.$append;
		$entry->description  = self::TEST_STRING.$append;
		$entry->flag         = self::TEST_STRING.$append;

		$entry->save();
		return $entry->id;
	}

	public static function createCurrency($append = ""){
		$entry = new Currency();

		$entry->code        = self::TEST_CURRENCY;
		$entry->name        = self::TEST_STRING.$append;
		$entry->description = self::TEST_STRING.$append;
		$entry->symbol      = self::TEST_SYMBOL.$append;

		$entry->save();
		return $entry->id;
	}

	public static function createCustomer($country_id, $company_id, $append = ""){
		$entry = new Customer();

		$entry->country_id = $country_id;
		$entry->company_id = $company_id;

		$entry->email      = self::TEST_EMAIL;
		$entry->firstname  = self::TEST_STRING.$append;
		$entry->lastname   = self::TEST_STRING.$append;
		$entry->verified   = self::TEST_BOOL;
		$entry->birthday   = self::TEST_DAY;
		$entry->gender     = self::TEST_GENDER;
		$entry->address_1  = self::TEST_STRING;
		$entry->address_2  = self::TEST_STRING;
		$entry->city       = self::TEST_STRING;
		$entry->county     = self::TEST_STRING;
		$entry->postcode   = self::TEST_STRING;
		$entry->phone      = self::TEST_PHONE;
		$entry->last_dive  = self::TEST_DAY;

		$entry->save();
		return $entry->id;
	}

	public static function createDeparture($trip_id, $boat_id, $timetable_id){
		$entry = new Departure();

		$entry->trip_id      = $trip_id;
		$entry->boat_id      = $boat_id;
		$entry->timetable_id = $timetable_id;

		$entry->start        = self::TEST_DATETIME;

		$entry->save();
		return $entry->id;
	}

	public static function createLocation($append = ""){
		$entry = new Location();

		$entry->name        = self::TEST_STRING.$append;
		$entry->description = self::TEST_STRING.$append;
		$entry->latitude    = self::TEST_DECIMAL;
		$entry->longitude   = self::TEST_DECIMAL;
		$entry->tags        = self::TEST_STRING;

		$entry->save();
		return $entry->id;
	}

	public static function createPackage($company_id, $append = ""){
		$entry = new Package();

		$entry->company_id  = $company_id;

		$entry->name        = self::TEST_STRING.$append;
		$entry->description = self::TEST_STRING.$append;
		$entry->capacity    = self::TEST_INTEGER;

		$entry->save();
		return $entry->id;
	}

	public static function createPackagefacade($package_id){
		$entry = new Packagefacade();

		$entry->package_id = $package_id;

		$entry->save();
		return $entry->id;
	}

	public static function createPayment($booking_id, $currency_id, $paymentgateway_id){
		$entry = new Payment();

		$entry->booking_id        = $booking_id;
		$entry->currency_id       = $currency_id;
		$entry->paymentgateway_id = $paymentgateway_id;

		$entry->received_at       = self::TEST_DATE;
		$entry->amount            = self::TEST_INTEGER;

		$entry->save();
		return $entry->id;
	}

	public static function createPaymentgateway($append = ""){
		$entry = new Paymentgateway();

		$entry->name = self::TEST_STRING.$append;

		$entry->save();
		return $entry->id;
	}

	public static function createPrice($owner_id){
		$entry = new Price();

		$entry->owner_id          = $owner_id;

		$entry->owner_type        = self::TEST_STRING;
		$entry->new_decimal_price = self::TEST_INTEGER;
		$entry->from              = self::TEST_DAY;
		$entry->until             = self::TEST_DAY;

		$entry->save();
		return $entry->id;
	}

	public static function createTicket($company_id, $append = ""){
		$entry = new Ticket();

		$entry->company_id  = $company_id;

		$entry->name        = self::TEST_STRING.$append;
		$entry->description = self::TEST_STRING.$append;

		$entry->save();
		return $entry->id;
	}

	public static function createTimetable($company_id){
		$entry = new Timetable();

		$entry->company_id = $company_id;

		$entry->weeks      = self::TEST_TINY_INTEGER;
		$entry->schedule   = self::TEST_JSON;

		$entry->save();
		return $entry->id;
	}

	public static function createTrip($company_id, $append = ""){
		$entry = new Trip();

		$entry->company_id  = $company_id;

		$entry->name        = self::TEST_STRING.$append;
		$entry->description = self::TEST_STRING.$append;
		$entry->duration    = self::TEST_INTEGER;
		$entry->photo       = self::TEST_STRING.$append;
		$entry->video       = self::TEST_STRING.$append;
		$entry->views       = self::TEST_INTEGER;

		$entry->save();
		return $entry->id;
	}

	public static function createTag($append = ""){
		$entry = new Tag();

		$entry->name        = self::TEST_STRING.$append;
		$entry->description = self::TEST_STRING.$append;
		$entry->for_type    = '';

		$entry->save();
		return $entry->id;
	}
}
