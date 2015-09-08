var Trip = {
	getAllTrips : function (handleData) {
		$.get("/api/trip/all").done(function(data){

			if(window.location.hostname === 'rms-test.scubawhere.com')
				_.each(data, function(object) {
					object.boat_required = parseInt(object.boat_required);
				});

			handleData(data);
		});
	},
	getAllWithTrashed : function (handleData) {
		$.get("/api/trip/all-with-trashed").done(function(data){

			if(window.location.hostname === 'rms-test.scubawhere.com')
				_.each(data, function(object) {
					object.boat_required = parseInt(object.boat_required);
				});

			handleData(data);
		});
	},

	getSpecificTrip : function (params, handleData) {
		$.get("/api/trip", params).done(function(data){
			handleData(data);
		});
	},

	tags : function(handleData) {
		$.get("/api/trip/tags", handleData);
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
