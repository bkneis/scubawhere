var Addon = {

	getAddon : function(params, handleData) {
		$.get("/api/addon", params, function(data) {
			handleData(data);
		});
	},

	getAllAddons : function(handleData) {
		$.get("/api/addon/all", function(data){

			if(window.location.hostname === 'rms-test.scubawhere.com')
				_.each(data, function(object) {
					object.compulsory = parseInt(object.compulsory);
				});

			handleData(data);
		});
	},

	createAddon : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/addon/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateAddon : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/addon/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deleteAddon : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/addon/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
