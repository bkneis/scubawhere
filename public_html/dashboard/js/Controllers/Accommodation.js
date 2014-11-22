var Accommodation = {

	get : function(params, handleData) {
		$.get("/api/accommodation", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/accommodation/all", handleData);
	},

	getAllWithTrashed : function(handleData) {
		$.get("/api/accommodation/all-with-trashed", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/accommodation/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/accommodation/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deactivate : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/accommodation/deactivate",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	/*
	restore : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/accommodation/restore",
			data: params,
			success: handleData,
			error: errorFn
		});
	},
	*/

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/accommodation/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
