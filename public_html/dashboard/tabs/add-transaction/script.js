var bookingDetailsTemplate,
    paymentgatewaysSelectTemplate;

Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});

Handlebars.registerHelper('status', function() {
	switch(this.status) {
		case 'cancelled': return new Handlebars.SafeString('<i class="fa fa-ban"></i> Cancelled');
		case 'confirmed': return new Handlebars.SafeString('<i class="fa fa-check"></i> Confirmed');
		case 'reserved':  return new Handlebars.SafeString('<i class="fa fa-clock-o"></i> Reserved');
		case 'saved':     return new Handlebars.SafeString('<i class="fa fa-floppy-o"></i> Saved');
		default:          return new Handlebars.SafeString('<i class="fa fa-exclamation-triangle"></i> N/A');
	}
});

Handlebars.registerHelper('sumPayed', function() {
	return _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0).toFixed(2);
});

Handlebars.registerHelper("remainingPayBar", function() {
	if(this.decimal_price === "0.00") return '';
	var sum = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0);

	var remainingPay = this.decimal_price - sum;
	var percentage   = sum / this.decimal_price;

	remainingPay = remainingPay.toFixed(2);
	if(remainingPay === 0) remainingPay = '';
	else remainingPay = window.company.currency.symbol + ' ' + remainingPay;

	var color = '#f0ad4e'; var bgClasses = 'bg-warning border-warning';
	if(percentage === 0) { color = '#d9534f'; bgClasses = 'bg-danger border-danger'; }
	if(percentage === 1) { color = '#5cb85c'; bgClasses = 'bg-success border-success'; }

	var html = '';
	html += '<div data-id="' + this.id + '" class="percentage-bar-container ' + bgClasses + '">';
	html += '	<div class="percentage-bar" style="background-color: ' + color + '; width: ' + percentage * 100 + '%">&nbsp;</div>';
	html += '	<span class="percentage-left">' + remainingPay + '</span>';
	html += '</div>';

	return new Handlebars.SafeString(html);
});

Handlebars.registerHelper("remainingPay", function() {
	if(this.decimal_price === "0.00") return '';
	var sum = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0);

	return (this.decimal_price - sum).toFixed(2);
});

$(function() {
	if(typeof window.booking === 'undefined' || window.clickedEdit === undefined || window.clickedEdit === false) {
		window.location.hash = 'manage-bookings';
		return;
	}

	window.clickedEdit = false;

	bookingDetailsTemplate        = Handlebars.compile( $('#booking-details-template').html() );
	paymentgatewaysSelectTemplate = Handlebars.compile( $('#paymentgateways-select-template').html() );

	$('#booking-details-container').html( bookingDetailsTemplate(booking) );

	Payment.getAllPaymentgateways(function success(data) {
		window.paymentgateways = _.indexBy(data, 'id');
		$('.loader').remove();
		$('#paymentgateways-select-container').html( paymentgatewaysSelectTemplate({paymentgateways: window.paymentgateways}) );
	});

	$('#received-at-input').val( moment().format('YYYY-MM-DD') );
	$('#received-at-input').datetimepicker({
		pickDate: true,
		pickTime: false,
		maxDate: moment(),
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});

	$('#wrapper').on('submit', '#add-transaction-form', function(event) {
		event.preventDefault();

		$('#add-transaction-submit').prop('disabled', true);
		$('#add-transaction-submit').html('Add Transaction <i class="pull-right fa fa-cog fa-spin" style="font-size: 1.3em;"></i>');

		var params = $(this).serializeObject();
		params._token = window.token;

		booking.addPayment(params, function success(status) {
			pageMssg(status, true);
			$('#booking-details-container').html( bookingDetailsTemplate(booking) );
			$('.loader').remove();
			$('#paymentgateways-select-container').html( paymentgatewaysSelectTemplate({paymentgateways: window.paymentgateways}) );
			$('#received-at-input').val( moment().format('YYYY-MM-DD') );
			$('#received-at-input').datetimepicker({
				pickDate: true,
				pickTime: false,
				maxDate: moment(),
				icons: {
					time: 'fa fa-clock-o',
					date: 'fa fa-calendar',
					up:   'fa fa-chevron-up',
					down: 'fa fa-chevron-down'
				},
			});
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			_.each(data.errors, function(error) {
				$('#add-transaction-panel').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><i class="fa fa-exclamation-circle"></i> ' + error + '</div>');
			});

			$('#add-transaction-submit').prop('disabled', false);
			$('#add-transaction-submit').html('Add Transaction');
		});
	});
});
