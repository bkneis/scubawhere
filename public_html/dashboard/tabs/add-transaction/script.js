// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1)
{
	window.location.href = '#dashboard';
}

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
	return this.sums.payed;
});

Handlebars.registerHelper('decimalise', function (num) {
	return new Handlebars.SafeString(parseFloat(parseInt(num) / 100).toFixed(2));
});

Handlebars.registerHelper('sumRefunded', function() {
	return this.sums.refunded;
});
/*Handlebars.registerHelper("remainingPayBar", function() {
	console.log('t', this);
	var price;
	if(this.absolute_price !== null)
	{
		price = this.absolute_price;
	}
	else
	{
		price = this.decimal_price;
	}

	if(this.decimal_price === "0.00") return '';

	var sum          = this.sums.have;
	var remainingPay = this.sums.payable;

	var percentage   = this.sums.have / this.decimal_price;

	if(remainingPay == 0) remainingPay = '';
	else remainingPay = window.company.currency.symbol + ' ' + remainingPay;

	var color = '#f0ad4e'; var bgClasses = 'bg-warning border-warning';
	if(percentage === 0) { color = '#d9534f'; bgClasses = 'bg-danger border-danger'; }
	if(percentage === 1) { color = '#5cb85c'; bgClasses = 'bg-success border-success'; }

	var html = '';
	html += '<div data-id="' + this.id + '" class="percentage-bar-container ' + bgClasses + '">';
	html += '	<div class="percentage-bar" style="background-color: ' + color + '; width: ' + percentage * 100 + '%">&nbsp;</div>';
	html += '   <span class="percentage-payed">' + window.company.currency.symbol + ' ' + sum + '</span>';
	html += '	<span class="percentage-left">' + remainingPay + '</span>';
	html += '</div>';
	html += '<div class="percentage-width-marker"></div>';
	html += '<div class="percentage-total">' + window.company.currency.symbol + ' ' + this.decimal_price  + '</div>';

	return new Handlebars.SafeString(html);
});*/

Handlebars.registerHelper("remainingPayBar", function() {
	var price = this.decimal_price;

	if(price === "0.00") return '';

	var sum          = this.sums.have;
	var remainingPay = this.sums.payable;

	var percentage   = this.sums.have / price;

	if(remainingPay == 0) remainingPay = '';
	else remainingPay = window.company.currency.symbol + ' ' + remainingPay;

	var color = '#f0ad4e'; var bgClasses = 'bg-warning border-warning';
	if(percentage === 0) { color = '#d9534f'; bgClasses = 'bg-danger border-danger'; }
	if(percentage === 1) { color = '#5cb85c'; bgClasses = 'bg-success border-success'; }

	var html = '';
	html += '<div data-id="' + this.id + '" class="percentage-bar-container ' + bgClasses + '">';
	html += '	<div class="percentage-bar" style="background-color: ' + color + '; width: ' + percentage * 100 + '%">&nbsp;</div>';
	html += '   <span class="percentage-payed">' + window.company.currency.symbol + ' ' + sum + '</span>';
	html += '	<span class="percentage-left">' + remainingPay + '</span>';
	html += '</div>';
	html += '<div class="percentage-width-marker"></div>';
	html += '<div class="percentage-total">' + window.company.currency.symbol + ' ' + price  + '</div>';

	return new Handlebars.SafeString(html);
});

Handlebars.registerHelper("remainingPay", function() {
	return this.sums.payable;
});

Handlebars.registerHelper("necessaryRefundFormated", function() {
	if(parseFloat(this.sums.refundable) >= 0) {
		return new Handlebars.SafeString('<strong class="text-danger">' + window.company.currency.symbol + ' ' + this.sums.refundable + '</strong>');
	}
	var refundable = this.sums.refundable.toString();
	return new Handlebars.SafeString('<strong class="text-success">' + window.company.currency.symbol + ' ' + refundable.substring(1) + '</strong>');
});

Handlebars.registerHelper("necessaryRefund", function() {
	return this.sums.refundable;
});

Handlebars.registerHelper('remainingFee', function () {
	if(parseFloat(this.sums.refundable) >= 0) {
		return parseFloat(this.sums.refundable).toFixed(2);
	}
	return parseFloat(this.sums.refundable - (2 * parseFloat(this.sums.refundable))).toFixed(2);
});

Handlebars.registerHelper('necessaryTransaction', function () {
	if(parseFloat(this.sums.refundable) > 0) {
		return new Handlebars.SafeString('Necessary refund');
	}
	return new Handlebars.SafeString('Necessary payment');
});

Handlebars.registerHelper('ifRequiresRefund', function (opts) {
	if(parseFloat(this.sums.refundable) > 0) {
		return opts.fn(this);
	}
	return opts.inverse(this);
});

Handlebars.registerHelper('ifRequiresPayment', function (opts) {
	if(this.status === 'cancelled') {
		if(parseFloat(this.sums.refundable) >= 0) {
			return opts.inverse(this);
		}
	} else {
		if(parseFloat(this.sums.have) >= parseFloat(this.sums.payable)) {
			return opts.inverse(this);
		}
	}
	return opts.fn(this);
});

$(function() {
	if(typeof window.booking === 'undefined' || window.clickedEdit === undefined || window.clickedEdit === false) {
		window.location.hash = 'manage-bookings';
		return;
	}

	window.clickedEdit = false;

	// Check for unassigned items (tickets/classes/packaged addons/packaged accommodations)
	booking.loadStorage();
	if(booking.checkUnassigned()) {
		pageMssg('Some items in this booking are still unassigned. Please assign or remove those items before continuing.', 'warning', true);
		delete booking;

		window.location.href = "#manage-bookings";
		return false;
	}

	bookingDetailsTemplate        = Handlebars.compile( $('#booking-details-template').html() );
	paymentgatewaysSelectTemplate = Handlebars.compile( $('#paymentgateways-select-template').html() );

	$('#booking-details-container').html( bookingDetailsTemplate(booking) );

	Payment.getAllPaymentgateways(function success(data) {
		window.paymentgateways = _.indexBy(data, 'id');
		window.paymentgateways = _.filter(window.paymentgateways, function(obj) { return obj.name !== 'Agent Deposit'; });
		$('.loader').remove();
		$('.paymentgateways-select-container').html( paymentgatewaysSelectTemplate({paymentgateways: window.paymentgateways}) );
	});

	/* $('#received-at-input').val( moment().format('YYYY-MM-DD') );
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
	}); */

	$('#wrapper').on('click', '.see-notes', function (event) {
		event.preventDefault();
		bootbox.alert({
			title   : $(this).attr('data-type') + ' Notes',
			message : $(this).attr('data-notes')
		});
	})

	$('#wrapper').on('submit', '#add-payment-form', function(event) {
		event.preventDefault();

		$('#add-payment-submit').prop('disabled', true);
		$('#add-payment-submit').html('Add Payment <i class="pull-right fa fa-cog fa-spin" style="font-size: 1.3em;"></i>');

		var params = $(this).serializeObject();
		params._token = window.token;

		booking.addPayment(params, function success(status) {
			pageMssg(status, true);
			console.log(booking);
			$('#booking-details-container').html( bookingDetailsTemplate(booking) );
			$('.loader').remove();
			$('.paymentgateways-select-container').html( paymentgatewaysSelectTemplate({paymentgateways: window.paymentgateways}) );
			/* $('#received-at-input').val( moment().format('YYYY-MM-DD') );
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
			}); */
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			_.each(data.errors, function(error) {
				$('#add-payment-panel').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><i class="fa fa-exclamation-circle"></i> ' + error + '</div>');
			});

			$('#add-payment-submit').prop('disabled', false);
			$('#add-payment-submit').html('Add Payment');
		});
	});

	$('#wrapper').on('submit', '#add-refund-form', function(event) {
		event.preventDefault();

		$('#add-refund-submit').prop('disabled', true);
		$('#add-refund-submit').html('Add Refund <i class="pull-right fa fa-cog fa-spin" style="font-size: 1.3em;"></i>');

		var params = $(this).serializeObject();
		params._token = window.token;

		booking.addRefund(params, function success(status) {
			pageMssg(status, true);
			$('#booking-details-container').html( bookingDetailsTemplate(booking) );
			$('.loader').remove();
			$('.paymentgateways-select-container').html( paymentgatewaysSelectTemplate({paymentgateways: window.paymentgateways}) );
			/* $('#received-at-input').val( moment().format('YYYY-MM-DD') );
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
			}); */
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			_.each(data.errors, function(error) {
				$('#add-refund-panel').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><i class="fa fa-exclamation-circle"></i> ' + error + '</div>');
			});

			$('#add-refund-submit').prop('disabled', false);
			$('#add-refund-submit').html('Add Refund');
		});
	});

	$('#wrapper').on('click', '#view-summary', function() {
		// the window.booking object already exists
		window.clickedEdit = true;

		window.location.hash = 'add-booking';
	});

});
