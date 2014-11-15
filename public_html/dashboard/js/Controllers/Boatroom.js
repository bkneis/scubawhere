var BoatRoom = {

	get : function(params, handleData) {
		$.get("/api/boatroom", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/boatroom/all", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boatroom/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boatroom/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/boatroom/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
