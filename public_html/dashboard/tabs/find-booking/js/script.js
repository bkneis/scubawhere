Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});

Handlebars.registerHelper('sourceIcon', function() {
	switch(this.source) {
		case null:         return 'user';
		case 'telephone':  return 'phone';
		case 'email':      return 'envelope';
		case 'facetoface': return 'eye';
		default:           return 'question';
	}
});
Handlebars.registerHelper('sourceTooltip', function() {
	switch(this.source) {
		case null:         return 'Source: Agent';
		case 'telephone':  return 'Source: Telephone';
		case 'email':      return 'Source: Email';
		case 'facetoface': return 'Source: Face-to-face';
		default:           return 'Source: Not specified';
	}
});

Handlebars.registerHelper('statusIcon', function() {
	if(this.confirmed == 1)    return 'check';
	if(this.reserved != null)  return 'clock-o';
	if(this.saved == 1)        return 'floppy-o';
	else                       return '';
});
Handlebars.registerHelper('statusTooltip', function() {
	if(this.confirmed == 1)    return 'Confirmed';
	if(this.reserved != null)  return 'Reserved until ' + moment(this.reserved).format('MMM Do, HH:mm');
	if(this.saved == 1)        return 'Saved';
	else                       return '';
});

Handlebars.registerHelper('paymentIcon', function() {
	if(this.confirmed === "0" || this.confirmed === 0) return 'transparent';

	var sum = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0);

	var percentage = sum / this.decimal_price;

	if(percentage === 1) return '#5cb85c';
	if(percentage === 0) return '#d9534f';
	else return '#f0ad4e';
});
Handlebars.registerHelper('paymentTooltip', function() {
	if(this.confirmed === "0" || this.confirmed === 0) return '';

	var sum = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0);

	var percentage = sum / this.decimal_price;

	if(percentage === 1) return 'Completely paid';
	else                 return window.company.currency.symbol + ' ' + sum.toFixed(2) + '/' + this.decimal_price + ' paid';
});

Handlebars.registerHelper('sumPayed', function() {
	return _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0).toFixed(2);
});

Handlebars.registerHelper("remainingPay", function() {
	var sum = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0);

	var remainingPay = this.decimal_price - sum;
	var percentage   = sum / this.decimal_price;

	remainingPay = remainingPay.toFixed(2);
	if(remainingPay === 0) remainingPay = '';
	else remainingPay = window.company.currency.symbol + ' ' + remainingPay;

	if(percentage === 0) { color = '#d9534f'; bgClass = 'bg-danger'; }
	if(percentage === 1) { color = '#5cb85c'; bgClass = 'bg-success'; }
	var color = '#f0ad4e'; bgClass = 'bg-warning';

	var html = '';
	html += '<div data-id="' + this.id + '" class="percentage-bar-container ' + bgClass + '">';
	html += '	<div class="percentage-bar" style="background-color: ' + color + '; width: ' + percentage * 100 + '%">&nbsp;</div>';
	html += '	<span class="percentage-left">' + remainingPay + '</span>';
	html += '</div>';

	return new Handlebars.SafeString(html);
});

$(function() {
	var bookingListItem = Handlebars.compile( $('#booking-list-item-template').html() );

	Booking.getAll(function(data) {
		$('#booking-list').html( bookingListItem({bookings: data}) );
	});

	// Prevent click on mailto-link from triggering the accordion
	$('#booking-list').on('click', '.mailto', function(event) {
		event.stopPropagation();
	});

	$('#booking-list').on('click', '.accordion-header', function(event) {
		$(this).toggleClass('expanded');
		$('.accordion-' + this.getAttribute('data-id')).toggle();
	});
});

function editBooking(booking_id, self) {
	// Set loading indicator
	$(self).after('<span id="save-loader" class="loader"></span>');

	// Load booking data and redirect to add-booking tab
	Booking.get(booking_id, function success(object) {
		window.booking     = object;
		window.clickedEdit = true;

		window.location.hash = 'add-booking';
	});
}

function addTransaction(booking_id, self) {
	pageMssg('Patience, my little padawan...', true);
}
