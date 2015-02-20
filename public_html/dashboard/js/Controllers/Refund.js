var Refund = {

	/**
	 * param = {id: payment_id};
	 */
	get : function(params, handleData) {
		$.get("/api/refund", params, handleData);
	},

	getAll : function(handleData, from, take) {
		if(from === undefined)
			from = '';

		if(take === undefined)
			take = '';
		else
			take = '/' + take;

		$.get("/api/refund/all" + from + take, handleData);
	},

	getAllPaymentgateways : function(handleData) {
		$.get("/api/refund/paymentgateways", handleData);
	}
};
