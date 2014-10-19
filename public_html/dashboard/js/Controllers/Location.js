var Locations = {

	//params need to be as follows:
	//float latitude
	//float longitude
	//int limit
	getLocationAround : function(params, handleData) {
		$.get("/company/locations", params).done(function(data){
			handeData(data);
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
	getLocationInside : function(params, handleData){
		$.get("/company/locations", params).done(function(data){
			handeData(data);
		});
	},

	//Params:
	// @param string name        A name for the location
	// @param string description A description for the location (optional)
	// @param float  latitude
	// @param float  longitude
	// @param string tags        Tags for the location (optional)

	//Note: all created locations are available to all companies
	createLocation : function(params, handleData){
		$.post("/company/add-location", params).done(function(data){
			handeData(data);
		});
	},

	getAttachedLocations : function(handleData) {
		$.get("/location/all").done(function(data){
			handeData(data);
		});
	}
};
