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
	else if(this.status === 'reserved') {
		icon    = 'fa-clock-o';
		tooltip = 'Reserved until ' + moment(this.reserved_until).format('DD MMM, HH:mm');

		if(this.reserved_until == null) {
			tooltip = 'Reservation expired';
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
	return this.sums.have;
});

Handlebars.registerHelper("remainingPay", function() {
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
});

Handlebars.registerHelper('addTransactionButton', function(id) {
	return new Handlebars.SafeString('<button onclick="addTransaction(' + id + ', this);" class="btn btn-default"><i class="fa fa-credit-card fa-fw"></i> Transactions</button>');
});
Handlebars.registerHelper('editButton', function(id) {
	// The edit button should always be available, because it also works as an info button, to see the booking details
	/*if(this.status === 'cancelled')
	return '';*/

	return new Handlebars.SafeString('<button onclick="editBooking(' + this.id + ', this);" class="btn btn-default"><i class="fa fa-pencil fa-fw"></i> View & Edit</button>');
});
Handlebars.registerHelper('cancelButton', function() {
	var disabled = '';

	if(this.status === 'cancelled')
		disabled = ' disabled';

	return new Handlebars.SafeString('<button onclick="cancelBooking(' + this.id + ', this);" class="btn btn-danger pull-right"' + disabled + '><i class="fa fa-times fa-fw"></i> Cancel</button>');
});

//var display;

$(function() {

	var display;

	Customer.getAllCustomers(function(data) {
		window.customers = _.indexBy(data, 'id');
	});

	Booking.getAll(function(data) {
		// window.bookings = _.indexBy(data, 'id');
		window.bookings = _.sortBy(data, function(booking) { return -booking.id; });
		renderBookingList(window.bookings, display);

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
			// Doesn't need sorting, because the server sorts DESC
			renderBookingList(data, display);

			btn.html('Find Booking');
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.errors[0]);
			btn.html('Find Booking');
		});
	});

	$('#find-booking-form').on('reset', function(event) {
		renderBookingList(window.bookings, display);
	});

	$("#booking-types").on('click', ':button', function(){
		$(".btn-switch").removeClass("btn-primary");
		display = $(this).attr('display');
		$("#filter-"+display).addClass("btn-primary");
		renderBookingList(window.bookings, display);
	});

	$('#email-customer-modal').on('submit', '#email-customer-form', function(e) {
		e.preventDefault();

		var btn = $(this).find('button[type="submit"]');
		btn.html('<i class="fa fa-cog fa-spin"></i> Sending...');

		var params = $(this).serializeObject();
		params._token = window.token;
		console.log(params);

		Company.sendEmail(params, function success(data){
			pageMssg('Thank you, your email has been sent', true);
			btn.html('Send');
			$('#email-customer-modal').modal('hide');
		},
		function error(xhr) {
			console.log(xhr);
			var data = JSON.parse(xhr.responseText);
			btn.html('Send');
			pageMssg(data.error.message, 'danger');

		});

	});

});

function emailCustomer(id) {

	$('#email-customer-modal').modal('show');
	var emailCustomerTemplate = Handlebars.compile($("#email-customer-template").html());
	$("#email-customer-details").html(emailCustomerTemplate(window.customers[id]));

}

var bookingListItem = Handlebars.compile( $('#booking-list-item-template').html() );
function renderBookingList(bookings, display) {

	if(!display) var display = "confirmed";

	var results = [];

	var results = _.filter(bookings, function(booking) {
		booking.sums = {};
		Booking.prototype.calculateSums.call(booking);
		Booking.prototype.setStatus.call(booking);
		// console.log(booking);
		if(display != "all") {
			if(booking.status == display) return true;
		}
		else
			return true;
	});

	$('#booking-table-div').html( bookingListItem({bookings: results}) );

	// Initiate tooltips
	$('#booking-list').find('[data-toggle=tooltip]').tooltip();

	if(results.length > 0) createDataTable();

}

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

	console.log(booking_id);
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
		var bookingPrice = _.find(window.bookings, function(booking) {
			return parseInt(booking.id) === parseInt(params.booking_id);
		}).decimal_price;
		var percentage   = modal.find('[name=fee_percentage]').val() / 100;
		cancellation_fee = bookingPrice * percentage;
		break;
	}

	params.cancellation_fee = cancellation_fee;

	// Cancel booking and reload list of bookings
	Booking.cancel(params, function success(status) {

		Booking.getAll(function(data) {
			window.bookings = _.indexBy(data, 'id');
			window.bookings = _.sortBy(window.bookings, function(booking) { return -booking.id; });
			renderBookingList(window.bookings);
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

var bookingDetailsTemplate = Handlebars.compile($('#booking-details-template').html());
function generateBookingDetails(id) {

	var booking = _.find(window.bookings, function(booking) {
		return parseInt(booking.id) === parseInt(id);
	});

	return bookingDetailsTemplate({bookingDetails : [booking]});
}

function createDataTable() {
	var bookingTable = $('#bookings-table').DataTable({
		"paging":   true,
		"ordering": false,
		"info":     false,
		"pageLength" : 10,
		"searching" : false,
		"dom": 'T<"clear">lfrtip',
		"tableTools": {
			"sSwfPath": "/common/vendor/datatables-tabletools/swf/copy_csv_xls_pdf.swf"
		}
	});

	// Add event listener for opening and closing details
    $('#booking-list').on('click', '.accordion-header', function () {
        var tr = $(this).closest('tr');
        var row = bookingTable.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( generateBookingDetails($(this).attr('data-id')) ).show();
            tr.addClass('shown');
        }
    });
}
