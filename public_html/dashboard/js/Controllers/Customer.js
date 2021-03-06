
var Customer = {

	getCustomer : function(params, handleData) {
		$.get("/api/customer", params, function(data) {
			handleData(data);
		});
	},

	getAllCustomers : function(handleData) {
		$.get("/api/customer/all", function(data){
			handleData(data);
		});
	},

	createCustomer : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/customer/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	filter : function(params, handleData, errorFn) {
		$.ajax({
			type: "GET",
			url: "/api/customer/filter",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateCustomer : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/customer/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},
	
	delete : function (id, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/customer/delete",
			data: { id: id, _token: window.token },
			success: handleData,
			error: errorFn
		});
	},

	importCSV : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/customer/importcsv",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	getLastImportFileUrl : function(handleData) {
		$.ajax({
			type    : 'GET',
			url     : '/api/customer/last-import-errors',
			success : handleData
		});
	},

	addStay : function (params, handleData, errorFn) {
		params._token = window.token;
		$.ajax({
			type    : 'POST',
			url     : '/api/customer/add-stay',
			data    : params,
			success : handleData,
			error   : errorFn
		});
	},

	removeStay : function (params, handleData, errorFn) {
		params._token = window.token;
		$.ajax({
			type    : 'POST',
			url     : '/api/customer/remove-stay',
			data    : params,
			success : handleData,
			error   : errorFn
		});
	}

};
