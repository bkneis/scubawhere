var Accommodation = {

	get : function(params, handleData) {
		$.get("/api/accommodation/" + params.id, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/accommodation", handleData);
	},

	getAllWithTrashed : function(handleData) {
		var param = {
			with_deleted : true
		};
		$.get("/api/accommodation", param, handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/accommodation",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	// @todo change the params[7] to dynamically get the ID
	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "PUT",
			url: "/api/accommodation/" + params[params.length - 2].value,
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "DELETE",
			url: "/api/accommodation/" + params.id,
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	filter : function(params, handleData, errorFn){
		$.ajax({
			type: "GET",
			url: "/api/accommodation",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	getManifest : function(params, handleData, errorFn) {
		params.type = 'accommodation';
		$.ajax({
			type: 'GET',
			url : '/api/manifest',
			data : params,
			success : handleData,
			error : errorFn
		});
	},

	getAvailability : function(params, handleData, errorFn) {
		$.ajax({
			type    : 'GET',
			url     : '/api/accommodation/availability',
			data    : params,
			success : handleData,
			error   : errorFn
		});
	}
};
