
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

	deactivatePackage : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/package/deactivate",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	restorePackage : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/package/restore",
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
