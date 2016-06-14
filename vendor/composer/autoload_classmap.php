<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Accommodation' => $baseDir . '/app/models/Accommodation.php',
    'AccommodationController' => $baseDir . '/app/controllers/AccommodationController.php',
    'AccommodationModelTest' => $baseDir . '/app/tests/unit/models/AccommodationModelTest.php',
    'AddActiveFieldToTicketsTable' => $baseDir . '/app/database/migrations/2014_10_08_222419_add_active_field_to_tickets_table.php',
    'AddAgentForeignToBookings' => $baseDir . '/app/database/migrations/2014_05_20_165617_add_agent_foreign_to_bookings.php',
    'AddAgentReferenceToBookingTable' => $baseDir . '/app/database/migrations/2015_02_12_203807_add_agent_reference_to_booking_table.php',
    'AddAvailableBetweenAndAvailableForBetweenColumnsToTicketsAndPackagesTables' => $baseDir . '/app/database/migrations/2015_06_18_084227_add_available_between_and_available_for_between_columns_to_tickets_and_packages_tables.php',
    'AddBoatRequiredFieldToTripsTable' => $baseDir . '/app/database/migrations/2015_09_08_192918_add_boat_required_field_to_trips_table.php',
    'AddBoatroomIdToBookingDetailsTable' => $baseDir . '/app/database/migrations/2014_12_01_234755_add_boatroom_id_to_booking_details_table.php',
    'AddBookingEmailsIntoCrm' => $baseDir . '/app/database/migrations/2015_12_20_144951_add_booking_emails_into_crm.php',
    'AddCancellationFeeToBookingTable' => $baseDir . '/app/database/migrations/2015_02_10_234000_add_cancellation_fee_to_booking_table.php',
    'AddCapacityFieldToPackagesTable' => $baseDir . '/app/database/migrations/2014_06_09_194447_add_capacity_field_to_packages_table.php',
    'AddCertificateIdToCoursesTable' => $baseDir . '/app/database/migrations/2015_08_29_090245_add_certificate_id_to_courses_table.php',
    'AddColumnTemporaryToBookingDetailsTable' => $baseDir . '/app/database/migrations/2015_09_05_112828_add_column_temporary_to_booking_details_table.php',
    'AddColumnToRepresentEmailSentToAllCustomers' => $baseDir . '/app/database/migrations/2015_12_20_131509_add_column_to_represent_email_sent_to_all_customers.php',
    'AddCompanyIdToAddonsTable' => $baseDir . '/app/database/migrations/2014_10_09_222929_add_company_id_to_addons_table.php',
    'AddCourseIdAndTrainingSessionIdToBookingDetailsTable' => $baseDir . '/app/database/migrations/2015_03_06_174025_add_course_id_and_training_session_id_to_booking_details_table.php',
    'AddCurrencyEverywhere' => $baseDir . '/app/database/migrations/2014_05_20_141758_add_currency_everywhere.php',
    'AddCurrencyFieldToTicketsAndAddonsTable' => $baseDir . '/app/database/migrations/2014_10_08_221505_add_currency_field_to_tickets_and_addons_table.php',
    'AddCurrencyToCountries' => $baseDir . '/app/database/migrations/2014_10_25_180907_add_currency_to_countries.php',
    'AddDeletedAtColumnsToAddonsAndTripsTables' => $baseDir . '/app/database/migrations/2014_10_14_012603_add_deleted_at_columns_to_addons_and_trips_tables.php',
    'AddDeletedAtColumnsToTicketsAndPackagesTable' => $baseDir . '/app/database/migrations/2014_10_10_002732_add_deleted_at_columns_to_tickets_and_packages_table.php',
    'AddDeletedAtFieldToBoatroomsTable' => $baseDir . '/app/database/migrations/2014_12_02_174659_add_deleted_at_field_to_boatrooms_table.php',
    'AddDescriptionToCompanyLocationTable' => $baseDir . '/app/database/migrations/2015_02_20_205808_add_description_to_company_location_table.php',
    'AddForeignCompanyIdToAgentsTable' => $baseDir . '/app/database/migrations/2014_05_21_102653_add_foreign_company_id_to_agents_table.php',
    'AddForeignKeysToBookingCustomerTable' => $baseDir . '/app/database/migrations/2014_05_21_103900_add_foreign_keys_to_booking_customer_table.php',
    'AddHtmlStringToCampaignsTable' => $baseDir . '/app/database/migrations/2015_10_03_165920_add_html_string_to_campaigns_table.php',
    'AddIdColumnToBookingDetailsTable' => $baseDir . '/app/database/migrations/2014_10_13_141916_add_id_column_to_booking_details_table.php',
    'AddIdToCrmTokens' => $baseDir . '/app/database/migrations/2015_11_13_093510_add_id_to_crm_tokens.php',
    'AddInitToCompanyTable' => $baseDir . '/app/database/migrations/2015_02_08_114910_add_init_to_company_table.php',
    'AddInnoDBRelationBetweenCurrenciesAndCountriesTables' => $baseDir . '/app/database/migrations/2014_10_31_163734_add_innoDB_relation_between_currencies_and_countries_tables.php',
    'AddNameToCrmCampaigns' => $baseDir . '/app/database/migrations/2015_11_15_120519_add_name_to_crm_campaigns.php',
    'AddNewColumnsToCustomerTable' => $baseDir . '/app/database/migrations/2015_08_04_172429_add_new_columns_to_customer_table.php',
    'AddOnlyPackagedFieldToTicketsTable' => $baseDir . '/app/database/migrations/2015_09_09_182240_add_only_packaged_field_to_tickets_table.php',
    'AddOpenedColumnToCrmTokens' => $baseDir . '/app/database/migrations/2015_11_13_091236_add_opened_column_to_crm_tokens.php',
    'AddOpenedTimeToLinkTracking' => $baseDir . '/app/database/migrations/2015_11_13_145244_add_opened_time_to_link_tracking.php',
    'AddPackagefacadeIdToAddonBookingdetailAndAccommodationBookingTable' => $baseDir . '/app/database/migrations/2015_03_07_205924_add_packagefacade_id_to_addon_bookingdetail_and_accommodation_booking_table.php',
    'AddParentColumnToAddonsTable' => $baseDir . '/app/database/migrations/2014_11_21_233944_add_parent_column_to_addons_table.php',
    'AddParentIdColumnToTicketsPackagesAndAccommodationsTable' => $baseDir . '/app/database/migrations/2014_11_21_224408_add_parent_id_column_to_tickets_packages_and_accommodations_table.php',
    'AddParentIdToBookingsTable' => $baseDir . '/app/database/migrations/2015_11_07_123530_add_parent_id_to_bookings_table.php',
    'AddPickUpDateFieldToBookingsTable' => $baseDir . '/app/database/migrations/2014_11_27_114937_add_pick_up_date_field_to_bookings_table.php',
    'AddPickUpsTable' => $baseDir . '/app/database/migrations/2015_10_29_130004_add_pick_ups_table.php',
    'AddQuantityColumnToAddonBookingdetailPivotTable' => $baseDir . '/app/database/migrations/2014_10_14_010948_add_quantity_column_to_addon_bookingdetail_pivot_table.php',
    'AddQuantityFieldToPickUpsTable' => $baseDir . '/app/database/migrations/2015_12_07_152924_add_quantity_field_to_pick_ups_table.php',
    'AddRecievedAtColumnToPaymentsTable' => $baseDir . '/app/database/migrations/2014_12_01_003730_add_recieved_at_column_to_payments_table.php',
    'AddRegisterDetailsToCompanies' => $baseDir . '/app/database/migrations/2014_11_03_123343_add_register_details_to_companies.php',
    'AddRememberTokenToCompaniesBecauseOfLaravelUpdate' => $baseDir . '/app/database/migrations/2014_05_08_171023_add_remember_token_to_companies_because_of_laravel_update.php',
    'AddSavedColumnToBookingsTable' => $baseDir . '/app/database/migrations/2014_11_23_172511_add_saved_column_to_bookings_table.php',
    'AddSchedulesTable' => $baseDir . '/app/database/migrations/2015_10_19_105347_add_schedules_table.php',
    'AddServiceDateToEquipment' => $baseDir . '/app/database/migrations/2016_02_21_192209_add_service_date_to_equipment.php',
    'AddSoftDeleteForCampaigns' => $baseDir . '/app/database/migrations/2015_12_20_165935_add_soft_delete_for_campaigns.php',
    'AddSoftDeletionToBoatsTable' => $baseDir . '/app/database/migrations/2014_11_18_165840_add_soft_deletion_to_boats_table.php',
    'AddStatusFieldToBookingsTable' => $baseDir . '/app/database/migrations/2014_12_11_200307_add_status_field_to_bookings_table.php',
    'AddThaiBahtToCurrencies' => $baseDir . '/app/database/migrations/2015_03_26_132625_add_thai_baht_to_currencies.php',
    'AddTimestampsToCoursePivotTables' => $baseDir . '/app/database/migrations/2015_03_16_140240_add_timestamps_to_course_pivot_tables.php',
    'AddTimestampsToCrmTokens' => $baseDir . '/app/database/migrations/2015_11_10_214022_add_timestamps_to_crm_tokens.php',
    'AddTimezoneFieldToCompaniesTable' => $baseDir . '/app/database/migrations/2014_12_01_153154_add_timezone_field_to_companies_table.php',
    'AddTrainingIdToBookingDetailsTable' => $baseDir . '/app/database/migrations/2015_09_06_123630_add_training_id_to_booking_details_table.php',
    'Addon' => $baseDir . '/app/models/Addon.php',
    'AddonController' => $baseDir . '/app/controllers/AddonController.php',
    'AddonModelTest' => $baseDir . '/app/tests/unit/models/AddonModelTest.php',
    'AdminController' => $baseDir . '/app/controllers/AdminController.php',
    'AgenciesTableSeeder' => $baseDir . '/app/database/seeds/AgenciesTableSeeder.php',
    'Agency' => $baseDir . '/app/models/Agency.php',
    'AgencyController' => $baseDir . '/app/controllers/AgencyController.php',
    'AgencyModelTest' => $baseDir . '/app/tests/unit/models/AgencyModelTest.php',
    'Agent' => $baseDir . '/app/models/Agent.php',
    'AgentController' => $baseDir . '/app/controllers/AgentController.php',
    'AgentModelTest' => $baseDir . '/app/tests/unit/models/AgentModelTest.php',
    'AllowNullForAccommodationIdInBoatTicketTable' => $baseDir . '/app/database/migrations/2014_10_24_012849_allow_null_for_accommodation_id_in_boat_ticket_table.php',
    'AlterBookingsTable' => $baseDir . '/app/database/migrations/2014_07_26_162601_alter_bookings_table.php',
    'AlterLastDiveOnCustomersToDate' => $baseDir . '/app/database/migrations/2014_05_24_204451_alter_last_dive_on_customers_to_date.php',
    'AuthenticationController' => $baseDir . '/app/controllers/AuthenticationController.php',
    'BaseController' => $baseDir . '/app/controllers/BaseController.php',
    'Boat' => $baseDir . '/app/models/Boat.php',
    'BoatController' => $baseDir . '/app/controllers/BoatController.php',
    'BoatModelTest' => $baseDir . '/app/tests/unit/models/BoatModelTest.php',
    'Boatroom' => $baseDir . '/app/models/Boatroom.php',
    'BoatroomController' => $baseDir . '/app/controllers/BoatroomController.php',
    'BoatroomModelTest' => $baseDir . '/app/tests/unit/models/BoatroomModelTest.php',
    'Booking' => $baseDir . '/app/models/Booking.php',
    'BookingController' => $baseDir . '/app/controllers/BookingController.php',
    'BookingModelTest' => $baseDir . '/app/tests/unit/models/BookingModelTest.php',
    'Bookingdetail' => $baseDir . '/app/models/Bookingdetail.php',
    'BookingdetailModelTest' => $baseDir . '/app/tests/unit/models/BookingdetailModelTest.php',
    'CSVSeeder' => $baseDir . '/app/database/seeds/CSVSeeder.php',
    'Certificate' => $baseDir . '/app/models/Certificate.php',
    'CertificateController' => $baseDir . '/app/controllers/CertificateController.php',
    'CertificateModelTest' => $baseDir . '/app/tests/unit/models/CertificateModelTest.php',
    'CertificatesTableSeeder' => $baseDir . '/app/database/seeds/CertificatesTableSeeder.php',
    'ChangeAgentCommissionToDecimal' => $baseDir . '/app/database/migrations/2014_05_30_155319_change_agent_commission_to_decimal.php',
    'ChangeAllPriceFieldsToInteger' => $baseDir . '/app/database/migrations/2014_06_11_131500_change_all_price_fields_to_integer.php',
    'ChangeBookingDetailsToIncludeCustomerIdAndLeadStatus' => $baseDir . '/app/database/migrations/2014_06_04_105146_change_booking_details_to_include_customer_id_and_lead_status.php',
    'ChangeCrmLinkTrackerName' => $baseDir . '/app/database/migrations/2015_11_13_154803_change_crm_link_tracker_name.php',
    'ChangeCrmTokensToUseBigint' => $baseDir . '/app/database/migrations/2015_11_13_160150_change_crm_tokens_to_use_bigint.php',
    'ChangeDayAndMonthFieldsOnPricesTableIntoDateFields' => $baseDir . '/app/database/migrations/2014_11_03_131716_change_day_and_month_fields_on_prices_table_into_date_fields.php',
    'ChangeDurationToDecimal' => $baseDir . '/app/database/migrations/2015_01_24_172633_change_duration_to_decimal.php',
    'ChangeEquipmentPriceAddDuration' => $baseDir . '/app/database/migrations/2016_02_21_180238_change_equipment_price_add_duration.php',
    'ChangeEquipmentPriceDurationToFloat' => $baseDir . '/app/database/migrations/2016_02_21_175625_change_equipment_price_duration_to_float.php',
    'ChangeForeignKeyPackagefacadeIdOnBookingDetailsTable' => $baseDir . '/app/database/migrations/2015_03_18_095141_change_foreign_key_packagefacade_id_on_booking_details_table.php',
    'ChangeForeignKeyTrainingIdOnCoursesTable' => $baseDir . '/app/database/migrations/2015_03_16_134334_change_foreign_key_training_id_on_courses_table.php',
    'ChangeLatitudeAndLongitudeFieldsToDoupleType' => $baseDir . '/app/database/migrations/2015_02_11_015111_change_latitude_and_longitude_fields_to_douple_type.php',
    'ChangeNightsColumnToEndDateOnAccommodationBooking' => $baseDir . '/app/database/migrations/2014_11_21_194518_change_nights_column_to_end_date_on_accommodation_booking.php',
    'ChangePackageRelationsToPolymorphic' => $baseDir . '/app/database/migrations/2015_03_22_141010_change_package_relations_to_polymorphic.php',
    'ChangePasswordFieldOnUsersTableToNullableDefaultNull' => $baseDir . '/app/database/migrations/2015_12_12_073235_change_password_field_on_users_table_to_nullable_default_null.php',
    'ChangeTriptypesToTags' => $baseDir . '/app/database/migrations/2014_12_02_194157_change_triptypes_to_tags.php',
    'Company' => $baseDir . '/app/models/Company.php',
    'CompanyController' => $baseDir . '/app/controllers/CompanyController.php',
    'CompanyModelTest' => $baseDir . '/app/tests/unit/models/CompanyModelTest.php',
    'ConnectAddonsTableWithCompaniesTable' => $baseDir . '/app/database/migrations/2014_10_13_135049_connect_addons_table_with_companies_table.php',
    'Continent' => $baseDir . '/app/models/Continent.php',
    'ContinentModelTest' => $baseDir . '/app/tests/unit/models/ContinentModelTest.php',
    'ContinentsTableSeeder' => $baseDir . '/app/database/seeds/ContinentsTableSeeder.php',
    'ConvertAddonPricesIntoBasePrices' => $baseDir . '/app/database/migrations/2015_12_12_082657_convert_addon_prices_into_base_prices.php',
    'CopyUserDataFromCompaniesTableToUsersTable' => $baseDir . '/app/database/migrations/2015_11_03_122632_copy_user_data_from_companies_table_to_users_table.php',
    'CountriesTableSeeder' => $baseDir . '/app/database/seeds/CountriesTableSeeder.php',
    'Country' => $baseDir . '/app/models/Country.php',
    'CountryController' => $baseDir . '/app/controllers/CountryController.php',
    'CountryModelTest' => $baseDir . '/app/tests/unit/models/CountryModelTest.php',
    'Course' => $baseDir . '/app/models/Course.php',
    'CourseController' => $baseDir . '/app/controllers/CourseController.php',
    'CreateAccommodationBoatTable' => $baseDir . '/app/database/migrations/2014_02_17_020518_create_accommodation_boat_table.php',
    'CreateAccommodationBookingTable' => $baseDir . '/app/database/migrations/2014_11_11_191720_create_accommodation_booking_table.php',
    'CreateAccommodationsTable' => $baseDir . '/app/database/migrations/2014_02_17_020436_create_accommodations_table.php',
    'CreateAddonBookingDetailPivotTable' => $baseDir . '/app/database/migrations/2014_10_13_142042_create_addon_booking_detail_pivot_table.php',
    'CreateAddonsTable' => $baseDir . '/app/database/migrations/2014_08_12_121905_create_addons_table.php',
    'CreateAgenciesTable' => $baseDir . '/app/database/migrations/2014_02_17_021059_create_agencies_table.php',
    'CreateAgencyCompanyPivotTable' => $baseDir . '/app/database/migrations/2014_11_03_231722_create_agency_company_pivot_table.php',
    'CreateAgentsTable' => $baseDir . '/app/database/migrations/2014_05_20_134127_create_agents_table.php',
    'CreateBoatTicketTable' => $baseDir . '/app/database/migrations/2014_02_17_020907_create_boat_ticket_table.php',
    'CreateBoatsTable' => $baseDir . '/app/database/migrations/2014_02_17_020249_create_boats_table.php',
    'CreateBookingDetailsTable' => $baseDir . '/app/database/migrations/2014_02_17_021844_create_booking_details_table.php',
    'CreateBookingsTable' => $baseDir . '/app/database/migrations/2014_02_17_021810_create_bookings_table.php',
    'CreateCertificateCustomerPivotTable' => $baseDir . '/app/database/migrations/2014_10_31_222143_create_certificate_customer_pivot_table.php',
    'CreateCertificatesTable' => $baseDir . '/app/database/migrations/2014_02_17_021060_create_certificates_table.php',
    'CreateCompaniesTable' => $baseDir . '/app/database/migrations/2014_02_17_014940_create_companies_table.php',
    'CreateCompanyLocationPivotTable' => $baseDir . '/app/database/migrations/2014_06_04_094433_create_company_location_pivot_table.php',
    'CreateContinentsTable' => $baseDir . '/app/database/migrations/2014_02_17_013607_create_continents_table.php',
    'CreateCountriesTable' => $baseDir . '/app/database/migrations/2014_02_17_014835_create_countries_table.php',
    'CreateCoursePackageTable' => $baseDir . '/app/database/migrations/2015_03_06_181956_create_course_package_table.php',
    'CreateCourseTicketPivotTable' => $baseDir . '/app/database/migrations/2015_03_06_175435_create_course_ticket_pivot_table.php',
    'CreateCourseTrainingPivotTable' => $baseDir . '/app/database/migrations/2015_08_18_162726_create_course_training_pivot_table.php',
    'CreateCoursesTable' => $baseDir . '/app/database/migrations/2015_03_06_173325_create_courses_table.php',
    'CreateCrmCustomerSubscription' => $baseDir . '/app/database/migrations/2015_12_05_103243_create_crm_customer_subscription.php',
    'CreateCrmLinksTable' => $baseDir . '/app/database/migrations/2015_11_13_141907_create_crm_links_table.php',
    'CreateCrmLinksTrackerTable' => $baseDir . '/app/database/migrations/2015_11_13_141927_create_crm_links_tracker_table.php',
    'CreateCrmTables' => $baseDir . '/app/database/migrations/2015_08_22_135836_create_crm_tables.php',
    'CreateCrmTemplatesTable' => $baseDir . '/app/database/migrations/2015_11_09_210353_create_crm_templates_table.php',
    'CreateCrmTokensTable' => $baseDir . '/app/database/migrations/2015_11_09_210400_create_crm_tokens_table.php',
    'CreateCurrenciesTable' => $baseDir . '/app/database/migrations/2014_10_25_173618_create_currencies_table.php',
    'CreateCustomersTable' => $baseDir . '/app/database/migrations/2014_02_17_021744_create_customers_table.php',
    'CreateEquipmentCategoryTable' => $baseDir . '/app/database/migrations/2016_01_23_122212_create_equipment_category_table.php',
    'CreateEquipmentPricesTable' => $baseDir . '/app/database/migrations/2016_01_23_132118_create_equipment_prices_table.php',
    'CreateEquipmentTable' => $baseDir . '/app/database/migrations/2016_01_23_125203_create_equipment_table.php',
    'CreateHotelAccommodationsTable' => $baseDir . '/app/database/migrations/2014_11_11_132623_create_hotel_accommodations_table.php',
    'CreateLocationTripTable' => $baseDir . '/app/database/migrations/2014_02_17_020801_create_location_trip_table.php',
    'CreateLocationsTable' => $baseDir . '/app/database/migrations/2014_02_17_020714_create_locations_table.php',
    'CreateMutexTable' => $baseDir . '/app/database/migrations/2016_05_08_150615_create_mutex_table.php',
    'CreatePackageFacadeSystem' => $baseDir . '/app/database/migrations/2014_10_18_153824_create_package_facade_system.php',
    'CreatePackageTicketTable' => $baseDir . '/app/database/migrations/2014_02_17_021041_create_package_ticket_table.php',
    'CreatePackagesTable' => $baseDir . '/app/database/migrations/2014_02_17_021010_create_packages_table.php',
    'CreatePasswordRemindersTable' => $baseDir . '/app/database/migrations/2014_02_17_002807_create_password_reminders_table.php',
    'CreatePaymentGatewaysTable' => $baseDir . '/app/database/migrations/2014_06_11_114105_create_payment_gateways_table.php',
    'CreatePaymentsTable' => $baseDir . '/app/database/migrations/2014_06_11_114106_create_payments_table.php',
    'CreatePricesForTicketsAndPackages' => $baseDir . '/app/database/migrations/2014_11_02_005053_create_prices_for_tickets_and_packages.php',
    'CreatePricesTable' => $baseDir . '/app/database/migrations/2014_10_31_231352_create_prices_table.php',
    'CreateRefundsTable' => $baseDir . '/app/database/migrations/2015_02_20_174754_create_refunds_table.php',
    'CreateRegionsTable' => $baseDir . '/app/database/migrations/2014_02_17_014920_create_regions_table.php',
    'CreateSessionsTable' => $baseDir . '/app/database/migrations/2014_02_17_021058_create_sessions_table.php',
    'CreateTicketTripTable' => $baseDir . '/app/database/migrations/2014_06_04_163225_create_ticket_trip_table.php',
    'CreateTicketsTable' => $baseDir . '/app/database/migrations/2014_02_17_020836_create_tickets_table.php',
    'CreateTimetablesTable' => $baseDir . '/app/database/migrations/2014_03_24_141238_create_timetables_table.php',
    'CreateTrainingSessionsTable' => $baseDir . '/app/database/migrations/2015_03_06_173415_create_training_sessions_table.php',
    'CreateTrainingsTable' => $baseDir . '/app/database/migrations/2015_03_06_173159_create_trainings_table.php',
    'CreateTripTriptypeTable' => $baseDir . '/app/database/migrations/2014_02_17_020748_create_trip_triptype_table.php',
    'CreateTripsTable' => $baseDir . '/app/database/migrations/2014_02_17_020733_create_trips_table.php',
    'CreateTriptypesTable' => $baseDir . '/app/database/migrations/2014_02_17_020706_create_triptypes_table.php',
    'CreateUsersTable' => $baseDir . '/app/database/migrations/2015_11_03_121624_create_users_table.php',
    'CrmCampaign' => $baseDir . '/app/models/CrmCampaign.php',
    'CrmCampaignController' => $baseDir . '/app/controllers/CrmCampaignController.php',
    'CrmGroup' => $baseDir . '/app/models/CrmGroup.php',
    'CrmGroupController' => $baseDir . '/app/controllers/CrmGroupController.php',
    'CrmGroupRule' => $baseDir . '/app/models/CrmGroupRule.php',
    'CrmLink' => $baseDir . '/app/models/CrmLink.php',
    'CrmLinkTracker' => $baseDir . '/app/models/CrmLinkTracker.php',
    'CrmSubscription' => $baseDir . '/app/models/CrmSubscription.php',
    'CrmTemplate' => $baseDir . '/app/models/CrmTemplate.php',
    'CrmTemplateController' => $baseDir . '/app/controllers/CrmTemplateController.php',
    'CrmToken' => $baseDir . '/app/models/CrmToken.php',
    'CrmTrackingController' => $baseDir . '/app/controllers/CrmTrackingController.php',
    'CronRunCommand' => $baseDir . '/app/commands/CronRunCommand.php',
    'CurrenciesTableSeeder' => $baseDir . '/app/database/seeds/CurrenciesTableSeeder.php',
    'Currency' => $baseDir . '/app/models/Currency.php',
    'CurrencyController' => $baseDir . '/app/controllers/CurrencyController.php',
    'CurrencyModelTest' => $baseDir . '/app/tests/unit/models/CurrencyModelTest.php',
    'Customer' => $baseDir . '/app/models/Customer.php',
    'CustomerController' => $baseDir . '/app/controllers/CustomerController.php',
    'CustomerModelTest' => $baseDir . '/app/tests/unit/models/CustomerModelTest.php',
    'DatabaseSeeder' => $baseDir . '/app/database/seeds/DatabaseSeeder.php',
    'Departure' => $baseDir . '/app/models/Departure.php',
    'DepartureController' => $baseDir . '/app/controllers/DepartureController.php',
    'DepartureModelTest' => $baseDir . '/app/tests/unit/models/DepartureModelTest.php',
    'DropCompanyIdColumnEquipment' => $baseDir . '/app/database/migrations/2016_02_21_163700_drop_company_id_column_equipment.php',
    'DropCompanyIdColumnEquipmentPrices' => $baseDir . '/app/database/migrations/2016_02_21_162548_drop_company_id_column_equipment_prices.php',
    'DropCoursePackageTable' => $baseDir . '/app/database/migrations/2015_03_22_151352_drop_course_package_table.php',
    'EditSessionsTable' => $baseDir . '/app/database/migrations/2014_03_24_155948_edit_sessions_table.php',
    'EditTimetablesTable' => $baseDir . '/app/database/migrations/2014_03_24_161126_edit_timetables_table.php',
    'EmptyAccommodationsTableAndDeleteAccommodationPrices' => $baseDir . '/app/database/migrations/2014_11_20_211435_empty_accommodations_table_and_delete_accommodation_prices.php',
    'EmptyBookingsTable' => $baseDir . '/app/database/migrations/2014_11_24_140450_empty_bookings_table.php',
    'Equipment' => $baseDir . '/app/models/Equipment.php',
    'EquipmentCategory' => $baseDir . '/app/models/EquipmentCategory.php',
    'EquipmentCategoryController' => $baseDir . '/app/controllers/EquipmentCategoryController.php',
    'EquipmentController' => $baseDir . '/app/controllers/EquipmentController.php',
    'EquipmentPrice' => $baseDir . '/app/models/EquipmentPrice.php',
    'EquipmentPriceController' => $baseDir . '/app/controllers/EquipmentPriceController.php',
    'ExtentBookingsTable' => $baseDir . '/app/database/migrations/2014_05_20_141137_extent_bookings_table.php',
    'FillTimezoneFieldOnCompaniesTable' => $baseDir . '/app/database/migrations/2014_12_01_153430_fill_timezone_field_on_companies_table.php',
    'FixAndReseedCountriesAndCurrenciesTables' => $baseDir . '/app/database/migrations/2015_03_29_171129_fix_and_reseed_countries_and_currencies_tables.php',
    'FixBookingsTable' => $baseDir . '/app/database/migrations/2014_05_20_165002_fix_bookings_table.php',
    'HomeController' => $baseDir . '/app/controllers/HomeController.php',
    'IlluminateQueueClosure' => $vendorDir . '/laravel/framework/src/Illuminate/Queue/IlluminateQueueClosure.php',
    'InjectCustomersBookingsTable' => $baseDir . '/app/database/migrations/2014_05_21_095057_inject_customers_bookings_table.php',
    'Location' => $baseDir . '/app/models/Location.php',
    'LocationController' => $baseDir . '/app/controllers/LocationController.php',
    'LocationModelTest' => $baseDir . '/app/tests/unit/models/LocationModelTest.php',
    'MakeBoatIdNullableInSessionsTable' => $baseDir . '/app/database/migrations/2015_09_08_193915_make_boat_id_nullable_in_sessions_table.php',
    'MakeBoatIdNullableOnBoatTicketTable' => $baseDir . '/app/database/migrations/2014_11_20_181000_make_boat_id_nullable_on_boat_ticket_table.php',
    'MakeCustomerTableColumnsNullable' => $baseDir . '/app/database/migrations/2014_07_13_232753_make_customer_table_columns_nullable.php',
    'MakeCustomerTableGenderNullable' => $baseDir . '/app/database/migrations/2014_07_15_030404_make_customer_table_gender_nullable.php',
    'MakeDiscountColumnOnBookingTableIntoInteger' => $baseDir . '/app/database/migrations/2014_07_26_164147_make_discount_column_on_booking_table_into_integer.php',
    'MakeFieldsInCompanyTableNullable' => $baseDir . '/app/database/migrations/2014_11_08_152034_make_fields_in_company_table_nullable.php',
    'MakeOtherFieldsNullableOnCompaniesTable' => $baseDir . '/app/database/migrations/2014_11_08_180948_make_other_fields_nullable_on_companies_table.php',
    'MakeTicketBoatBoatroomRelationshipPolymorphic' => $baseDir . '/app/database/migrations/2015_03_11_182409_make_ticket_boat_boatroom_relationship_polymorphic.php',
    'MakeTicketIdAndSessionIdNullableOnBookingDetailsTable' => $baseDir . '/app/database/migrations/2015_03_07_195147_make_ticket_id_and_session_id_nullable_on_booking_details_table.php',
    'MakeTrainingIdAndTrainingQuantityNullableOnCoursesTable' => $baseDir . '/app/database/migrations/2015_03_23_112635_make_training_id_and_training_quantity_nullable_on_courses_table.php',
    'ModelRelationshipsTest' => $baseDir . '/app/tests/integration/ModelRelationshipsTest.php',
    'ModelTestCase' => $baseDir . '/app/tests/unit/models/ModelTestCase.php',
    'ModelTestHelper' => $baseDir . '/app/tests/helpers/ModelTestHelper.php',
    'MoveIsLeadColumnFromBookingDetailsToBookingsTable' => $baseDir . '/app/database/migrations/2014_11_24_135928_move_is_lead_column_from_booking_details_to_bookings_table.php',
    'MovePhoneFieldFromCompaniesTableToUsersTable' => $baseDir . '/app/database/migrations/2015_11_03_144634_move_phone_field_from_companies_table_to_users_table.php',
    'Normalizer' => $vendorDir . '/patchwork/utf8/src/Normalizer.php',
    'NullifyBadBirthdayDatesInCustomersTable' => $baseDir . '/app/database/migrations/2015_11_16_195306_nullify_bad_birthday_dates_in_customers_table.php',
    'Package' => $baseDir . '/app/models/Package.php',
    'PackageController' => $baseDir . '/app/controllers/PackageController.php',
    'PackageModelTest' => $baseDir . '/app/tests/unit/models/PackageModelTest.php',
    'Packagefacade' => $baseDir . '/app/models/Packagefacade.php',
    'PackagefacadeModelTest' => $baseDir . '/app/tests/unit/models/PackagefacadeModelTest.php',
    'PasswordController' => $baseDir . '/app/controllers/PasswordController.php',
    'Payment' => $baseDir . '/app/models/Payment.php',
    'PaymentController' => $baseDir . '/app/controllers/PaymentController.php',
    'PaymentModelTest' => $baseDir . '/app/tests/unit/models/PaymentModelTest.php',
    'Paymentgateway' => $baseDir . '/app/models/Paymentgateway.php',
    'PaymentgatewayModelTest' => $baseDir . '/app/tests/unit/models/PaymentgatewayModelTest.php',
    'PaymentgatewaysTableSeeder' => $baseDir . '/app/database/seeds/PaymentgatewaysTableSeeder.php',
    'PickUp' => $baseDir . '/app/models/PickUp.php',
    'Price' => $baseDir . '/app/models/Price.php',
    'PriceModelTest' => $baseDir . '/app/tests/unit/models/PriceModelTest.php',
    'Refund' => $baseDir . '/app/models/Refund.php',
    'RefundController' => $baseDir . '/app/controllers/RefundController.php',
    'RegisterController' => $baseDir . '/app/controllers/RegisterController.php',
    'RemoveActiveColumnFromTicketsTable' => $baseDir . '/app/database/migrations/2014_10_24_015813_remove_active_column_from_tickets_table.php',
    'RemoveCapacityFromPackagesTable' => $baseDir . '/app/database/migrations/2015_03_06_174118_remove_capacity_from_packages_table.php',
    'RemoveCurrencyFieldsFromAllTablesExceptCompanies' => $baseDir . '/app/database/migrations/2014_11_08_162602_remove_currency_fields_from_all_tables_except_companies.php',
    'RemoveDescriptionColumnFromCurrenciesAndCountriesTable' => $baseDir . '/app/database/migrations/2015_03_26_105613_remove_description_column_from_currencies_and_countries_table.php',
    'RemoveLocationIdFromTripTable' => $baseDir . '/app/database/migrations/2014_12_01_201547_remove_location_id_from_trip_table.php',
    'RemovePaymentFieldsFromBookingsTable' => $baseDir . '/app/database/migrations/2014_06_11_114251_remove_payment_fields_from_bookings_table.php',
    'RemovePickUpFieldsFromBookingTable' => $baseDir . '/app/database/migrations/2015_10_29_134256_remove_pick_up_fields_from_booking_table.php',
    'RemovePriceFieldFromAddonsTable' => $baseDir . '/app/database/migrations/2015_12_12_085026_remove_price_field_from_addons_table.php',
    'RemoveRegionsTable' => $baseDir . '/app/database/migrations/2014_06_04_103901_remove_regions_table.php',
    'RemoveTagsFieldFromLocationsTable' => $baseDir . '/app/database/migrations/2014_12_03_131211_remove_tags_field_from_locations_table.php',
    'RemoveUniqueIndexFromEmailInCustomerTable' => $baseDir . '/app/database/migrations/2014_07_16_152245_remove_unique_index_from_email_in_customer_table.php',
    'RemoveUnneededColumnsFromCustomers' => $baseDir . '/app/database/migrations/2014_05_24_203935_remove_unneeded_columns_from_customers.php',
    'RemoveUnneededIdColumns' => $baseDir . '/app/database/migrations/2014_02_19_143751_remove_unneeded_id_columns.php',
    'RemoveUserDataFromCompaniesTable' => $baseDir . '/app/database/migrations/2015_11_03_132048_remove_user_data_from_companies_table.php',
    'RenameAccommodationsTableToBoatrooms' => $baseDir . '/app/database/migrations/2014_11_11_081111_rename_accommodations_table_to_boatrooms.php',
    'RenameAllAccommodationIdInstancesToBoatroomId' => $baseDir . '/app/database/migrations/2014_11_11_084413_rename_all_accommodation_id_instances_to_boatroom_id.php',
    'RenameColumnReservedToReservedUntilOnBookingsTable' => $baseDir . '/app/database/migrations/2015_08_31_220623_rename_column_reserved_to_reserved_until_on_bookings_table.php',
    'RenameEquipmentcategoriesTable' => $baseDir . '/app/database/migrations/2016_02_19_092629_rename_equipmentcategories_table.php',
    'RenameEquipmentcategoriesTableBack' => $baseDir . '/app/database/migrations/2016_02_19_092944_rename_equipmentcategories_table_back.php',
    'RenameIndexesOnBoatBoatroomTable' => $baseDir . '/app/database/migrations/2014_11_16_191954_rename_indexes_on_boat_boatroom_table.php',
    'RenameInitToInitialisedOnCompaniesTable' => $baseDir . '/app/database/migrations/2015_02_15_180954_rename_init_to_initialised_on_companies_table.php',
    'RenameQuantityToCapacityOnAccommodationsTable' => $baseDir . '/app/database/migrations/2014_11_11_210332_rename_quantity_to_capacity_on_accommodations_table.php',
    'ReportController' => $baseDir . '/app/controllers/ReportController.php',
    'ReseedCurrenciesAndCountriesDataAndUpdateForeignKeys' => $baseDir . '/app/database/migrations/2015_03_26_111738_reseed_currencies_and_countries_data_and_update_foreign_keys.php',
    'Schedule' => $baseDir . '/app/models/Schedule.php',
    'ScheduleController' => $baseDir . '/app/controllers/ScheduleController.php',
    'ScubaWhere\\Context' => $baseDir . '/app/lib/scubawhere/Context.php',
    'ScubaWhere\\CrmUtils' => $baseDir . '/app/lib/scubawhere/CrmUtils.php',
    'ScubaWhere\\Helper' => $baseDir . '/app/lib/scubawhere/Helper.php',
    'ScubaWhere\\Mailer' => $baseDir . '/app/lib/scubawhere/Mailer.php',
    'SearchController' => $baseDir . '/app/controllers/SearchController.php',
    'SeedUpdatedAgencyAndCertificateDataSeptember2015' => $baseDir . '/app/database/migrations/2015_09_09_193042_seed_updated_agency_and_certificate_data_september_2015.php',
    'SessionHandlerInterface' => $vendorDir . '/symfony/http-foundation/Symfony/Component/HttpFoundation/Resources/stubs/SessionHandlerInterface.php',
    'SetCompanyLocationToProDiveCairns' => $baseDir . '/app/database/migrations/2014_10_26_233629_set_company_location_to_pro_dive_cairns.php',
    'SetReservedToNullForConfirmedBookings' => $baseDir . '/app/database/migrations/2015_05_26_183245_set_reserved_to_null_for_confirmed_bookings.php',
    'SetTrainingIdsForExistingBookingDetails' => $baseDir . '/app/database/migrations/2015_09_06_143107_set_training_ids_for_existing_booking_details.php',
    'SetsBoaroomForeignKeyInBoatBoatroomTableToRestrictOnDelete' => $baseDir . '/app/database/migrations/2014_11_16_191219_sets_boaroom_foreign_key_in_boat_boatroom_table_to_restrict_on_delete.php',
    'Tag' => $baseDir . '/app/models/Tag.php',
    'TagModelTest' => $baseDir . '/app/tests/unit/models/TagModelTest.php',
    'TagsTableSeeder' => $baseDir . '/app/database/seeds/TagsTableSeeder.php',
    'TestCase' => $baseDir . '/app/tests/TestCase.php',
    'TestController' => $baseDir . '/app/controllers/TestController.php',
    'TestHelper' => $baseDir . '/app/tests/helpers/TestHelper.php',
    'TestSettings' => $baseDir . '/app/tests/TestSettings.php',
    'Ticket' => $baseDir . '/app/models/Ticket.php',
    'TicketController' => $baseDir . '/app/controllers/TicketController.php',
    'TicketModelTest' => $baseDir . '/app/tests/unit/models/TicketModelTest.php',
    'Timetable' => $baseDir . '/app/models/Timetable.php',
    'TimetableController' => $baseDir . '/app/controllers/TimetableController.php',
    'TimetableModelTest' => $baseDir . '/app/tests/unit/models/TimetableModelTest.php',
    'Training' => $baseDir . '/app/models/Training.php',
    'TrainingController' => $baseDir . '/app/controllers/TrainingController.php',
    'TrainingSession' => $baseDir . '/app/models/TrainingSession.php',
    'TrainingSessionController' => $baseDir . '/app/controllers/TrainingSessionController.php',
    'TransferExistingPickUpsToNewPickUpsTable' => $baseDir . '/app/database/migrations/2015_10_29_132525_transfer_existing_pick_ups_to_new_pick_ups_table.php',
    'Trip' => $baseDir . '/app/models/Trip.php',
    'TripController' => $baseDir . '/app/controllers/TripController.php',
    'TripModelTest' => $baseDir . '/app/tests/unit/models/TripModelTest.php',
    'TruncateBoatTicketTable' => $baseDir . '/app/database/migrations/2014_11_20_185558_truncate_boat_ticket_table.php',
    'UpdateAgenciesTable' => $baseDir . '/app/database/migrations/2015_02_07_154847_update_agencies_table.php',
    'UpdateCustomersTable' => $baseDir . '/app/database/migrations/2014_02_18_234149_update_customers_table.php',
    'UpdateDescriptionToTextTypeOnAddonsTable' => $baseDir . '/app/database/migrations/2015_03_11_170335_update_description_to_text_type_on_addons_table.php',
    'User' => $baseDir . '/app/models/User.php',
    'Whoops\\Module' => $vendorDir . '/filp/whoops/src/deprecated/Zend/Module.php',
    'Whoops\\Provider\\Zend\\ExceptionStrategy' => $vendorDir . '/filp/whoops/src/deprecated/Zend/ExceptionStrategy.php',
    'Whoops\\Provider\\Zend\\RouteNotFoundStrategy' => $vendorDir . '/filp/whoops/src/deprecated/Zend/RouteNotFoundStrategy.php',
);
