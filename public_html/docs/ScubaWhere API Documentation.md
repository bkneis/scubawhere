[![](http://scubawhere.com/docs/ScubaWhere_Logo.svg)](http://scubawhere.com/docs)

# Internal API Documentation

<div id="contents">
<h3>Table of Contents</h3>
<ol></ol>
</div>

Documentation for the ScubaWhere.com API

- **@version** 0.9
- **@date**    2014/05/26 - 1:15

> ### Important
> On **success**, the response contains either the requested information, the string `status` or (for calls that create something) `status` and `id`.

> If a request **fails**, the response *always* contains an `errors` array and has a HTTP code >= 400.

## Authentication & User Management
All requests to the API, when coming from AJAX, are automatically sent and returned with a `scubawhere_session` cookie. This cookie contains a unique keystring that is used by the system to identify each user individually. The system also stores wheather a user is logged in. So no additional authentification information is needed to be sent with requests.

### Login

`POST /login`

Logs the company in with their credentials.

- **@param** string  username
- **@param** string  password
- **@param** boolean remember
- &nbsp;
- **@return** JSON   Contains `status` on success, `errors` on failure

### Logout

`GET /logout`

Logs a company out by their session key cookie, if logged in.

- **@return** JSON   Always succeeds. Always contains `status`

### Register a company

`POST /register/company`

This route creates a new company in the database and attemps to send a password reminder email to the submitted email address. The email contains a link to [http://scubawhere.com/&#8203;companypasswordreset?email=`email`&token=`token`](http://scubawhere.com/companypasswordreset). On how to transmit this form, refer to [#Reset a company's password](#Reset_a_company's_password) in the documentation.

- **@param** string  username   The chosen username
- **@param** string  email      The company's contact email adress
- **@param** string  name       The full legal company name
- **@param** string  address_1  The first address line
- **@param** string  address_2  The second address line (optional)
- **@param** string  city       The city the company is based in
- **@param** string  county     The county the company is based in
- **@param** string  postcode   The postcode of the company's address
- **@param** integer region_id  The ID of the corresponding region of the company
- **@param** integer country_id The ID of the corresponding country of the company
- **@param** string  phone      The company's contact telephone number
- **@param** string  website    The company's website (optional)
- &nbsp;
- **@return** JSON   Contains `status` on success, `errors` on failure

### Check if username or email already exists

`GET /register/exists`

- **@param** string field Either `username` or `email`
- **@param** string value The username or email address to check
- &nbsp;
- **@return** integer     `0` (FALSE) if no record was found, otherwise `1` (TRUE)

### Sent a password reminder email

`POST /password/remind`

- **@param** string email The email adress to reset the password for
- &nbsp;
- **@return** JSON        Contains `status` on success, `errors` when the email address can't be found

### Reset a company's password

`POST /password/reset`

This request should be made from the page where the user is linked to in the password reminder email.

- **@param** string email                 The email adress of the user
- **@param** string password              The password
- **@param** string password_confirmation The password confirmation
- **@param** string token                 The token (from the link)
- &nbsp;
- **@return** JSON                        Contains `status` on success, `errors` on failure

## General

All requests from here only allow authenticated users/companies.

### Retrieve CSRF token

`GET /token`

After login, all `POST` requests must contain the `_token` parameter to prevent CSRF (Cross Site Request Forgery).

- **@return** string    The token to be embedded into `POST` requests

## Company

### Retrieve the company's basic information

`GET /company`

- **@return** JSON    The `company` object

## Boats

### Retrieve all accommodations & boats

`GET /company/boats`

- **@return** JSON    An object containing an `accommodations` array and a `boats` array

### Add new, update and delete accommodations & boats

`POST /company/boats`

Submit the whole form with the following structure:

*The IDs 1 & 2 are just examples. Please use the real IDs that are retrieved from the API*

	<input name="accommodations[1][name]" value="My Accommodation">
	<input name="accommodations[1][description]" value="This room is brilliant!">
	<input name="accommodations[2][name]" value="Double Room">
	<input name="accommodations[2][description]" value="A room for two people">

	<input name="boats[1][name]" value="My Boat">
	<input name="boats[1][description]" value="This is my first boat">
	<input name="boats[1][capacity]" value="35">
	<input name="boats[1][photo]" value="JPG_01234.jpg">
	<input name="boats[1][accommodations][1]" value="20"> <!-- This specifies the capacity of the accommodation on this particular boat -->
	<input name="boats[1][accommodations][2]" value="15">
	...

When **creating new accommodations or boats**, simply make their ID a random string, but continue to use this random ID whenever you refer to the entity throughout the form. The random ID is replaced by the real one in the system when the request is submitted.

- **@param** array accommodations An associative array of the structure described above
- **@param** array boats          An associative array of the structure described above
- &nbsp;
- **@return** JSON                Contains `status` on success, `errors` on failure

## Accommodations

### Retrieve all accommodations

`GET /company/accommodations`

- **@return** JSON    An array of `accommodation` objects

## Trips

### Retrieve a specific trip

`GET /api/trip`

- **@param** integer id The ID of the wanted trip
- &nbsp;
- **@return** JSON      A `trip` object

### Retrieve all trips

`GET /api/trip/all`

`GET /company/trips` (deprecated)

- **@return** JSON    An array of `trip` objects, complete with details of the pick-up `location`, all `locations` in the itinary and all connected `triptypes`

### Retrieve all triptypes

`GET /company/triptypes`

- **@return** JSON    An array of `triptypes` objects

### Add a trip

`POST /company/add-trip`

Create a new trip.

`Locations` should be submitted as

    <input name="locations[]" value="1">

`Triptypes` should be submitted as

    <input type="checkbox" name="triptypes[]" value="1">

- **@param** string  name        The name of the trip
- **@param** string  description The description of the trip
- **@param** integer duration    How long the trip takes, in hours
- **@param** integer location_id The ID of the pick-up location for the trip (optional)
- **@param** string  photo       The filename of a photo that represents the trip (optional)
- **@param** string  video       The filename of a video that represents the trip (optional)
- **@param** array   locations   The IDs of the locations in the itinary
- **@param** array   triptypes   The IDs of the triptypes associated with this trip
- &nbsp;
- **@return** JSON               Contains `status` and `id` of the newly created trip on success, `errors` on failure

### Activate a trip

Please refer to [#Create a session](#Create_a_session).

### Edit a trip

`POST /company/edit-trip`

Edit an existing trip's properties. Only the ID and the properties that are changed need to be submitted.

For the structure of the `locations` and `triptypes` arrays, take a look at `Add a trip`.

- **@param** integer id          The ID of the trip to edit
- **@param** string  name        The name of the trip
- **@param** string  description The description of the trip
- **@param** integer duration    How long the trip takes, in hours
- **@param** integer location_id The ID of the pick-up location for the trip
- **@param** string  photo       The filename of a photo that represents the trip
- **@param** string  video       The filename of a video that represents the trip
- **@param** array   locations   The IDs of the locations in the itinary
- **@param** array   triptypes   The IDs of the triptypes associated with this trip
- &nbsp;
- **@return** JSON               Contains `status` on success, `errors` on failure

### Delete a trip

`POST /company/delete-trip`

Delete an existing trip.

- **@param** integer id The ID of the trip to delete
- &nbsp;
- **@return** JSON      Contains `status` on success, `errors` on failure

## Locations

### Retrieve locations 1/2 - Around a center

`GET /company/locations`

Retrieve an arbitrary number of locations, sorted by distance to submitted location (near to far).

> #### Important
> If `area` is set and an array, [#Retrieve locations 2/2 - Inside bounds](#Retrieve_locations_2/2_-_Inside_bounds) is performed instead.

- **@param** float   latitude
- **@param** float   longitude
- **@param** integer limit     The number of locations to retrieve (optional, default: 5)
- &nbsp;
- **@return** JSON             An array of locations, ordered by `distance` (also in the results, in miles)

*The time it takes for the MySQL query to execute is logged.*

### Retrieve locations 2/2 - Inside bounds

`GET /company/locations`

Retrieve *all* locactions inside the specified bounds.

Build your `area` array like so:

	var bounds = map.getBounds(),
		north  = bounds.getNorthEast().lat(),
		west   = bounds.getSouthWest().lng(),
		south  = bounds.getSouthWest().lat(),
		east   = bounds.getNorthEast().lng();

	var area   = [north, west, south, east];

> #### Important
> `limit` has no effect!

- **@param** array area An array containg the bounds of the map (structure shown above)
- &nbsp;
- **@return** JSON      An array of locations (unordered)

### Create a location

`POST /company/add-location`

A newly created location is **not** limited to the creating company. All locations are available to all companies.

- **@param** string name        A name for the location
- **@param** string description A description for the location (optional)
- **@param** float  latitude
- **@param** float  longitude
- **@param** string tags        Tags for the location (optional)
- &nbsp;
- **@return** JSON              Contains `status` and `id` of the newly created location on success, `errors` on failure

## Tickets

### Retrieve a specific ticket

`GET /api/ticket`

- **@param** integer id The ID of the wanted ticket
- &nbsp;
- **@return** JSON      A `ticket` object, with details of connected `boats` and lists of `accommodation_id`s

### Retrieve all tickets

`GET /api/ticket/all`

- **@return** JSON    An array of `ticket` objects, complete with details of connected `boats` and lists of `accommodation_id`s

### Create a ticket

`POST /api/ticket/add`

Creates a new ticket for a trip.

- **@param** integer trip_id     The ID of the `trip` that the ticket belongs to
- **@param** string  name        A name for the ticket
- **@param** string  description A description for the ticket
- **@param** decimal price       The price (will be rounded to two decimals)
- **@param** string  currency    Three letter international code (optional, default: dive center's country's currency)
- **@param** array   trips       A simple array of `trip_id`s that the ticket is eligable for
- **@param** array   boats       An array of `boat_id` => `accommodation_id` associations (can be empty)
- &nbsp;
- **@return** JSON               Contains `status` and `id` of the newly created ticket on success, `errors` on failure

### Update a ticket

`POST /api/ticket/edit`

All parameters are optional (except the ticket `id`).

It is **not** recommended that the `trip_id` be *updated* (although the functionality exits)!

> #### Important
> The response *can* contain an `id` field. If it *does* it means that the ticket could not simply be updated because it has already been booked. Instead the old ticket has now been replaced with an updated ticket in the system. The returned `id` is the new ID of the ticket and must be used from now on!
>
> The following fields will create a new ticket when they are updated and the (old) ticket has already been booked: `trip_id`, `price` and `currency`

- **@param** integer id          The ID of the ticket to edit
- **@param** integer trip_id     The ID of the `trip` that the ticket belongs to
- **@param** string  name        A name for the ticket
- **@param** string  description A description for the ticket
- **@param** decimal price       The price (will be rounded to two decimals)
- **@param** string  currency    The currency that the price is in
- **@param** array   boats       An array of `boat_id` => `accommodation_id` associations (can be empty)
- &nbsp;
- **@return** JSON               Contains `status`, or `status` and `id` of the updated ticket on success, `errors` on failure

### Delete a ticket

`POST /api/ticket/delete`

Delete an existing ticket.

- **@param** integer id The ID of the ticket to delete
- &nbsp;
- **@return** JSON      Contains `status` on success, `errors` on failure

## Sessions

### Retrieve a specific session

`GET /api/session`

- **@param** integer id The ID of the wanted session
- &nbsp;
- **@return** JSON      A `session` object, with integrated `trip` and `boat` objects

### Retrieve all sessions

`GET /api/session/all`

- **@return** JSON    An array of `session` objects (without connected objects)

*To retrieve the related `trip` and `boat` objects, please refer to [#Retrieve a specific trip](#Retrieve_a_specific_trip), [#Retrieve all trips](#Retrieve_all_trips) and [#Retrieve all accommodations & boats](#Retrieve_all_accommodations_&_boats).*

### Create a session

`POST /api/session/add`

*Activates* a trip by giving it a start datetime and an assigned boat.

- **@param** integer trip_id      The ID of the `trip` that the session belongs to
- **@param** string  start        The start datetime of the session in UTC (format: `YYYY-MM-DD HH:mm:ss`)
- **@param** integer boat_id      The ID of the `boat` that carries this session
- &nbsp;
- **@return** JSON                Contains `status` and `id` of the new session on success, `errors` on failure

*The correct `timetable_id` is set automatically on the session from which a new `timetable` is created. For more information about timetables see [#Create a timetable](#Create_a_timetable).*

### Update a session

> #### Important
> This function can have four different failure responses:
>
> - `HTTP 400 Bad Request`    Validation errors of the submitted data.
> - `HTTP 404 Not Found`      The session/departure could not be found.
> - `HTTP 406 Not Acceptable` The new boat's capacity is too small.
> - `HTTP 409 Conflict`       Cannot move session. It has already been booked!

`POST /api/session/edit`

- **@param** integer id      The ID of the `session` to be edited
- **@param** string  start   The start datetime of the session in UTC (format: `YYYY-MM-DD HH:mm:ss`)
- **@param** integer boat_id The ID of the `boat` that carries this session
- &nbsp;
- **@return** JSON           Contains `status` on success, `errors` on failure

### Delete a session

> #### Important
> This function can have two different failure responses:
>
> - `HTTP 404` The session could not be found.
> - `HTTP 409` Cannot delete session. It has already been booked!
>
> In the second case it is recommended to ask the user to [#deactivate the session](#Deactivate_a_session) instead.

- **@param** integer id The ID of the session to delete
- &nbsp;
- **@return** JSON      Contains `status` on success, `errors` on failure

### Deactivate a session

`POST /api/session/deactivate`

Deactivating a session means it can no longer be selected for booking, but is still in the system and the bookings are still valid.

> #### Important
> This function is intended to be used if a session cannot be deleted because it has been booked, to sort out the rebooking of the affected customers while no new bookings can be created.

- **@param** integer id The ID of the session to deactivate
- &nbsp;
- **@return** JSON      Contains `status` on success, `errors` on failure

## Timetables

### Retrieve a specific timetable

`GET /api/timetable`

- **@param** integer id The ID of the wanted timetable
- &nbsp;
- **@return** JSON      A `timetable` object

### Retrieve all timetables

`GET /api/timetable/all`

- **@return** JSON    An array of `timetable` objects

### Create a timetable

`POST /api/timetable/add`

Creates a timetable that clones an existing `session` to all days of the weeks specified in the timetable in the specified rhythm of up to 4 weeks.

The HTML form must submit the following structure:

	<!-- First Week -->
	Mon <input type="checkbox" name="schedule[1][]" value="mon">
	Tue <input type="checkbox" name="schedule[1][]" value="tue">
	Wed <input type="checkbox" name="schedule[1][]" value="wed">
	Thu <input type="checkbox" name="schedule[1][]" value="thu">
	Fri <input type="checkbox" name="schedule[1][]" value="fri">
	Sat <input type="checkbox" name="schedule[1][]" value="sat">
	Sun <input type="checkbox" name="schedule[1][]" value="sun">

	<!-- Second Week -->
	Mon <input type="checkbox" name="schedule[2][]" value="mon">
	Tue <input type="checkbox" name="schedule[2][]" value="tue">
	Wed <input type="checkbox" name="schedule[2][]" value="wed">
	Thu <input type="checkbox" name="schedule[2][]" value="thu">
	Fri <input type="checkbox" name="schedule[2][]" value="fri">
	Sat <input type="checkbox" name="schedule[2][]" value="sat">
	Sun <input type="checkbox" name="schedule[2][]" value="sun">

	<!-- Third Week (if needed) -->
	...

> #### Important
> It is not possible (yet) to retrieve the created sessions directly after they are created with a timetable! It is therefore necessary to retrieve the sessions via [#Retrieve all sessions](#Retrieve_all_sessions) to confirm their creation and correctness.

- **@param** integer session_id The ID of the `session` to be timetabled
- **@param** integer weeks      The number of weeks of the timetable's schedule
- **@param** array   schedule   An 2D array of the structure specified above
- **@param** integer iterations How often the timetable should be repeated (optional, default: 1.5 years worth of iterations)
- &nbsp;
- **@return** JSON              Contains `status` and `id` of the new timetable on success, `errors` on failure

## Agents

### Retrieve a specific agent

`GET /api/agent`

- **@param** integer id The ID of the wanted agent
- &nbsp;
- **@return** JSON      An `agent` object

### Retrieve all agents

`GET /api/agent/all`

- **@return** JSON    An array of `agent` objects

### Create an agent

`POST /api/agent/add`

Creates a new (travel) agent. All fields that are **not** marked *(optional)* are required.

- **@param** string  name            The name of the travel agent
- **@param** string  website         The agent's website (optional)
- **@param** string  branch_name     Name of the agent's local branch
- **@param** text    branch_address  Free textfield for the branch's address
- **@param** string  branch_phone    The branch's telephone contact number (optional)
- **@param** string  branch_email    A contact email address of the branch (optional)
- **@param** text    billing_address If different from `branch_address` specify billing address (optional)
- **@param** string  billing_phone   If different from `branch_phone` specify billing contact number (optional)
- **@param** string  billing_email   If different from `branch_email` specify contact email address (optional)
- **@param** integer commission      The agent's commission in percent as a whole number (e.g. 15%)
- **@param** string  terms           MUST be one of the following three: fullamount, deposit, banned
- &nbsp;
- **@return** JSON                   Contains `status` and `id` of the newly created agent on success, `errors` on failure

### Update an agent

`POST /api/agent/edit`

All parameters are optional (except the agent `id`).

- **@param** integer id              The ID of the agent to be updated
- **@param** string  name            The name of the travel agent
- **@param** string  branch_name     Name of the agent's local branch
- **@param** text    branch_address  Free textfield for the branch's address
- **@param** string  branch_phone    The branch's telephone contact number
- **@param** string  branch_email    A contact email address of the branch
- **@param** text    billing_address If different from `branch_address` specify billing address
- **@param** string  billing_phone   If different from `branch_phone` specify billing contact number
- **@param** string  billing_email   If different from `branch_email` specify contact email address
- **@param** integer commission      The agent's commission in percent as a whole number (e.g. 15%)
- **@param** string  terms           MUST be one of the following three: fullamount, deposit, banned
- &nbsp;
- **@return** JSON                   Contains `status` on success, `errors` on failure

## Customers

> #### Important
> A dive center can only view, access and edit customers that the dive center created itself.

### Retrieve a specific customer

`GET /api/customer`

- **@param** integer id  The ID of the wanted customer
- &nbsp;
- **@return** JSON       A `customer` object

### Retrieve all customers

`GET /api/customer/all`

- **@return** JSON       An array of `customer` objects

### Filter customers by email address

The search functionality has been moved to [#Search for customers by email](#Search_for_customer_by_email).

### Create a customer

`POST /api/customer/add`

Creates a new customer. The only *required* fields are `firstname` and `lastname`.

> #### Important
> Altough nearly all customer fields are optional, a booking does always **need** at least one customer with an email address assigned to it. This is validated when the booking is finalised. (It can also be queried at any time with [#Validate booking](#Validate_booking).)

- **@param** string  email          The email of the customer (optional)
- **@param** string  firstname      The customer's first name
- **@param** string  lastname       The customer's last name
- **@param** string  birthday       Date of birth. Must be in a format understood by the PHP function [strtotime](http://php.net/strtotime). (Example: `24-05-2014`) (optional)
- **@param** integer gender         One of three digits: `1` for male, `2` for female, `3` for other/undefined (optional)
- **@param** string  address_1      First line of customer's address (optional)
- **@param** string  address_2      Second line of customer's address (optional)
- **@param** string  city           The customer's city (optional)
- **@param** string  county         The customer's county (optional)
- **@param** string  postcode       The customer's postcode (optional)
- **@param** integer region_id      The ID of the `region` the customer lives in (optional)
- **@param** integer country_id     The ID of the `country` the customer lives in (optional)
- **@param** string  phone          A contact telephone number (optional)
- **@param** integer certificate_id The ID of the `certificate` that the customer holds (optional)
- **@param** string  last_dive      Date of the customer's last dive. Must be in a format understood by the PHP function [strtotime](http://php.net/strtotime). (Example: `24-05-2014`) (optional)
- &nbsp;
- **@return** JSON                  Contains `status` and `id` of the newly created customer on success, `errors` on failure

### Update a customer

`POST /api/customer/edit`

All parameters are optional (except the customer `id`).

- **@param** string  email          The email of the customer (optional)
- **@param** string  firstname      The customer's first name
- **@param** string  lastname       The customer's last name
- **@param** string  birthday       Date of birth. Must be in a format understood by the PHP function [strtotime](http://php.net/strtotime). (Example: `24-05-2014`) (optional)
- **@param** integer gender         One of three digits: `1` for male, `2` for female, `3` for other/undefined (optional)
- **@param** string  address_1      First line of customer's address (optional)
- **@param** string  address_2      Second line of customer's address (optional)
- **@param** string  city           The customer's city (optional)
- **@param** string  county         The customer's county (optional)
- **@param** string  postcode       The customer's postcode (optional)
- **@param** integer region_id      The ID of the `region` the customer lives in (optional)
- **@param** integer country_id     The ID of the `country` the customer lives in (optional)
- **@param** string  phone          A contact telephone number (optional)
- **@param** integer certificate_id The ID of the `certificate` that the customer holds (optional)
- **@param** string  last_dive      Date of the customer's last dive. Must be in a format understood by the PHP function [strtotime](http://php.net/strtotime). (Example: `24-05-2014`) (optional)
- &nbsp;
- **@return** JSON                  Contains `status` and `id` of the newly created customer on success, `errors` on failure

## Countries

### Recieve all countries

`GET /api/country/all`

Use this API call to populate a country drop-down/select field.

- **@return** JSON  An array of `country` objects

## Agencies & Certificates

### Recieve all agencies and related certificates

`GET /api/agency/all`

Use this API call to populate an agency and (subsequent) certificate drop-down/select field.

- **@return** JSON  An array of `agency` objects with related `certificates` arrays

## Bookings

The ideal booking process is throughoutly documented in a [Facebook Document](https://www.facebook.com/notes/scuba-where/the-booking-process-manifest/498190260280790).

### Retrieve a specific booking

`GET /api/booking`

- **@param** integer id  The ID of the wanted booking
- &nbsp;
- **@return** JSON       A `booking` object

### Retrieve all bookings

`GET /api/booking/all`

- **@return** JSON       An array of `booking` objects

### Start a booking

`POST /api/booking/init`

> #### Important
> When an `agent_id` is supplied, `source` is discarded.

- **@param** integer agent_id The ID of the agent that makes the booking
- **@param** string  source   The source of a non-agent booking (MUST be one of the following: telephone, email, facetoface
- &nbsp;
- **@return** JSON            Contains `status` and `id` & `reference` of the newly created booking on success, `errors` on failure

*Please display the current booking's `reference` number prominently in the interface whenever a booking is made or edited!*

### Attach customer to booking

`POST /api/booking/attach-customer`

Attach an existing customer to a booking. (To create a customer, see [#Create a customer](#Create_a_customer).)

- **@param** integer booking_id  The ID of the `booking` that the customer should be added to (most likeley the ID returned in [#Start a booking](#Start_a_booking)
- **@param** integer customer_id The ID of the customer to attach
- &nbsp;
- **@return** JSON               Contains `status` and a list of attached `customers` on success, `errors` on failure

### Detach customer from booking

`POST /api/booking/detach-customer`

Detach an attached customer from a booking. (To attach a customer, see [#Attach customer to booking](#Attach_customer_to_booking).)

- **@param** integer booking_id  The ID of the `booking` that the customer should be removed from (most likeley the ID returned in [#Start a booking](#Start_a_booking)
- **@param** integer customer_id The ID of the customer to detach
- &nbsp;
- **@return** JSON               Contains `status` and a list of attached `customers` on success, `errors` on failure

### Validate a booking

`GET /api/booking/validate`

Return an array containing boolean values for various important tests.

> #### Available tests
> **customer**: Tests if the lead customer fulfills the requirements (has an email)

- **@param**  integer booking_id The ID of the booking to be checked
- &nbsp;
- **@return** JSON               An array containing the available keys and boolean values

## Search

### Search for customers by email 

`GET /api/search/customers`

This can be used to populate a drop-down list of suggestions when searching for a customer by email address. It returns a set of maximal 10 `customer` objects whos email addresses contain the search string. Results are only returned when the search string is longer than 2 characters (length >= 3).

- **@param** string search  String to be searched for in the available email addresses (min length: 3 characters)
- &nbsp;
- **@return** JSON          An array of `customer` objects

### Search for sessions by filter

All parameters are **optional**.

- **@param** string  after   A date & time interpretable by PHP's [strtotime](http://php.net/strtotime) function (default: now)
- **@param** string  before  A date & time interpretable by PHP's [strtotime](http://php.net/strtotime) function (default: now + 1 month)
- **@param** integer trip_id The ID of the `trip` that the search should be limited to (default: null, meaning *all* trips)
- &nbsp;
- **@return** JSON           An array of `session` objects, complete with the connected `trip`, `trip->tickets` and a `capacity` array (`[used, available]`)


&nbsp;

## Changelog

### 4<sup>th</sup> June 2014
- **@edit** Added `trips` field to parameters for [#Create a ticket](#Create_a_ticket)
- **@edit** Removed regions from [#Countries](#Countries)

### 30<sup>th</sup> May 2014
- **@added** The method [#Update a session](#Update_a_session)

### 29<sup>th</sup> May 2014
- **@edit** Removed `timetable_id` as a parameter for [#Create a session](#Create_a_session)

### 26<sup>th</sup> May 2014
- **@added** New [#Bookings](#Bookings) section
- **@added** New [#Search](#Search) section
- **@added** New [#Validate a booking](#Validate_a_booking) entry
- **@edit**  Moved [#Search for customers by email](#Search_for_customers_by_email) from [#Customers](#Customers) to [#Search](#Search)

### 24<sup>th</sup> May 2014
- **@added** New [#Customers](#Customers) section
- **@added** The [#Countries](#Countries) and [#Agencies & Certificates](#Agencies_&_Certificates) sections

### 21<sup>st</sup> May 2014
- **@added** New [#Agents](#Agents) section
- **@edit**  Clarified responsible fields in *Important* section of [#Update a ticket](#Update_a_ticket)

### 20<sup>th</sup> May 2014
- **@added** New parameter in [#Create a Timetable](#Create_a_timetable): `iterations`

### 11<sup>th</sup> May 2014
- **@edit** Made `location_id` optional in [#Add a Trip](#Add_a_trip)

### 1<sup>st</sup> May 2014
- **@added** The [#Timetables](#Timetables) section

### 24<sup>th</sup> March 2014
- **@added** The [#Sessions](#Sessions) section

### 23<sup>rd</sup> March 2014
- **@added** [#Update a ticket](#Update_a_ticket)
- **@added** [#Delete a ticket](#Delete_a_ticket)
- **@fixed** Removed redundant `id` return field from response in [#Edit a trip](#Edit_a_trip)
- **@edit**  Changed [#Check if username or email already exists](#Check_if_username_or_email_already_exists) from `POST` to `GET`

### 19<sup>th</sup> March 2014
- **@added** The [#Tickets](#Tickets) section

### 14<sup>th</sup> March 2014
- **@added** The method [#Retrieve a specific trip](#Retrieve_a_specific_trip)

### 13<sup>th</sup> March 2014
- **@added** Some new `/api/` calls
- **@edit**  Marked old calls as *(deprecated)*

### 12<sup>th</sup> March 2014
- **@added** Internal links in the [#Changelog](#Changelog)
- **@added** [#Retrieve locations 2/2 - Inside bounds](#Retrieve_locations_2/2_-_Inside_bounds)
- **@edit**  Changed [#Retrieve locations](#Retrieve_locations_1/2_-_Around_a_center) to [#Retrieve locations 1/2 - Around a center](#Retrieve_locations_1/2_-_Around_a_center)
- **@edit**  Changed description of [#Retrieve locations 1/2 - Around a center](#Retrieve_locations_1/2_-_Around_a_center) to reflect the parameter of `area`

### 10<sup>th</sup> March 2014
- **@added** Basic heading numbering
- **@added** Heading links (access on hover)
- **@added** Table of Contents
- **@added** Docs for [#Retrieve all triptypes](#Retrieve_all_triptypes)
- **@fixed** Spelling mistakes
- **@fixed** Missing `locations` array on [#Edit a trip](#Edit_a_trip)
- **@fixed** Missing `triptypes` array on [#Add a trip](#Add_a_trip) and [#Edit a trip](#Edit_a_trip)
- **@edit**  Removed repetition from [#Locations](#Locations)
- **@edit**  Clarifications
- **@edit**  Made [#Retrieve locations](#Retrieve_locations_1/2_-_Around_a_center) use `GET`

### 9<sup>th</sup> March 2014
- **@added** Created the page/documentation

&nbsp;

----------

# The End

- **@author**  Soren Schwert

<a class="to-the-top" href="#Table_of_Contents" title="Go up to table of contents">&uarr;</a>
<script src="assets/zepto.min.js"></script>
<script>
    contents = '';
    $('h2').each(function() {
      $self = $(this);
      name = $self.text().replace(/ /g, '_').replace(/<(.|\n)*?>/g, '');
      $self.attr('id', name);
      contents += '<li><a href="#' + name + '">' + $self.text() + '</a></li>';
      $self.append(' <a href="#' + name + '" title="Link to this section">#</a>');
    });
    $('#contents ol').append(contents);

    $('h3').each(function() {
      $self = $(this);
      name = $self.text().replace(/ /g, '_').replace(/<(.|\n)*?>/g, '');
      $self.attr('id', name);
      $self.append(' <a href="#' + name + '" title="Link to this method">#</a>');
    });
</script>
