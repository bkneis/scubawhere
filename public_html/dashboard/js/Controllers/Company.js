var Company = {
	getCompany : function(handleData) {
		$.ajax({
			type: "GET",
			async: false,
			url: "/api/company",
			success: handleData,
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
	}
};
