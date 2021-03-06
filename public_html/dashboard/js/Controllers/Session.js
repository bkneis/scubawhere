var Session = {
	//params = int id (the ID of the wanted session)
	getSpecificSession: function(params, handleData) {
		$.get("/api/session", params).done(function(data){
			handleData(data);
		});
	},

	getAllSessions: function(handleData) {
		console.warning('The function Session.getAllSessions() has been deprecated! Please use Session.filter() instead!');

		$.get("/api/session/all").done(function(data){
			handleData(data);
		});
	},

	/**
	 * Filter sessions by certain parameters.
	 *
	 * Optional:
	 * - ticket_id
	 * - package_id
	 * - trip_id
	 * - after      (a datetime (in UTC) of the format 'YYYY-MM-DD hh:mm:ss')
	 * - before     (a datetime (in UTC) of the format 'YYYY-MM-DD hh:mm:ss')
	 * - with_full  (whether or not to include full boats into the result set. Defaul: false)
	 *
	 * @param  {function} handleData [recieves API 'data' as first and only parameter]
	 * @param  {function} errorFn    [recieves xhr object as first parameter.
	 *                                'xhr.responseText' contains the API response in plaintext]
	 */
	filter: function(params, handleData, errorFn) {
		$.ajax({
			type: "GET",
			url: "/api/session/filter",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	getToday: function(handleData, errorFn) {
		$.ajax({
			type: "GET",
			url: "/api/session/today",
			success: handleData,
			error: errorFn
		});
	},

	//Params:
	// @param integer trip_id      The ID of the trip that the session belongs to
	// @param string  start        The start datetime of the session. Must be interpretable by the strtotime PHP function
	// @param integer boat_id      The ID of the boat that carries this session
	// @param integer timetable_id The ID of the related timetable (optional)
	createSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/session/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/session/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deleteSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/session/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deactivateSession: function(params, handleData) {
		$.post("/api/session/deactivate", params, function(data){
			handleData(data);
		});
	},

	restoreSession: function(params, handleData) {
		$.post("/api/session/restore", params, function(data){
			handleData(data);
		});
	},

	getAllCustomers: function(params, handleData, errorFn) {
		$.ajax({
			type: "GET",
			url: "/api/session/manifest",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};
