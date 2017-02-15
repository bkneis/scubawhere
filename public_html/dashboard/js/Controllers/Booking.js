var Booking = function(data) {

	// Defaults for new booking
	this.decimal_price  = "0.00";
	this.discount       = "0.00";
	this.discount_reason = '';
	this.commission = 0;
	this.lead_customer  = null;
	this.bookingdetails = [];
	this.accommodations = [];
	this.payments       = [];
	this.refunds        = [];
	this.pick_ups       = [];

	// User interface variables
	this.selectedTickets   = {};
	this.selectedPackages  = {};
	this.selectedCourses   = {};
	this.selectedCustomers = {};
	this.sums              = {};

	this.currentTab        = null;
	this.mode              = 'view';

	if(data !== undefined) {
		$.extend(this, data);

		// Parsing of boolean strings in bookingdetails
		_.each(this.bookingdetails, function(detail) {
			detail.temporary = parseInt(detail.temporary);
		});

		this.setStatus();
	}

	this.calculateSums();
};


/*
 ********************************
 ******* STATIC FUNCTIONS *******
 ********************************
 */

/**
 * Takes the required booking's ID and calls the success callback with a Booking object as its only parameter
 *
 * @param {integer} id The ID of the wanted booking
 * @param {function} successFn Recieves new Booking object as first and only parameter
 */
Booking.get = function(id, successFn) {
	$.get("/api/booking", {id: id}, function(data) {
		var booking = new Booking(data);
		booking.commission = data.commission_amount; // @todo Fix the naming collision of commission and commission_amount
		successFn( booking );
	});
};

/**
 * Takes the required booking's reference and calls the success callback with a Booking object as its only parameter
 *
 * @param {integer} id The ID of the wanted booking
 * @param {function} successFn Recieves new Booking object as first and only parameter
 */
Booking.getByRef = function(reference, successFn, errorFn) {
	$.ajax({
		type    : 'GET',
		url     : '/api/booking',
		data    : { ref: reference },
		success : function(data) {
			successFn(new Booking(data));
		},
		error   : errorFn
	});
};

/**
 * Takes the ID of the booking to edit and calls the success callback with a Booking object (dublicate of the booking to edit) as its only parameter
 *
 * @param {integer} id The ID of the booking to edit
 * @param {function} successFn Recieves new Booking object as first and only parameter
 */
Booking.startEditing = function(id, successFn, errorFn) {
	$.ajax({
		type: "POST",
		url: "/api/booking/start-editing",
		data: {booking_id: id, _token: window.token},
		context: this,
		success: function(data) {
			successFn( new Booking(data) );
		},
		error: errorFn
	});
};

/*
 * Calls success callback with unaltered JSON data
 */
Booking.getAll = function(successFn) {
	$.get("/api/booking/all", successFn);
};

Booking.getRecent = function(successFn, errorFn) {
	$.ajax({
		type: "GET",
		url: "/api/booking/all/0/5",
		success: successFn,
		error: errorFn
	});
};

Booking.today = function(successFn, errorFn) {
	$.ajax({
		type: "GET",
		url: "/api/booking/today",
		success: successFn,
		error: errorFn
	});
};

Booking.tomorrow = function(successFn, errorFn) {
	$.ajax({
		type: "GET",
		url: "/api/booking/tomorrow",
		success: successFn,
		error: errorFn
	});
};

Booking.filter = function(params, successFn, errorFn) {
	$.ajax({
		type: "GET",
		url: "/api/booking/filter",
		data: params,
		success: successFn,
		error: errorFn
	});
};

Booking.getCustomerBookings = function(params, successFn, errorFn) {
	$.ajax({
		type: "GET",
		url: "/api/booking/customerbookings",
		data: params,
		success: successFn,
		error: errorFn
	});
};

Booking.pickUpLocations = function(params, success) {
	$.get("/api/booking/pick-up-locations", params, success);
};

Booking.initiateStorage = function() {
	window.basil = new window.Basil({
		namespace: 'bookings',
		storages: ['local', 'cookie'], // Only use persistent storages
	});
};

/**
 * Cancels a booking.
 * Cancelled bookings DO NOT count towards sessions' utilisation
 *
 * @param  {object} params    Must contain:
 * - _token
 * - booking_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.cancel = function(params, successFn, errorFn) {

	$.ajax({
		type: "POST",
		url: "/api/booking/cancel",
		data: params,
		context: this,
		success: function(data) {
			successFn(data.status);
		},
		error: errorFn
	});
};

Booking.changeRef = function(params, successFn, errorFn) {
	
	$.ajax({
		type: 'POST',
		url: '/api/booking/change-ref',
		data: params,
		success: successFn,
		error: errorFn
	});
	
};

/**
 * The reason this needs to be static and called by setting the this context via the
 * call method is that the handlebars context can only contain the data, not functions,
 * so in a handlebars helper, i cannot use this.generateRemainingBar
 *
 */
Booking.generateRemainingBar = function() {
	var price = parseFloat(this.decimal_price) + parseFloat(this.sums.surcharge);

	if(price === "0.00") return '';

	var sum          = (parseFloat(this.sums.have) + parseFloat(this.sums.surcharge)).toFixed(2);
	var remainingPay = this.sums.payable;

	var percentage   = (parseFloat(this.sums.have) + parseFloat(this.sums.surcharge)) / price;

	console.log(percentage, this.decimal_price);

	if(remainingPay == 0) remainingPay = '';
	else remainingPay = window.company.currency.symbol + ' ' + remainingPay;

	var color = '#f0ad4e'; var bgClasses = 'bg-warning border-warning';
	if(percentage === 0) { color = '#d9534f'; bgClasses = 'bg-danger border-danger'; }
	if(percentage === 1) { color = '#5cb85c'; bgClasses = 'bg-success border-success'; }

	var html = '';
	html += '<div data-id="' + this.id + '" class="percentage-bar-container ' + bgClasses + '">';
	html += '	<div class="percentage-bar" style="background-color: ' + color + '; width: ' + percentage * 100 + '%">&nbsp;</div>';
	html += '   <span class="percentage-payed">' + window.company.currency.symbol + ' ' + sum + '</span>';
	html += '	<span class="percentage-left">' + remainingPay + '</span>';
	html += '</div>';
	html += '<div class="percentage-width-marker"></div>';
	html += '<div class="percentage-total">' + window.company.currency.symbol + ' ' + price.toFixed(2)  + '</div>';

	return html;
};


/*
 ********************************
 ******* PUBLIC FUNCTIONS *******
 ********************************
 */

/**
 * Save UI state to LocalStorage
 */
Booking.prototype.store = function() {
	if(typeof window.basil === 'undefined') Booking.initiateStorage();

	window.basil.set('booking_' + this.id, {
		selectedTickets   : this.selectedTickets,
		selectedCustomers : this.selectedCustomers,
		selectedPackages  : this.selectedPackages,
		selectedCourses   : this.selectedCourses,
		// currentTab        : this.currentTab,
	});

	return true;
};

/**
 * Load UI state from LocalStorage and extend Booking object with it
 */
Booking.prototype.loadStorage = function() {
	if(typeof window.basil === 'undefined') Booking.initiateStorage();

	// $.extend(this, window.basil.get('booking_' + this.id));

	var storedObject = window.basil.get('booking_' + this.id);

	if(storedObject !== null) {
		// Only overwrite these attributes (other attributes could have changed on the server and are thus newer)
		this.selectedTickets   = storedObject.selectedTickets;
		this.selectedCustomers = storedObject.selectedCustomers;
		this.selectedPackages  = storedObject.selectedPackages;
		this.selectedCourses   = storedObject.selectedCourses ;
		// this.currentTab        = storedObject.currentTab;
	}
};

/**
 * Remove saved UI state from LocalStorage for this booking
 */
Booking.prototype.clearStorage = function() {
	if(typeof window.basil === 'undefined') Booking.initiateStorage();

	window.basil.remove('booking_' + this.id);
};

/**
 * Initate a booking with either the 'source' of the booking or the 'agent_id'.
 * Source must be one of (telephone, email, facetoface).
 *
 * @param {object} Object containing _token and either source or agent_id. Examples:
 * - _token
 * - source (telephone, email, facetoface) || agent_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.initiate = function(params, successFn, errorFn) {
	$.ajax({
		type: "POST",
		url: "/api/booking/init",
		data: params,
		context: this,
		success: function(data) {
			this.id        = data.id;
			this.reference = data.reference;
			this.agent     = data.agent || null;

			this.source          = params.source || null;
			this.agent_id        = params.agent_id || null;
			this.agent_reference = params.agent_reference || null;

			this.mode = 'edit';

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Add a ticket/customer/package combo to a booking.
 *
 * @param {object} params      Must contain:
 * - _token
 * - customer_id
 * - ticket_id
 * - session_id
 * - package_id (optional)
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addDetail = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	// Determine whether we need to inject a packagefacade_id into the request
	if(typeof params.packagefacade_id === 'undefined' && typeof params.package_id !== 'undefined') {
		console.warn('WARNING: Potentially unexpected behaviour! - No packagefacade_id submitted. A new packagefacade will be assigned.');

		/*var existingDetail = _.find(this.bookingdetails, function(detail) {
			// First, test the customer_id
			if( detail.customer.id != params.customer_id )
				return false;

			// Next, check if a packagefacade exists
			if( typeof detail.packagefacade === 'undefined' || detail.packagefacade === null )
				return false;

			// Next, check if the packagefacade includes the requested package
			if( detail.packagefacade.package.id == params.package_id)
				return true;
		});
		if(typeof existingDetail !== 'undefined') { // _.find() returns `undefined` if no match is found
			console.info('Existing packagefacade_id detected: ' + existingDetail.packagefacade.id + ' - For package "' + existingDetail.packagefacade.package.name + '"');
			params.packagefacade_id = existingDetail.packagefacade.id;
		}
		else
			console.info('No packagefacade_id detected. Assigning new package.');*/
	}

	$.ajax({
		type: "POST",
		url: "/api/booking/add-detail",
		data: params,
		context: this,
		success: function(data) {
			var detail = {
				id: data.id,
				customer: window.customers[params.customer_id],
				session: params.session_id ? window.sessions[params.session_id] : null,
				ticket: params.ticket_id ? $.extend(true, {}, window.tickets[params.ticket_id]) : null, // Need to clone the ticket object, because we are going to write its decimal_price for the session's date in it
				course: params.course_id ? $.extend(true, {}, window.courses[params.course_id]) : null,
				training: params.training_id ? $.extend(true, {}, window.trainings[params.training_id]) : null,
				training_session: params.training_session_id ? _.omit(window.training_sessions[params.training_session_id], 'training') : null,
				addons: [], // Prepare the addons array to be able to just push to it later
				item_commissionable: data.item_commissionable,
			};

			if(params.package_id) {
				detail.packagefacade = {
					id: data.packagefacade_id,
					package: $.extend(true, {}, window.packages[params.package_id]),
					commissionable: data.item_commissionable
				};

				// Clean up package object
				delete detail.packagefacade.package.accommodations;
				delete detail.packagefacade.package.addons;
				delete detail.packagefacade.package.courses;
				delete detail.packagefacade.package.tickets;
				delete detail.packagefacade.package.base_prices;
				delete detail.packagefacade.package.prices;

				detail.packagefacade.package.decimal_price = data.package_decimal_price;
			}
			else if(params.course_id) {
				detail.course.decimal_price = data.course_decimal_price;
				detail.item_commissionable = data.item_commissionable;
			}
			else {
				detail.ticket.decimal_price = data.ticket_decimal_price;
				detail.item_commissionable = data.item_commissionable;
			}

			if(data.boatroom_id)
				detail.boatroom_id = data.boatroom_id;

			if(params.temporary)
				detail.temporary = params.temporary;

			// Add compulsory addons
			_.each(data.addons, function(addon) {
				addon.pivot = {
					quantity: 1,
				};
				detail.addons.push(addon);
			});

			this.bookingdetails.push(detail);

			// If this is the first detail to be added and there is no lead customer yet, make this customer the lead customer
			if(!this.lead_customer && this.bookingdetails.count === 1) {
				this.lead_customer = window.customers[params.customer_id];
			}

			this.decimal_price = data.decimal_price;

			this.calculateSums();

			successFn(data.status, data.packagefacade_id);
		},
		error: errorFn
	});
};

/**
 * Remove a ticket/customer/package combo from a booking.
 * @param {object} params      Must contain
 * - _token
 * - bookingdetail_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.removeDetail = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/remove-detail",
		data: params,
		context: this,
		success: function(data) {

			var removedDetail = _.find(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			});

			this.bookingdetails = _.reject(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			});

			this.decimal_price = data.decimal_price;

			this.calculateSums();

			successFn(data.status, removedDetail);
		},
		error: errorFn
	});
};

/**
 * Sets the lead_customer_id for this booking
 * @param {object} params      Must contain
 * - _token
 * - customer_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.setLead = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/set-lead",
		data: params,
		context: this,
		success: function(data) {

			if(params.customer_id === null)
				this.lead_customer = false;
			else
				this.lead_customer = window.customers[ params.customer_id ];

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Adds an addon to the booking
 * @param {object} params      Must contain
 * - _token
 * - bookingdetail_id
 * - addon_id
 * - quantity (optional, default: 1)
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addAddon = function(params, successFn, errorFn){

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/add-addon",
		data: params,
		context: this,
		success: function(data) {

			var relatedBookingdetail = _.find(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			});

			// Check if the addon already exists
			var existingAddon = _.find(relatedBookingdetail.addons, function(addon) {
				return addon.id == params.addon_id && addon.pivot.packagefacade_id == (params.packagefacade_id || null);
			});

			if(existingAddon !== undefined) {
				// Increase quantity on existing addon
				existingAddon.pivot.quantity += parseInt(params.quantity);
			}
			else {
				var addon = $.extend(true, {}, window.addons[params.addon_id]);
				addon.decimal_price = data.addon_decimal_price;
				addon.pivot = {
					quantity: parseInt(params.quantity),
					packagefacade_id: params.packagefacade_id || null,
					commissionable: true
				};
				relatedBookingdetail.addons.push( addon );
			}

			this.decimal_price = data.decimal_price;

			if(!params.packagefacade_id)
				this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Removes an addon from the booking
 * @param {object} params      Must contain
 * - _token
 * - bookingdetail_id
 * - addon_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.removeAddon = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/remove-addon",
		data: params,
		context: this,
		success: function(data) {

			var detail = _.find(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			});

			var removedAddon = _.find(detail.addons, function(addon) {
				return addon.id == params.addon_id && addon.pivot.packagefacade_id == (params.packagefacade_id || null);
			});

			if(removedAddon.pivot.quantity > 1) {
				// Reduce quantity by 1
				removedAddon.pivot.quantity--;
			}
			else {
				// Otherwise remove addon from the array
				detail.addons = _.reject(detail.addons, function(addon) {
					return addon.id == params.addon_id && addon.pivot.packagefacade_id == (params.packagefacade_id || null);
				});
			}

			this.decimal_price = data.decimal_price;

			if(removedAddon.pivot.packagefacade_id === null)
				this.calculateSums();

			successFn(data.status, removedAddon);
		},
		error: errorFn
	});
};

/**
 * Adds an accommodation to the booking
 * @param {object} params      Must contain
 * - _token
 * - accommodation_id
 * - customer_id
 * - start            (Date: YYYY-MM-DD)
 * - end              (Date: YYYY-MM-DD)
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addAccommodation = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/add-accommodation",
		data: params,
		context: this,
		success: function(data) {

			var accommodation = $.extend(true, {}, window.accommodations[params.accommodation_id]);
			accommodation.pivot = {
				start: params.start,
				end: params.end,
				customer_id: params.customer_id,
				packagefacade_id: data.packagefacade_id ? data.packagefacade_id : null,
				commissionable: true
			};

			accommodation.customer = window.customers[params.customer_id];

			if(params.package_id)
				accommodation.package = window.packages[params.package_id];

			accommodation.decimal_price = data.accommodation_decimal_price;

			this.accommodations.push( accommodation );

			this.decimal_price = data.decimal_price;

			if(!params.packagefacade_id)
				this.calculateSums();

			successFn(data.status, data.packagefacade_id);
		},
		error: errorFn
	});
};

/**
 * Removes an accommodation from the booking
 * @param {object} params      Must contain
 * - _token
 * - accommodation_id
 * - customer_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.removeAccommodation = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/remove-accommodation",
		data: params,
		context: this,
		success: function(data) {

			var removedAccommodation = _.find(this.accommodations, function(accommodation) {
				return accommodation.id == params.accommodation_id && accommodation.pivot.customer_id == params.customer_id && accommodation.pivot.start === params.start;
			});

			this.accommodations = _.reject(this.accommodations, function(accommodation) {
				return accommodation.id == params.accommodation_id && accommodation.pivot.customer_id == params.customer_id && accommodation.pivot.start === params.start;
			});

			this.decimal_price = data.decimal_price;

			if(removedAccommodation.pivot.packagefacade_id === null)
				this.calculateSums();

			successFn(data.status, removedAccommodation);
		},
		error: errorFn
	});
};

/**
 * Edits additional information about the booking
 * @param  {object} params    Can contain:
 * - _token
 * - discount         {float}  The discount value gets substracted from the final booking price
 * - comment          {text}
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.editInfo = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	console.log(params);

	$.ajax({
		type: "POST",
		url: "/api/booking/edit-info",
		data: params,
		context: this,
		success: function(data) {
			if(params.discount)        this.discount        = params.discount * 100;
			if(params.comment)         this.comment         = params.comment;
			if(params.discount_reason) this.discount_reason = params.discount_reason;

			this.price         = data.price;
			this.decimal_price = data.decimal_price;
			this.commission    = data.commission;

			this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Adds a pick-up location and datetime to the booking
 * @param  {object} params    Can contain:
 * - _token
 * - booking_id
 * - location {string} The location of the pick-up
 * - date     {date}   The date of the pick-up
 * - time     {time}   The time of the pick-up
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addPickUp = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/add-pick-up",
		data: params,
		context: this,
		success: function(data) {
			this.pick_ups.push(data.pick_up);

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Removes a pick-up location from the booking
 * @param  {object} params    Can contain:
 * - _token
 * - booking_id
 * - id           The ID of the pick-up object
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.removePickUp = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/remove-pick-up",
		data: params,
		context: this,
		success: function(data) {
			this.pick_ups = _.reject(this.pick_ups, function(pick_up) {
				return pick_up.id == params.id;
			});

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Reserves the booking until a specified date & time
 * ! Reserved bookings count towards sessions' utilisation !
 *
 * @param  {object} params    Must contain:
 * - _token
 * - reserved_until {string} The number of hours that the booking should be reserved for (from "now")
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.reserve = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/reserve",
		data: params,
		context: this,
		success: function(data) {

			this.reserved_until = data.reserved_until;
			this.status = 'reserved';
			this.setStatus();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Saves the booking so it won't be automatically deleted and can be finished later
 * Saved bookings DO NOT count towards sessions' utilisation
 *
 * @param  {object} params    Must contain:
 * - _token
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.save = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/save",
		data: params,
		context: this,
		success: function(data) {

			this.status = 'saved';
			this.setStatus();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Confirms the booking (only possible for bookings by agent)
 *
 * @param  {object} params    Must contain:
 * - _token
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.confirm = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/confirm",
		data: params,
		context: this,
		success: function(data) {

			this.status = 'confirmed';
			this.setStatus();

			successFn(data);
		},
		error: errorFn
	});
};

/**
 * Apply changes made during the editing to the booking (and parent booking)
 *
 * @param  {object} params    Must contain:
 * - _token
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.applyChanges = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/apply-changes",
		data: params,
		context: this,
		success: function(data) {

			this.reference = data.booking_reference;
			this.status    = data.booking_status;
			this.setStatus();

			this.payments = data.payments;
			this.refunds  = data.refunds;

			this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Cancels the booking.
 * Cancelled bookings DO NOT count towards sessions' utilisation
 *
 * @param  {object} params    Must contain:
 * - _token
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.cancel = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/cancel",
		data: params,
		context: this,
		success: function(data) {

			this.status = 'cancelled';
			this.setStatus();

			this.cancellation_fee = params.cancellation_fee;

			this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Adds a payment to the booking
 *
 * @param  {object} params    Must contain:
 * - _token
 * - amount
 * - paymentgateway_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addPayment = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/payment/add",
		data: params,
		context: this,
		success: function(data) {

			var payment = data.payment;
			payment.paymentgateway = _.find(window.paymentgateways, function(obj) { return obj.id === parseInt(payment.paymentgateway_id); });

			this.payments.push(payment);

			this.status = data.booking_status;
			this.setStatus();

			this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

Booking.prototype.loadPayments = function(successFn, errorFn) {

	var params = {
		booking_id: this.id
	};

	$.ajax({
		type: "POST",
		url: "/api/booking/payments",
		data: params,
		context: this,
		success: function(data) {

			this.payments = data;

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Adds a refund to the booking
 *
 * @param  {object} params    Must contain:
 * - _token
 * - amount
 * - paymentgateway_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addRefund = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/refund/add",
		data: params,
		context: this,
		success: function(data) {

			var refund = data.refund;
			refund.paymentgateway = _.find(window.paymentgateways, function(obj) { return obj.id === parseInt(refund.paymentgateway_id); });
			//refund.paymentgateway = window.paymentgateways[ refund.paymentgateway_id ];

			this.refunds.push(refund);

			this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

Booking.prototype.loadRefunds = function(successFn, errorFn) {

	var params = {
		booking_id: this.id
	};

	$.ajax({
		type: "POST",
		url: "/api/booking/refunds",
		data: params,
		context: this,
		success: function(data) {

			this.refunds = data;

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Validate that all required lead customer fields are provided
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.validate = function(successFn, errorFn){

	var params = {
		booking_id: this.id
	};

	$.ajax({
		type: "GET",
		url: "/api/booking/validate",
		data: params,
		context: this,
		success: function(data) {
			successFn(data.status);
		},
		error: errorFn
	});
};

Booking.prototype.calculateSums = function() {
	this.sums.payed = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0).toFixed(2);

	this.sums.refunded = _.reduce(this.refunds, function(memo, refund) {
		return memo + refund.amount * 1;
	}, 0).toFixed(2);

	this.sums.have = (this.sums.payed - this.sums.refunded).toFixed(2);
	
	this.sums.surcharge = 0;
	for (var i in this.payments) {
		this.sums.surcharge += this.payments[i].surcharge;
	}
	for (var i in this.refunds) {
		this.sums.surcharge += this.refunds[i].surcharge;
	}
	this.sums.surcharge = (this.sums.surcharge / 100).toFixed(2);
	
	this.sums.payable = (this.decimal_price - this.sums.have).toFixed(2);

	this.sums.refundable = (this.sums.have - parseFloat(parseInt(this.cancellation_fee) / 100)).toFixed(2);
};

Booking.prototype.setStatus = function() {

	this.saved = this.reserved = this.confirmed = this.cancelled = false;

	// Set the status attribute to true (needed for Handlebars #if blocks)
	switch(this.status) {
		case 'saved':     this.saved = true;     break;
		case 'reserved':  this.reserved = true;  break;
		case 'confirmed': this.confirmed = true; break;
		case 'cancelled': this.cancelled = true; break;
		default: break;
	}
};

Booking.prototype.checkUnassigned = function() {
	if(_.size(this.selectedTickets)) return true;

	if(_.size(this.selectedCourses) && _.find(this.selectedCourses, function(course) {return course.tickets.length || course.trainings.length})) return true;

	if(_.size(this.selectedPackages) && _.find(this.selectedPackages, function(package) {return package.tickets.length})) return true;
	if(_.size(this.selectedPackages) && _.find(this.selectedPackages, function(package) {return _.size(package.courses) && _.find(package.courses, function(course) {return course.tickets.length || course.trainings.length})})) return true;

	// Next, check for leftover addons or accommodations in selectedPackages
	if(_.size(this.selectedPackages) && _.find(this.selectedPackages, function(package) {return package.addons.length || package.accommodations.length})) return true;

	return false;
};

Booking.prototype.resendConfirmation = function(successFn, errorFn) {
	var params = {
		booking_id     : this.id,
		_token         : window.token
	};

	$.ajax({
		url     : '/api/booking/resend-confirmation',
		type    : 'POST',
		data    : params,
		success : successFn,
		error   : errorFn
	});	
};

Booking.prototype.applyItemCommission = function (params, handleData, errorFn) {
	params._token = window.token;

	var self = this;
	$.ajax({
		url     : '/api/booking/apply-item-commission',
		type    : 'POST',
		data    : params,
		success : function (res) {
			self.commission = res.commission;
			if (params.item_type !== 'accommodation') {
				var detail = _.findWhere(self.bookingdetails, {id: params.bookingdetail_id});
				if (detail === undefined) {
					console.error('WARNING! Unexpected result. The booking detail could not be found to update');
				}
			}
			if (params.item_type === 'addon') {
				var addon = _.findWhere(detail.addons, {id: params.item_id});
				addon.pivot.commissionable = parseInt(res.item_commissionable);
			} else if (params.item_type === 'accommodation') {
				var accommodation = _.findWhere(self.accommodations, {id: params.item_id});
				accommodation.pivot.commissionable = parseInt(res.item_commissionable);
			} else if (params.item_type === 'package') {
				detail.packagefacade.commissionable = parseInt(res.item_commissionable);
			} else {
				detail.item_commissionable = parseInt(res.item_commissionable);
			}
			self.generateSummaries();
			handleData(res, self);
		},
		error: errorFn
	})
};

Booking.prototype.applyItemDiscount = function (params, handleData, errorFn) {
	params._token = window.token;
	
	var self = this;
	$.ajax({
		url     : '/api/booking/apply-item-discount',
		type    : 'POST',
		data    : params,
		success : function (res) {
			self.decimal_price = res.decimal_price;
			if (params.item_type !== 'accommodation') {
				var detail = _.findWhere(self.bookingdetails, {id: params.bookingdetail_id});
				if (detail === undefined) {
					console.error('WARNING! Unexpected result. The booking detail could not be found to update');
				}
			}
            if (params.item_type === 'addon') {
                var addon = _.findWhere(detail.addons, {id: params.item_id});
                addon.pivot.override_price = parseInt(params.price);
            } else if (params.item_type === 'accommodation') {
                var accommodation = _.findWhere(self.accommodations, {id: params.item_id});
                accommodation.pivot.override_price = parseInt(params.price);
            } else if (params.item_type === 'package') {
                detail.packagefacade.override_price = parseInt(params.price);
            } else {
                detail.override_price = parseInt(params.price);
            }
			self.calculateSums();
			self.generateSummaries();
			handleData(res, self);
		},
		error: function (xhr) {
			var errors = (JSON.parse(xhr.responseText)).errors;
			pageMssg(errors[0], 'danger');
			if (typeof errorFn === 'function') {
				errorFn(xhr);
			}
		}
	})
};

Booking.prototype.generateSummaries = function () {

	this.bookingdetails = _.sortBy(this.bookingdetails, function(detail) {
		if(detail.session)
			return detail.session.start;
		else if(detail.training_session)
			return detail.training_session.start;
		else
			return '0'; // Temporary/un-dated sessions should be displayed on top
	});

	// Sort accommodations by start date
	this.accommodations = _.sortBy(this.accommodations, function(accom) {
		return accom.pivot.start;
	});

	// Generate booked items list (for the price table)
	var packagesSummary = {};
	var coursesSummary  = {};
	var ticketsSummary  = [];
	var addonsSummary   = {};

	_.each(this.bookingdetails, function(detail) {
		if(detail.packagefacade) { // This catches NULL and UNDEFINED
			if(!packagesSummary[detail.packagefacade.id]) {
				detail.packagefacade.package.isCommissioned = detail.packagefacade.commissionable;
				packagesSummary[detail.packagefacade.id] = detail.packagefacade.package;
				packagesSummary[detail.packagefacade.id].bookingdetail_id = detail.id;
				packagesSummary[detail.packagefacade.id].facade_id = detail.packagefacade.id;
			}
		}
		else if(detail.course) {
			if(!coursesSummary[detail.customer.id + '-' + detail.course.id]) {
				detail.course.isCommissioned = detail.item_commissionable;
				coursesSummary[detail.customer.id + '-' + detail.course.id] = detail.course;
				coursesSummary[detail.customer.id + '-' + detail.course.id].bookingdetail_id = detail.id;
			}
		}
		else if(detail.ticket) {
			var ticket = detail.ticket;
			ticket.bookingdetail_id = detail.id;
			ticket.isCommissioned = detail.item_commissionable;
			ticketsSummary.push(ticket);
		}

		_.each(detail.addons, function(addon) {
			if(!addon.pivot.packagefacade_id) {
				if(addonsSummary[addon.id])
					addonsSummary[addon.id].qtySummary += parseInt(addon.pivot.quantity);
				else {
					addon.qtySummary = parseInt(addon.pivot.quantity);
					addon.isCommissioned = addon.pivot.commissionable;
					addonsSummary[addon.id] = addon;
					addonsSummary[addon.id].bookingdetail_id = detail.id;
				}
			}
		});
	});

	this.packagesSummary = packagesSummary;
	this.coursesSummary  = coursesSummary;
	this.ticketsSummary  = ticketsSummary;
	this.addonsSummary   = addonsSummary;
};

/*
 ********************************
 ******* PRIVATE FUNCTIONS ******
 ********************************
 */
