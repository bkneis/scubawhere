
var Package = {

	getPackage : function(params, handleData) {
		$.get("/api/package", function(data) {
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

	/*
	deactivatePackage : function(params, handleData){
		$.post("/api/package/deactivate", params, function(data){
			handleData(data);
		});
	}
	*/

	/*
	deletePackage : function(params, handleData){
		$.post("/api/package/delete", params, function(data){
			handleData(data);
		});
	}
	*/
};
