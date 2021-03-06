var Place = {

	//params need to be as follows:
	//float latitude
	//float longitude
	//int limit
	around: function(params, handleData) {
		$.get("/api/company/locations", params).done(function(data){
			handleData(data);
		});
	},


	// var bounds = map.getBounds(),
	//     north  = bounds.getNorthEast().lat(),
	//     west   = bounds.getSouthWest().lng(),
	//     south  = bounds.getSouthWest().lat(),
	//     east   = bounds.getNorthEast().lng();

	// var area   = [north, west, south, east];

	//gets locations inside a rectangle
	//requires one param - "area", as above
	inside: function(params, handleData){
		$.get("/api/company/locations", params).done(function(data){
			handleData(data);
		});
	},

	tags : function(handleData) {
		$.get("/api/location/tags", handleData);
	},

	//Params:
	// @param string name        A name for the location
	// @param string description A description for the location (optional)
	// @param float  latitude
	// @param float  longitude
	// @param string tags        Tags for the location (optional)

	//Note: all created locations are available to all companies
	create: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/company/add-location",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/location/update",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	attached: function(handleData) {
		$.get("/api/location/all").done(function(data){
			handleData(data);
		});
	},

	attach: function(params, handleData){
		$.post("/api/location/attach", params).done(function(data){
			handleData(data);
		});
	},

	detach : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/location/detach",
			data: params,
			success: handleData,
			error: errorFn
		});
	},
};
