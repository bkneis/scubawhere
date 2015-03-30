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

	getDemographics : function(params, handleData) {
		$.ajax({
			url: '/api/report/demographics',
			data: params,
			success: handleData
		});
	},

	getPickupSchedule : function(params, handleData) {
		$.ajax({
			url: 'api/company/pick-up-schedule',
			data: params,
			success: handleData
		});
	},

	getTicketsPackages : function(params, handleData) {
		$.ajax({
			url: 'api/report/revenue-streams',
			data: params,
			success: handleData
		});
	}

};