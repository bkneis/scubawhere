var Trip = {
	getAllTrips : function (handeData) {
		$.get("/api/trip/all").done(function(data){
			handeData(data);
		});
	},

	getSpecificTrips : function (params, handeData) {
		$.get("/api/trip", params).done(function(data){
			handeData(data);
		}); 
	},

	getAllTripTypes : function(handeData){
		$.get("/company/triptypes").done(function(data){
			handeData(data);
		}); 
	}

};