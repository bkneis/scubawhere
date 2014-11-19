var Boat = {

	get : function(params, handleData) {
		$.get("/api/boat", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/boat/all", handleData);
	},

	getAllWithTrashed : function(handleData) {
		$.get("/api/boat/all-with-trashed", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boat/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boat/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/boat/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
