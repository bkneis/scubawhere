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

		var sum = _.reduce(this.payments, function(memo, payment) {
			return memo + payment.amount * 1;
		}, 0);

		if(sum > this.cancellation_fee) {
			// Refund necessary!
			color   = '#d9534f';
			tooltip = 'Cancelled, refund necessary';
		}

		if(sum < this.cancellation_fee) {
			color   = '#f0ad4e';
			tooltip = 'Cancelled, payment outstanding';
		}
	}
	else if(this.status === 'confirmed') {
		icon = 'fa-check';

		var sum = _.reduce(this.payments, function(memo, payment) {
			return memo + payment.amount * 1;
		}, 0);

		var percentage = sum / this.decimal_price;

		if(percentage === 1) color = '#5cb85c';
		else if(percentage === 0) color = '#d9534f';
		else color = '#f0ad4e';

		if(percentage === 1) tooltip = 'Confirmed, completely paid';
		else                 tooltip = 'Confirmed, ' + window.company.currency.symbol + ' ' + sum.toFixed(2) + '/' + this.decimal_price + ' paid';
	}
	else if(this.status === 'reserved') {
		icon    = 'fa-clock-o';
		tooltip = 'Reserved until ' + moment(this.reserved).format('DD MMM, HH:mm');

		if(this.reserved == null) {
			icon    = 'fa-exclamation';
			tooltip = 'Expired reservation!';
			color   = '#d9534f';
		}
	}
	else if(this.status === 'saved') {
		icon    = 'fa-floppy-o';
		tooltip = 'Saved';
	}

	return new Handlebars.SafeString('<i class="fa ' + icon + ' fa-fw fa-lg" style="color: ' + color + ';" data-toggle="tooltip" data-placement="top" title="' + tooltip + '"></i>');
});

Handlebars.registerHelper('arrivalDate', function() {
	if(this.arrival_date === null || this.arrival_date === 'null')
		return '-';

	return moment(this.arrival_date).format('DD MMM YYYY'); // e.g. '14 Oct 2015'
});

Handlebars.registerHelper('price', function() {
	if(this.status === 'cancelled') {
		return new Handlebars.SafeString(window.company.currency.symbol + ' <del class="text-danger">' + this.decimal_price + '</del> ' + (this.cancellation_fee));
	}

	return window.company.currency.symbol + " " + this.decimal_price;
});

Handlebars.registerHelper('sumPaid', function() {
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

Handlebars.registerHelper('addTransactionButton', function() {
	var sum = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0);

	var disabled = '';
	if(this.decimal_price === '0.00' || sum == this.decimal_price)
		disabled = 'disabled';

	return new Handlebars.SafeString('<button onclick="addTransaction(' + this.id + ', this);" class="btn btn-default" ' + disabled + '><i class="fa fa-credit-card fa-fw"></i> Add Transaction</button>');
});
Handlebars.registerHelper('editButton', function() {
	// The edit button should always be available, because it also works as an info button, to see the booking details
	/*if(this.status === 'cancelled')
		return '';*/

	return new Handlebars.SafeString('<button onclick="editBooking(' + this.id + ', this);" class="btn btn-default"><i class="fa fa-pencil fa-fw"></i> Edit</button>');
});
Handlebars.registerHelper('cancelButton', function() {
	var disabled = '';

	if(this.status === 'cancelled')
		disabled = ' disabled';

	return new Handlebars.SafeString('<button onclick="cancelBooking(' + this.id + ', this);" class="btn btn-danger pull-right"' + disabled + '><i class="fa fa-times fa-fw"></i> Cancel</button>');
});

$(function() {
	var bookingListItem = Handlebars.compile( $('#booking-list-item-template').html() );

	/*
	if(typeof window.bookings === 'object')
		$('#booking-list').html( bookingListItem({bookings: window.bookings}) );
	*/
	Booking.getAll(function(data) {
		window.bookings = _.indexBy(data, 'id');
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

var cancellationFeeTemplate  = Handlebars.compile($("#cancellation-fee-template").html());

function cancelBooking(booking_id, self) {

	// Set loading indicator
	var btn = $(self);
	btn.html('<i class="fa fa-cog fa-spin fa-fw"></i> Cancel');

	var params = {
		'_token': getToken(),
		'booking_id': booking_id
	};

	$('#modalWindows')
	.append( cancellationFeeTemplate() )     // Create the modal
	.children('#modal-cancellation-fee')             // Directly find it and use it
	.data('params', params)                         // Assign the eventObject to the modal DOM element
	.reveal({                                       // Open modal window | Options:
		animation: 'fadeAndPop',                    // fade, fadeAndPop, none
		animationSpeed: 300,                        // how fast animtions are
		closeOnBackgroundClick: true,               // if you click background will modal close?
		dismissModalClass: 'close-modal',           // the class of a button or element that will close an open modal
		btn: btn,                                   // Submit by reference to later get it as this.btn for resetting
		onFinishModal: function() {
			// Aborted action
			if(!window.sw.modalClosedBySelection)
				this.btn.html('<i class="fa fa-times fa-fw"></i> Cancel'); // Reset the button
			else
				delete window.sw.modalClosedBySelection;

			$('#modal-cancellation-fee').remove();   // Remove the modal from the DOM
		}
	});
}

$('#modalWindows').on('submit', '.cancellation-form', function(event) {
	event.preventDefault();
	var modal  = $(event.target).closest('.reveal-modal');
	var btn    = modal.find('.cancel-booking').html('<i class="fa fa-cog fa-spin fa-fw"></i> Cancel Booking');
	var params = modal.data('params');

	// Figure out the cancellation fee
	var cancellation_fee = 0;
	var selectedRadio    = modal.find(':checked').val();

	switch(selectedRadio) {
		case 'fee':
			cancellation_fee = modal.find('[name=cancellation_fee]').val();
			break;
		case 'percentage':
			var bookingPrice = window.bookings[params.booking_id].decimal_price;
			var percentage   = modal.find('[name=fee_percentage]').val() / 100;
			cancellation_fee = bookingPrice * percentage;
			break;
	}

	params.cancellation_fee = cancellation_fee;

	// Cancel booking and reload list of bookings
	Booking.cancel(params, function success(status) {

		Booking.getAll(function(data) {
			var bookingListItem = Handlebars.compile( $('#booking-list-item-template').html() );
			window.bookings = _.indexBy(data, 'id');
			$('#booking-list').html( bookingListItem({bookings: data}) );

			// Initiate tooltips
			$('#booking-list').find('[data-toggle=tooltip]').tooltip();
		});

		pageMssg(status, 'success');

		// Close modal window
		window.sw.modalClosedBySelection = true;
		$('#modal-cancellation-fee .close-reveal-modal').first().click();
	},
	function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0]);
		btn.html('Cancel Booking');
	});
});
