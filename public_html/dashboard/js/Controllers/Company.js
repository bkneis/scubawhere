var Company = {
	getCompany : function(handleData) {
		$.ajax({
			type: "GET",
			async: false,
			url: "/api/company",
			success: handleData,
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			url: "/api/company/update",
			type: "POST",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	initialise : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/company/initialise",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	getNotifications : function(handleData) {
		$.get("/api/notifications/all", function(data){
			handleData(data);
		});
	}
};
