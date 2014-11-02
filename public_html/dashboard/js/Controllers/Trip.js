var Trip = {
	getAllTrips : function (handleData) {
		$.get("/api/trip/all").done(function(data){
			handleData(data);
		});
	},
	getAllWithTrashed : function (handleData) {
		$.get("/api/trip/all-with-trashed").done(function(data){
			handleData(data);
		});
	},

	getSpecificTrip : function (params, handleData) {
		$.get("/api/trip", params).done(function(data){
			handleData(data);
		});
	},

	getAllTripTypes : function(handleData){
		$.get("/company/triptypes").done(function(data){
			handleData(data);
		});
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/trip/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/trip/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	/*deactivate : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/trip/deactivate",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	restore : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/trip/restore",
			data: params,
			success: handleData,
			error: errorFn
		});
	},*/

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/trip/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
