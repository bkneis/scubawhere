Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});

Handlebars.registerHelper('sourceIcon', function() {
	var icon = '',
	    tooltip = '';

	switch(this.source) {
		case null:         icon = 'fa-user';     tooltip = 'Source: Agent';         break;
		case 'telephone':  icon = 'fa-phone';    tooltip = 'Source: Telephone';     break;
		case 'email':      icon = 'fa-envelope'; tooltip = 'Source: Email';         break;
		case 'facetoface': icon = 'fa-eye';      tooltip = 'Source: Face-to-face';  break;
		default:           icon = 'fa-question'; tooltip = 'Source: Not specified';
	}

	return new Handlebars.SafeString('<i class="fa ' + icon + ' fa-fw" data-toggle="tooltip" data-placement="top" title="' + tooltip + '"></i>');
});

Handlebars.registerHelper('statusIcon', function() {
	var icon    = '',
	color   = 'inherit',
	tooltip = '';

	if(this.status === 'cancelled') {
		icon    = 'fa-ban';
		tooltip = 'Cancelled';

		// if(this.sums.refundable > 0 {
			if(this.sums.have > this.cancellation_fee) {
			// Refund necessary!
			color   = '#d9534f';
			tooltip = 'Cancelled, refund necessary';
		}

		if(this.sums.have < this.cancellation_fee) {
			color   = '#f0ad4e';
			tooltip = 'Cancelled, payment outstanding';
		}
	}
	else if(this.status === 'confirmed') {
		icon = 'fa-check';

		if(this.decimal_price === '0.00') {
			color = '#5cb85c';
			tooltip = 'Confirmed, free of charge';
		}
		else if(this.agent && this.agent.terms === 'fullamount') {
			color = '#5cb85c';
			tooltip = 'Confirmed, agent takes full amount';
		}
		else {
			var percentage = this.sums.have / this.decimal_price;

			if(percentage === 1) color = '#5cb85c';
			else if(percentage === 0) color = '#d9534f';
			else color = '#f0ad4e';

			if(percentage === 1) tooltip = 'Confirmed, completely paid';
			else                 tooltip = 'Confirmed, ' + window.company.currency.symbol + ' ' + this.sums.have + '/' + this.decimal_price + ' paid';

			if(percentage > 1) {
				icon = 'fa-exclamation';
				color = '#d9534f';
				tooltip = 'Confirmed, refund necessary';
			}
		}
	}
	else if(this.status === 'reserved') {
		icon    = 'fa-clock-o';
		tooltip = 'Reserved until ' + moment(this.reserved_until).format('DD MMM, HH:mm');
	}
	else if(this.status === 'expired') {
		icon    = 'fa-clock-o';
		tooltip = 'Reservation expired on ' + moment(this.reserved_until).format('DD MMM, HH:mm');
		color   = '#d9534f';
	}
	else if(this.status === 'initialised') {
		icon    = 'fa-star-o';
		tooltip = 'New';
	}
	else if(this.status === 'saved') {
		icon    = 'fa-floppy-o';
		tooltip = 'Saved';
	}
	else if(this.status === 'temporary') {
		icon    = 'fa-pencil';
		tooltip = 'In editing mode';
	}

	return new Handlebars.SafeString('<i class="fa ' + icon + ' fa-fw fa-lg" style="color: ' + color + ';" data-toggle="tooltip" data-placement="top" title="' + tooltip + '"></i>');
});

Handlebars.registerHelper('arrivalDate', function() {
	if(this.arrival_date === null || this.arrival_date === 'null')
		return '-';

	return moment(this.arrival_date).format('DD MMM YYYY'); // e.g. '14 Oct 2015'
});

/*Handlebars.registerHelper('sumPaid', function() {
	return _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0).toFixed(2);
});*/

Handlebars.registerHelper('sumPaid', function() {
	//console.log(this.sums.have);
	return this.sums.have;
});

/*Handlebars.registerHelper("remainingPay", function() {
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
});*/

Handlebars.registerHelper("remainingPay", function() {
	return new Handlebars.SafeString(Booking.generateRemainingBar.call(this));
});

Handlebars.registerHelper('addTransactionButton', function() {
	var sum = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0);

	var disabled = '';
	if(this.decimal_price === '0.00')
		disabled = 'disabled';

	return new Handlebars.SafeString('<button onclick="addTransaction(' + this.id + ', this);" class="btn btn-default btn-sm" ' + disabled + '><i class="fa fa-credit-card fa-fw"></i> Add Transaction</button>');
});
Handlebars.registerHelper('editButton', function() {
	/*if(this.status === 'confirmed')
		return '';*/

	return new Handlebars.SafeString('<button onclick="editBooking(' + this.id + ', this);" class="btn btn-default btn-sm"><i class="fa fa-eye fa-fw"></i> View</button>');
});

$(function() {
	var bookingListItem = Handlebars.compile( $('#booking-list-item-template').html() );

	/*
	if(typeof window.bookings === 'object')
		$('#booking-list').html( bookingListItem({bookings: window.bookings}) );
	*/
	Booking.getRecent(function(data) {
		window.bookings = data;
		var results = [];

		var results = _.filter(data, function(booking) {
			booking.sums = {};
			Booking.prototype.calculateSums.call(booking);
			Booking.prototype.setStatus.call(booking);
		});
		$('#booking-list').html( bookingListItem({bookings: data}) );

		// Initiate tooltips
		$('#booking-list').find('[data-toggle=tooltip]').tooltip();
	});

	$('#booking-list').on('click', '.accordion-header', function() {
		$(this).toggleClass('expanded');
		$('.accordion-' + this.getAttribute('data-id')).toggle();
	});

	$('input.datepicker').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});

	$('#find-booking-form').on('submit', function(event) {
		event.preventDefault();

		var btn = $('#find-booking');
		btn.append(' <i class="fa fa-cog fa-spin"></i>');

		var params = $(this).serializeObject();

		Booking.filter(params, function success(data) {
			$('#booking-list').html( bookingListItem({bookings: data}) );

			// Initiate tooltips
			$('#booking-list').find('[data-toggle=tooltip]').tooltip();

			btn.html('Find Booking');
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.errors[0]);
			btn.html('Find Booking');
		});
	});
});

function editBooking(booking_id, self) {
	// Set loading indicator
	$(self).after('<span id="save-loader" class="loader"></span>');

	// Load booking data and redirect to add-booking tab
	Booking.get(booking_id, function success(object) {
		window.booking = object;
		window.clickedEdit = true;
		window.location.hash = 'add-booking';
	});
}

function addTransaction(booking_id, self) {
	// Set loading indicator
	$(self).after('<span id="save-loader" class="loader"></span>');

	// Load booking data and redirect to add-transaction tab
	Booking.get(booking_id, function success(object) {
		window.booking     = object;
		window.clickedEdit = true;

		window.location.hash = 'add-transaction';
	});
}
