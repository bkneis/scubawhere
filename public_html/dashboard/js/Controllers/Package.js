var Package = {

	getPackage : function(params, handleData) {
		$.get("/api/package", params, function(data) {
			handleData(data);
		});
	},

	getAllPackages : function(handleData) {
		$.get("/api/package/all", function(data){
			handleData(data);
		});
	},

	getAllWithTrashed : function(handleData) {
		$.get("/api/package/all-with-trashed", function(data){
			handleData(data);
		});
	},

	getOnlyAvailable : function(handleData) {
		$.get("/api/package/only-available", function(data){
			handleData(data);
		});
	},

	createPackage : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/package/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updatePackage : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/package/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deletePackage : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/package/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
