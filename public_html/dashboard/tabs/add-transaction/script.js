var bookingDetailsTemplate;

Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});

Handlebars.registerHelper('status', function() {
	if(this.confirmed == 1)    return new Handlebars.SafeString('<i class="fa fa-check"></i> Confirmed');
	if(this.reserved != null)  return new Handlebars.SafeString('<i class="fa fa-clock-o"></i> Reserved');
	if(this.saved == 1)        return new Handlebars.SafeString('<i class="fa fa-floppy-o"></i> Saved');
	else                       return new Handlebars.SafeString('<i class="fa fa-exclamation-triangle"></i> N/A');
});

Handlebars.registerHelper('sumPayed', function() {
	return _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0).toFixed(2);
});

Handlebars.registerHelper("remainingPay", function() {
	if(this.decimal_price === "0.00") return '';
	var sum = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0);

	var remainingPay = this.decimal_price - sum;
	var percentage   = sum / this.decimal_price;

	remainingPay = remainingPay.toFixed(2);
	if(remainingPay === 0) remainingPay = '';
	else remainingPay = window.company.currency.symbol + ' ' + remainingPay;

	var color = '#f0ad4e'; var bgClass = 'bg-warning';
	if(percentage === 0) { color = '#d9534f'; bgClass = 'bg-danger'; }
	if(percentage === 1) { color = '#5cb85c'; bgClass = 'bg-success'; }

	var html = '';
	html += '<div data-id="' + this.id + '" class="percentage-bar-container ' + bgClass + '">';
	html += '	<div class="percentage-bar" style="background-color: ' + color + '; width: ' + percentage * 100 + '%">&nbsp;</div>';
	html += '	<span class="percentage-left">' + remainingPay + '</span>';
	html += '</div>';

	return new Handlebars.SafeString(html);
});

$(function() {
	if(window.booking === undefined || window.clickedEdit === undefined || window.clickedEdit === false)
		window.location.hash = 'find-booking';

	window.clickedEdit = false;

	bookingDetailsTemplate = Handlebars.compile( $('#booking-details-template').html() );
	$('#booking-details-container').html( bookingDetailsTemplate(window.booking) );
});
