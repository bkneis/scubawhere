var Booking = function(data) {

	// Defaults for new booking
	this.decimal_price  = "0.00";
	this.lead_customer  = false;
	this.bookingdetails = [];
	this.accommodations = [];
	this.payments       = [];
	this.refunds        = [];

	// User interface variables
	this.currentTab        = null;
	this.selectedTickets   = {};
	this.selectedPackages  = {};
	this.selectedCourses   = {};
	this.selectedCustomers = {};
	this.sums              = {};

	if(data !== undefined) {
		$.extend(this, data);

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
		currentTab        : this.currentTab,
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
		this.currentTab        = storedObject.currentTab;
	}
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
			this.id = data.id;
			this.reference = data.reference;

			this.source = params.source || null;
			this.agent_id = params.agent_id || null;
			this.agent_reference = params.agent_reference || null;

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
		console.warn('WARNING: Potentially unexpected behaviour! - No packagefacade_id submitted. A new package will be assigned.');

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
				training_session: params.training_session_id ? window.training_sessions[params.training_session_id] : null,
				addons: [], // Prepare the addons array to be able to just push to it later
			};

			if(params.package_id) {
				detail.packagefacade = {
					id: data.packagefacade_id,
					package: $.extend(true, {}, window.packages[params.package_id]),
				};
				detail.packagefacade.package.decimal_price = data.package_decimal_price;
			}
			else if(params.course_id) {
				detail.course.decimal_price = data.course_decimal_price;
			}
			else {
				detail.ticket.decimal_price = data.ticket_decimal_price;
			}

			if(data.boatroom_id)
				detail.boatroom_id = data.boatroom_id;

			// Add compulsory addons
			_.each(data.addons, function(id) {
				var addon = $.extend(true, {}, window.addons[id]);
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
			this.arrival_date  = data.arrival_date;

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
			this.bookingdetails = _.reject(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			});

			this.decimal_price = data.decimal_price;
			this.arrival_date  = data.arrival_date;

			this.calculateSums();

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

			var addon = $.extend(true, {}, window.addons[params.addon_id]);
			addon.pivot = {
				quantity: params.quantity,
				packagefacade_id: params.packagefacade_id ? params.packagefacade_id : null,
			};

			_.find(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			}).addons.push( addon );

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

			var removedAddons = _.filter(detail.addons, function(addon) {
				return addon.id == params.addon_id;
			});

			detail.addons = _.reject(detail.addons, function(addon) {
				return addon.id == params.addon_id;
			});

			this.decimal_price = data.decimal_price;

			var notPackaged = _.find(removedAddons, function(addon) {
				return addon.pivot.packagefacade_id === null;
			});
			if(notPackaged !== undefined)
				this.calculateSums();

			successFn(data.status, removedAddons);
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
				customer_id: params.customer_id,
				packagefacade_id: params.packagefacade_id ? params.packagefacade_id : null,
			};

			accommodation.customer = window.customers[params.customer_id];
			accommodation.decimal_price = data.accommodation_decimal_price;

			this.accommodations.push( accommodation );

			this.decimal_price = data.decimal_price;

			if(!params.packagefacade_id)
				this.calculateSums();

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

			var packaged = _.find(this.accommodations, function(accommodation) {
				return accommodation.id == params.accommodation_id && accommodation.pivot.customer_id == params.customer_id;
			}).pivot.packagefacade_id !== null;

			this.accommodations = _.reject(this.accommodations, function(accommodation) {
				return accommodation.id == params.accommodation_id && accommodation.pivot.customer_id == params.customer_id;
			});

			this.decimal_price = data.decimal_price;

			if(!packaged)
				this.calculateSums();

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

			this.calculateSums();

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
			this.status = 'reserved';

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
			payment.paymentgateway = window.paymentgateways[ payment.paymentgateway_id ];

			this.payments.push(payment);

			this.status = 'confirmed';
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
			refund.paymentgateway = window.paymentgateways[ refund.paymentgateway_id ];

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

	this.sums.payable = (this.decimal_price - this.sums.have).toFixed(2);

	this.sums.refundable = (this.sums.have - this.cancellation_fee).toFixed(2);
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
}


/*
 ********************************
 ******* PRIVATE FUNCTIONS ******
 ********************************
 */
