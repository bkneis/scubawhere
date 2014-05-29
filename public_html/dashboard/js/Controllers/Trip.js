var Trip = {
	getAllTrips : function (handleData) {
		$.get("/api/trip/all").done(function(data){
			handleData(data);
		});
	},

	getSpecificTrips : function (params, handleData) {
		$.get("/api/trip", params).done(function(data){
			handleData(data);
		});
	},

	getAllTripTypes : function(handleData){
		$.get("/company/triptypes").done(function(data){
			handleData(data);
		});
	}

};
