var Booking = function(data) {
	if(data !== undefined)
		$.extend(this, data);

	this.bookingdetails = [];
	this.accommodations = [];
};


/*
 ********************************
 ******* STATIC FUNCTIONS *******
 ********************************
 */

/**
 * Takes the required booking's ID and calls the success callback with a Booking object as its only parameter
 *
 * @param  {integer} id The ID of te required session
 */
Booking.get = function(id, success) {
	$.get("/api/booking", {id: id}, function(data) {
		success( new Booking(data) );
	});
};

/*
 * Calls success callback with unaltered JSON data
 */
Booking.getAll = function(success) {
	$.get("/api/booking/all", success);
};


/*
 ********************************
 ******* OBJECT FUNCTIONS *******
 ********************************
 */

/**
 * Initate a booking with either the 'source' of the booking or the 'agent_id'.
 * Source must be one of (telephone, email, facetoface).
 *
 * @param {object} Object containing _token and either source or agent_id. Examples:
 * {
 *     _token: ...,
 *     source: 'telephone' (for example)
 * }
 * {
 *     _token: ...,
 *     agent_id: 2 (also an example)
 * }
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
 * @param {object} params The parameters, as described here:
 * Required parameters:
 * - _token
 * - customer_id
 * - ticket_id
 * - session_id
 *
 * Optional parameter:
 * - package_id
 * - is_lead
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
				customer: window.customers[params.customer_id],
				is_lead: params.is_lead || false,
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

			this.decimal_price = data.decimal_price;

			successFn(data.status);
		},
		error: errorFn
	});
};








// All following has not been adapted yet!










	/**
	 * Remove a ticket from a booking.
	 * Required parameters:
	 * - _token
	 * - booking_id
	 * - customer_id
	 * - session_id
	 *
	 * A booking details record should be uniquely identifyable by the combination of customer_id and session_id (because one customer can't really book one session twice).
	 *
	 * @param  {function} handleData [recieves API 'data' as first and only parameter]
	 * @param  {function} errorFn    [recieves xhr object as first parameter.
	 *                                'xhr.responseText' contains the API response in plaintext]
	 */
Booking.prototype.removeDetails = function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/booking/remove-details",
			data: params,
			success: handleData,
			error: errorFn
		});
	};

Booking.prototype.editInfo = function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/booking/edit-info",
			data: params,
			success: handleData,
			error: errorFn
		});
	};

Booking.prototype.validateBooking = function(params, handleData, errorFn){
		$.ajax({
			type: "GET",
			url: "/api/booking/validate",
			data: params,
			success: handleData,
			error: errorFn
		});
	};

Booking.prototype.addAddon = function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/booking/add-addon",
			data: params,
			success: handleData,
			error: errorFn
		});
	};
