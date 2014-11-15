var Boats = {

	get : function(params, handleData) {
		$.get("/api/boats", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/boats/all", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boats/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boats/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/boats/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
