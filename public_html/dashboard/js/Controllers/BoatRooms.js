var BoatRooms = {

	get : function(params, handleData) {
		$.get("/api/boatrooms", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/boatrooms/all", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boatrooms/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boatrooms/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/boatrooms/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
