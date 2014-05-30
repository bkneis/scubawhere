var Sessions = {
	//params = int id (the ID of the wanted session)
	getSecificSession: function(params, handleData) {
		$.get("/api/session", params).done(function(data){
			handleData(data);
		});
	},

	getAllSessions: function(handleData) {
		$.get("/api/session/all").done(function(data){
			handleData(data);
		});
	},

	//Params:
	// @param integer trip_id      The ID of the trip that the session belongs to
	// @param string  start        The start datetime of the session. Must be interpretable by the strtotime PHP function
	// @param integer boat_id      The ID of the boat that carries this session
	// @param integer timetable_id The ID of the related timetable (optional)
	createSession: function(params, handleData) {
		$.post("/api/session/add", params, function(data){
			handleData(data);
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

	//params id of session
	deactivateSession: function(params, handleData) {
		$.post("/api/session/deactivate", params, function(data){
			handleData(data);
		});
	}
};
