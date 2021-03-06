var Addon = {

	getAddon : function(params, handleData) {
		$.get("/api/addon/" + params.id, function(res) {
			handleData(res.data);
		});
	},

	getAllAddons : function(handleData) {
		$.get("/api/addon", function(res){

			if(window.location.hostname === 'rms-test.scubawhere.com')
				_.each(data, function(object) {
					object.compulsory = parseInt(object.compulsory);
				});

			handleData(res.data);
		});
	},

	createAddon : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/addon",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateAddon : function(id, params, handleData, errorFn) {
		$.ajax({
			type: "PUT",
			url: "/api/addon/" + id,
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deleteAddon : function(params, handleData, errorFn){
		$.ajax({
			type: "DELETE",
			url: "/api/addon/" + params.id,
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
