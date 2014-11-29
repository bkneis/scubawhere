var Booking = function(data) {

	if(data !== undefined) {
		$.extend(this, data);
	}
	else {
		this.bookingdetails = [];
		this.accommodations = [];
		this.payments       = [];
		this.lead_customer  = false;
	}

	this.selectedCustomers = {};
	this.selectedTickets   = {};
};


/*
 ********************************
 ******* STATIC FUNCTIONS *******
 ********************************
 */

/**
 * Takes the required booking's ID and calls the success callback with a Booking object as its only parameter
 *
 * @param {integer} id The ID of te required session
 * @param {function} successFn Recieves new Booking object as first and only parameter
 */
Booking.get = function(id, successFn) {
	$.get("/api/booking", {id: id}, function(data) {
		successFn( new Booking(data) );
	});
};

/*
 * Calls success callback with unaltered JSON data
 */
Booking.getAll = function(success) {
	$.get("/api/booking/all", success);
};

Booking.pickUpLocations = function(params, success) {
	$.get("/api/booking/pick-up-locations", params, success);
};


/*
 ********************************
 ******* PUBLIC FUNCTIONS *******
 ********************************
 */

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
			this.id = data.id;
			this.reference = data.reference;

			this.source = params.source || null;
			this.agent_id = params.agent_id || null;

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
	var existingDetail = !params.package_id || _.find(this.bookingdetails, function(detail) {
		// First, test the customer_id
		if( detail.customer.id != params.customer_id )
			return false;

		// Next, check if packagefacade exist
		if( detail.packagefacade === undefined )
			return false;

		// Next, check if the packagefacade includes the requested package
		if( detail.packagefacade.package.id == params.package_id)
			return true;
	});
	if( params.package_id && existingDetail !== undefined )
		params.packagefacade_id = existingDetail.packagefacade.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/add-detail",
		data: params,
		context: this,
		success: function(data) {
			var detail = {
				id: data.id,
				customer: window.customers[params.customer_id],
				session: window.sessions[params.session_id],
				ticket: $.extend(true, {}, window.tickets[params.ticket_id]), // Need to clone the ticket object, because we are going to write its decimal_price for the session's date in it
				addons: [] // Prepare the addons array to be able to just push to it later
			};

			detail.ticket.decimal_price = data.ticket_decimal_price;

			if(params.package_id) {
				detail.packagefacade = {
					id: data.packagefacade_id,
					package: window.packages[params.package_id]
				};
			}

			this.bookingdetails.push(detail);

			// If this is the first detail to be added and there is no lead customer yet, make this customer the lead customer
			if(!this.lead_customer && this.bookingdetails.count === 1) {
				this.lead_customer = window.customers[params.customer_id];
			}

			this.decimal_price = data.decimal_price;

			successFn(data.status);
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
			this.bookingdetails = _.reject(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id
			});

			this.decimal_price = data.decimal_price;

			successFn(data.status);
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

			var addon = window.addons[params.addon_id];
			addon.pivot = {
				quantity: params.quantity
			};

			_.find(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			}).addons.push( addon );

			this.decimal_price = data.decimal_price;

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

			detail.addons = _.reject(detail.addons, function(addon) {
				return addon.id == params.addon_id;
			});

			this.decimal_price = data.decimal_price;

			successFn(data.status);
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

			var accommodation = window.accommodations[params.accommodation_id];
			accommodation.pivot = {
				start: params.start,
				end: params.end,
				customer_id: params.customer_id
			};

			accommodation.customer = window.customers[params.customer_id];
			accommodation.decimal_price = data.accommodation_decimal_price;

			this.accommodations.push( accommodation );

			this.decimal_price = data.decimal_price;

			successFn(data.status);
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

			this.accommodations = _.reject(this.accommodations, function(accommodation) {
				return accommodation.id == params.accommodation_id && accommodation.pivot.customer_id == params.customer_id;
			});

			this.decimal_price = data.decimal_price;

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Edits additional information about the booking
 * @param  {object} params    Can contain:
 * - _token
 * - pick_up_location {string}
 * - pick_up_time     {string} Must be formatted as 'YYYY-MM-DD HH:mm:ss'
 * - discount         {float}  The discount value gets substracted from the final booking price
 * - comment          {text}
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.editInfo = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/edit-info",
		data: params,
		context: this,
		success: function(data) {

			if(params.pick_up_location) this.pick_up_location = params.pick_up_location;
			if(params.pick_up_time)     this.pick_up_time     = params.pick_up_time;
			if(params.discount)         this.discount         = params.discount;
			if(params.comment)          this.comment          = params.comment;

			this.decimal_price = data.decimal_price;

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
 * - reserved {string} The datetime until the booking should be reserved, in 'YYYY-MM-DD HH:MM:SS' format
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

			this.reserved = params.reserved;

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

			this.saved = 1;

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
Booking.prototype.addPayment = function(params, succesFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/payment/add",
		data: params,
		context: this,
		success: function(data) {

			var payment = {
				amount: params.amount,
				paymentgateway: window.paymentgateways[params.paymentgateway_id],
				currency: window.company.currency
			};

			this.payments.push(payment);

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



/*
 ********************************
 ******* PRIVATE FUNCTIONS ******
 ********************************
 */


