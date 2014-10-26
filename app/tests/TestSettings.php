<?php

/**
 * Contains any setting related to the test environment 
 */
class TestSettings {

	/**
	 * Array of all unseeded models with their table names
	 * @var array of unseeded models (name => table)
	 */
	public static $modelTables = array(
			'Accommodation' => 'accommodations',
			'Addon' => 'addons',
			'Agent' => 'agents',
			'Boat' => 'boats',
			'Booking' => 'bookings',
			'Bookingdetail' => 'booking_details',
			'Company' => 'companies',
			'Customer' => 'customers',
			'Departure' => 'sessions',
			'Location' => 'locations',
			'Package' => 'packages',
			'Packagefacade' => 'packagefacades',
			'Payment' => 'payments',
			'Ticket' => 'tickets',
			'Timetable' => 'timetables',
			'Trip' => 'trips'
	);
	
	
	
	/**
	 * Array of all seeded models with their table names
	 * @var array of seeded models (name => table)
	*/
	public static $seededModelTables = array(
			'Agency' => 'agencies',
			'Certificate' => 'certificates',
			'Continent' => 'continents',
			'Country' => 'countries',
			'Currency' => 'currencies',
			'Paymentgateway' => 'paymentgateways',
			'Triptype' => 'triptypes'
	);
	
	
	
	/**
	 * Array of all pivot table names
	 * @var array of pivot table names
	*/
	public static $pivotTables = array(
			'accommodation_boat',
			'addon_bookingdetail',
			'boat_ticket',
			'company_location',
			'location_trip',
			'package_ticket',
			'ticket_trip',
			'trip_triptype'
	);
	
}

