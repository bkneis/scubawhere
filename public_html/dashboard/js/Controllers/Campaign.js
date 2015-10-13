var Campaign = {

	get : function(params, handleData) {
		$.get("/api/campaign", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/campaign/all", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/campaign/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};