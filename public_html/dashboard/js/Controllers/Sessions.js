var Sessions = {
	//params = int id (the ID of the wanted session)
	getSecificSession : function(params, handeData){
		$.get("/api/session", params).done(function(data){
			handeData(data);
		}); 
	},

	getAllSessions : function(handeData){
		$.get("/api/session/all").done(function(data){
			handeData(data);
		}); 
	},

	//Params:
	// @param integer trip_id      The ID of the trip that the session belongs to
	// @param string  start        The start datetime of the session. Must be interpretable by the strtotime PHP function
	// @param integer boat_id      The ID of the boat that carries this session
	// @param integer timetable_id The ID of the related timetable (optional)
	createSession : function(params, handeData){
		$.post("/api/session/add").done(function(data){
			handeData(data);
		});
	},

	deleteSession : function(){
		//??
	},

	//params id of session
	deactivateSession : function(params, handeData){
		$.post("/api/session/deactivate").done(function(data){
			handeData(data);
		});
	}
};