Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});
Handlebars.registerHelper('icon', function(source) {
	switch(source) {
		case null: return 'user';
		case 'telephone': return 'phone';
		case 'email': return 'envelope';
		case 'facetoface': return 'eye';
		default: return 'question';
	}
});

$(function() {
	var bookingListItem = Handlebars.compile( $('#booking-list-item-template').html() );

	Booking.getAll(function(data) {
		_.each(data, function(element) {
			element.lead_customer = element.lead_customer[0];
		});
		$('#booking-list').html( bookingListItem({bookings: data}) );
	});
});
