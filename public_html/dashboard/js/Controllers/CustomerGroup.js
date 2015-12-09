var CustomerGroup = {

	get : function(params, handleData) {
		$.get("/api/customer-group", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/customer-group/all", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/customer-group/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params,handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/customer-group/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/customer-group/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	},
    
    getCustomers: function(params, handleData) {
        $.get("/api/customer-group/customers", params, handleData);
    },
    
    getCustomerAnalysis: function(params, handleData) {
        $.ajax({
			type: "GET",
			url: "/api/customer-group/customeranalytics",
			data: params,
			success: handleData
		});
    }

};