var Booking = function(data) {
	if(data !== undefined)
		$.extend(this, data);
};


/*
 ********************************
 ******* STATIC FUNCTIONS *******
 ********************************
 */

/*
 * @param integer id The ID of te required session
 *
 * Takes the required booking's ID and calls the success callback with a Booking object as its only parameter
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
 * @param {object}    Object containing _token and either source or agent_id. Examples:
 * {
 *     _token: ...,
 *     source: 'telephone' (for example)
 * }
 * {
 *     _token: ...,
 *     agent_id: 2 (also an example)
 * }
 *
 * @param  {function} successFn [recieves API 'data.status' as first and only parameter]
 * @param  {function} errorFn   [recieves xhr object as first parameter.
 *                                'xhr.responseText' contains the API response in plaintext]
 */
Booking.prototype.initiate = function(params, successFn, errorFn) {
	$.ajax({
		type: "POST",
		url: "/api/booking/init",
		data: params,
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








// All following has not been adapted yet!








	/**
	 * Add a ticket to a booking.
	 * Required parameters:
	 * - _token
	 * - booking_id
	 * - customer_id
	 * - ticket_id
	 * - session_id
	 *
	 * Optional parameter:
	 * - package_id
	 * - is_lead
	 *
	 * @param  {function} handleData [recieves API 'data' as first and only parameter]
	 * @param  {function} errorFn    [recieves xhr object as first parameter.
	 *                                'xhr.responseText' contains the API response in plaintext]
	 */
Booking.prototype.addDetails = function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/booking/add-details",
			data: params,
			success: handleData,
			error: errorFn
		});
	};

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
