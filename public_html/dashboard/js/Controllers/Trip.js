var Trip = {
	getAllTrips : function (handleData) {
		$.get("/api/trip/all?" + Math.random()).done(function(data){
			handleData(data);
		});
	},

	getSpecificTrips : function (params, handleData) {
		$.get("/api/trip?" + Math.random(), params).done(function(data){
			handleData(data);
		});
	},

	getAllTripTypes : function(handleData){
		$.get("/company/triptypes?" + Math.random()).done(function(data){
			handleData(data);
		});
	}

};
