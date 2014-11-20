var Booking = {
	//params = int id (the ID of the wanted session)
	get: function(params, handleData) {
		$.get("/api/booking", params).done(handleData);
	},

	getAll: function(handleData) {
		$.get("/api/booking/all").done(handleData);
	},

	/**
	 * Initate a booking with either the 'source' of the booking or the 'agent_id'.
	 * Source must be one of telephone, email, facetoface.
	 *
	 * So 'params' should be an object, either
	 *
	 * {
	 *     _token: ...,
	 *     source: 'telephone'
	 * } (for example)
	 *
	 * or
	 *
	 * {
	 *     _token: ...,
	 *     agent_id: 2
	 * } (also an example)
	 *
	 * @param  {function} handleData [recieves API 'data' as first and only parameter]
	 * @param  {function} errorFn    [recieves xhr object as first parameter.
	 *                                'xhr.responseText' contains the API response in plaintext]
	 *
	 * On success, data.id will contain the bookingID.
	 */
	initiate: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/booking/init",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	/**
	 * Add a ticket to a booking.
	 * Required parameters:
	 * - _token
	 * - booking_id
	 * - customer_id
	 * - is_lead
	 * - ticket_id
	 * - session_id
	 *
	 * Optional parameter:
	 * - package_id
	 *
	 * @param  {function} handleData [recieves API 'data' as first and only parameter]
	 * @param  {function} errorFn    [recieves xhr object as first parameter.
	 *                                'xhr.responseText' contains the API response in plaintext]
	 */
	addDetails: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/booking/add-details",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

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
	removeDetails: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/booking/remove-details",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	editInfo: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/booking/edit-info",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	validateBooking: function(params, handleData, errorFn){
		$.ajax({
			type: "GET",
			url: "/api/booking/validate",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	addAddon: function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/booking/add-addon",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
