var Boat = {
	//No params required
	getAllBoats : function (handeData) {
		$.get("/company/boats?" + Math.random()).done(function(data){
			handeData(data);
		});
	},

	//this will create, edit or delete accomodations and boats
	//param needs to be a serialised form - check docs for format
	setBoat : function (form, handeData) {
		$.post("/company/boats", form).done(function(data){
			handeData(data);
		});
	},

	//get all accommodations - no params
	getAccommodations : function (handeData) {
		$.get("/company/boats?" + Math.random()).done(function(data){
			handeData(data);
		});
	}
};
