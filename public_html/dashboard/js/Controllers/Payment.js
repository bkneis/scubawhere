var Payment = {

	/**
	 * param = {id: payment_id};
	 */
	get : function(params, handleData) {
		$.get("/api/payment", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/payment/all", handleData);
	},

	getAllPaymentgateways : function(handleData) {
		$.get("/api/payment/paymentgateways", handleData);
	}
};
