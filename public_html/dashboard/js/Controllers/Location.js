var Locations = {

	//params need to be as follows:
	//float latitude
	//float longitude
	//int limit
	getLocationsAround: function(params, handleData) {
		$.get("/company/locations", params).done(function(data){
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
	getLocationInside: function(params, handleData){
		$.get("/company/locations", params).done(function(data){
			handleData(data);
		});
	},

	//Params:
	// @param string name        A name for the location
	// @param string description A description for the location (optional)
	// @param float  latitude
	// @param float  longitude
	// @param string tags        Tags for the location (optional)

	//Note: all created locations are available to all companies
	create: function(params, handleData){
		$.post("/company/add-location", params).done(function(data){
			handleData(data);
		});
	},

	getAttachedLocations: function(handleData) {
		$.get("/api/location/all").done(function(data){
			handleData(data);
		});
	},

	attachLocation: function(params, handleData){
		$.post("/api/location/attach", params).done(function(data){
			handleData(data);
		});
	},

	detach: function(params, handleData){
		$.post("/api/location/detach", params).done(function(data){
			handleData(data);
		});
	},
};
var Place = Locations;
