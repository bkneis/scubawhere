var Payment = {

	/**
	 * param = {id: payment_id};
	 */
	get : function(params, handleData) {
		$.get("/api/payment", params, handleData);
	},

	getAll : function(handleData, from, take) {
		if(from === undefined)
			from = '';

		if(take === undefined)
			take = '';
		else
			take = '/' + take;

		$.get("/api/payment/all" + from + take, handleData);
	},

	getAllPaymentgateways : function(handleData) {
		$.get("/api/payment/paymentgateways", handleData);
	}
};
