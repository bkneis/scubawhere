var Package = {

	getPackage : function(id, handleData) {
		$.get("/api/package/" + id, function(data) {
			handleData(data);
		});
	},

	getAllPackages : function(handleData) {
		$.get("/api/package", function(data){
			handleData(data);
		});
	},

	getAllWithTrashed : function(handleData) {
		$.get("/api/package", { with_deleted : true }, function(data){
			handleData(data);
		});
	},

	getOnlyAvailable : function(handleData) {
		$.get("/api/package", { only_available : true }, function(data){
			handleData(data);
		});
	},

	createPackage : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/package",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updatePackage : function(id, params, handleData, errorFn) {
		$.ajax({
			type    : "PUT",
			url     : "/api/package/" + id,
			data    : params,
			success : handleData,
			error   : errorFn
		});
	},

	deletePackage : function(id, params, handleData, errorFn){
		$.ajax({
			type    : "DELETE",
			url     : "/api/package/" + id,
			data    : params,
			success : handleData,
			error   : errorFn
		});
	}
};
