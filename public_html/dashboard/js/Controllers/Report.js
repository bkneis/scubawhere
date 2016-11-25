var Report = {
	
	getPaymentGateways : function(handleData) {
		$.ajax({ url: '/api/payment/paymentgateways', success: handleData });
	},

	getPayments : function(params, handleData) {
		$.ajax({
			url: '/api/payment/filter',
			data: params,
			success: handleData
		});
	},

	getAgentDepositId: function(handleData) {
		$.ajax({
			url: '/api/payment/agent-deposit-id',
			type: 'GET',
			success: handleData
		});
	},

	getRefunds : function(params, handleData) {
		$.ajax({
			url: '/api/refund/filter',
			data: params,
			success: handleData
		});
	},

	getAgentBookings : function(params, handleData) {
		$.ajax({
			url: '/api/booking/filter-confirmed-by-agent',
			data: params,
			success: handleData
		});
	},

	getBookingHistory : function(params, handleData) {
		$.ajax({
			url: '/api/booking/filter-confirmed',
			data: params,
			success: handleData
		});
	},

	getTripUtilisation : function(params, handleData) {
		$.ajax({
			url: '/api/report/utilisation',
			data: params,
			success: handleData
		});
	},

	getClassUtilisation : function(params, handleData) {
		$.ajax({
			url: '/api/report/trainingutilisation',
			data: params,
			success: handleData
		});
	},

	getDemographics : function(params, handleData) {
		$.ajax({
			url: '/api/report/demographics',
			data: params,
			success: handleData
		});
	},

	getPickupSchedule : function(params, handleData) {
		$.ajax({
			url: '/api/company/pick-up-schedule',
			data: params,
			success: handleData
		});
	},

	getTicketsPackages : function(params, handleData) {
		$.ajax({
			url     : '/api/report/revenue-streams',
			data    : params,
			success : handleData
		});
	},

	getCancellations : function (params, handleData) {
		$.ajax({
			url     : '/api/report/cancellations',
			data    : params,
			success : handleData,
			error   : function (xhr) {
				console.log(xhr);
				var res = JSON.parse(xhr.responseText);
				pageMssg(res.errors[0], 'danger');
			}
		});
	},

	getDiscounts : function (params, handleData) {
		$.ajax({
			url     : '/api/report/discounts',
			data    : params,
			success : handleData,
			error   : function (xhr) {
				console.log(xhr);
				var res = JSON.parse(xhr.responseText);
				pageMssg(res.errors[0], 'danger');
			}
		});
	}

};
