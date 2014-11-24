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
	if(this.confirmed) return 'check';
	if(this.reserved)  return 'clock-o';
	if(this.saved)     return 'floppy-o';
	else               return '';
});
Handlebars.registerHelper('statusTooltip', function() {
	if(this.confirmed) return 'Confirmed';
	if(this.reserved)  return 'Reserved until ' + moment(this.reserved).format('MMM Do, HH:mm');
	if(this.saved)     return 'Saved';
	else               return '';
});

Handlebars.registerHelper('paymentIcon', function() {
	if(!this.confirmed) return 'transparent';

	var sum = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0);

	var percentage = sum / this.decimal_price;

	if(percentage === 1) return '#5cb85c';
	else return '#f0ad4e';
});
Handlebars.registerHelper('paymentTooltip', function() {
	if(!this.confirmed) return '';

	var sum = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0);

	var percentage = sum / this.decimal_price;

	if(percentage === 1) return 'Completely paid';
	else                 return window.company.currency.symbol + ' ' + sum.toFixed(2) + '/' + this.decimal_price + ' paid';
});

$(function() {
	var bookingListItem = Handlebars.compile( $('#booking-list-item-template').html() );

	Booking.getAll(function(data) {
		$('#booking-list').html( bookingListItem({bookings: data}) );
	});
});
