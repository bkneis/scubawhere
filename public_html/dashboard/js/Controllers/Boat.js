var Boat = {
	//No params required
	getAllBoats : function (handleData) {
		$.get("/company/boats").done(function(data){
			handleData(data);
		});
	},

	//this will create, edit or delete accomodations and boats
	//param needs to be a serialised form - check docs for format
	setBoat : function (form, handleData) {
		$.post("/company/boats", form).done(function(data){
			handleData(data);
		});
	},

	//get all accommodations - no params
	getAccommodations : function (handleData) {
		$.get("/company/boats").done(function(data){
			handleData(data);
		});
	}
};
