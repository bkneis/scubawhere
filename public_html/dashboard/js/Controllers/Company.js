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
		$.get("/api/notifications", function(data){
			handleData(data);
		});
	},

	sendFeedback : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/company/feedback",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	sendHeartbeat : function(params) {

		params = $.extend({}, params, {
			'route': window.location.hash,
			'_token': window.token
		});

		$.ajax({
			type: "POST",
			url: "/api/company/heartbeat",
			data: params,
			global: false
		});
	},
};
