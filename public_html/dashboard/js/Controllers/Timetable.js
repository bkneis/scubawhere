var Timetable = {
	//params = int id (the ID of the wanted session)
	/*
	getSecificSession: function(params, handleData) {
		$.get("/api/session?" + Math.random(), params).done(function(data){
			handleData(data);
		});
	},

	getAllSessions: function(handleData) {
		$.get("/api/session/all?" + Math.random()).done(function(data){
			handleData(data);
		});
	},
	*/

	//Params:
	// @param integer trip_id      The ID of the trip that the session belongs to
	// @param string  start        The start datetime of the session. Must be interpretable by the strtotime PHP function
	// @param integer boat_id      The ID of the boat that carries this session
	// @param integer timetable_id The ID of the related timetable (optional)
	createTimetable: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/timetable/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	createClassTimetable: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/schedule/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},
};
