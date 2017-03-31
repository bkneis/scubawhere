
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1 && (!window.tourStart))
{
	window.location.href = '#dashboard';
}

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
		/*else if(this.agent && this.agent.terms === 'deposit') {

			var net_price = this.decimal_price * (1 - (this.agent.commission / 100));
			net_price = Math.round(net_price * 100) / 100; // round to 2 decimals
			var percentage = this.sums.have / net_price;

			if(percentage === 1) color = '#5cb85c';
			else if(percentage === 0) color = '#d9534f';
			else color = '#f0ad4e';

			if(percentage === 1) tooltip = 'Confirmed, completely paid';
			else                 tooltip = 'Confirmed, ' + window.company.currency.symbol + ' ' + this.sums.have + '/' + net_price + ' paid';

			if(percentage > 1) {
				icon = 'fa-exclamation';
				color = '#d9534f';
				tooltip = 'Confirmed, refund necessary';
			}
		}*/
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

Handlebars.registerHelper('price', function() {
	var price = (parseFloat(this.decimal_price) + parseFloat(this.sums.surcharge)).toFixed(2);
	if(this.status === 'cancelled') {
		return new Handlebars.SafeString(window.company.currency.symbol + ' <del class="text-danger">' + price + '</del> ' + parseFloat(parseInt(this.cancellation_fee) / 100).toFixed(2));
	}

	return window.company.currency.symbol + " " + price;
});

Handlebars.registerHelper('prettyPrice', function(price) {
	return new Handlebars.SafeString(window.company.currency.symbol + ' ' + parseFloat(price).toFixed(2));
});

Handlebars.registerHelper('convertPrice', function(price) {
	return new Handlebars.SafeString(window.company.currency.symbol + ' ' + (parseFloat(price) / 100).toFixed(2));
})

Handlebars.registerHelper('sumPaid', function() {
	return (parseFloat(this.sums.have) + parseFloat(this.sums.surcharge)).toFixed(2);
});

Handlebars.registerHelper("remainingPay", function() {
	return new Handlebars.SafeString(Booking.generateRemainingBar.call(this));
});

Handlebars.registerHelper('addTransactionButton', function(id) {
	// I needed to remove this to ensure users can add transactions for cancelled booking with refunds
	/*if(this.agent && this.agent.terms === 'fullamount') {
		return '';
	}*/

	if(this.status === 'temporary') {
		return '';
	}

	return new Handlebars.SafeString('<button onclick="addTransaction(' + id + ', this);" class="btn btn-default"><i class="fa fa-credit-card fa-fw"></i> Transactions</button>');
});
Handlebars.registerHelper('invoiceButton', function(id) {
	return new Handlebars.SafeString('<button onclick="viewInvoices(' + id + ')" class="btn btn-success"><i class="fa fa-file fa-fw"></i> Invoices</button> ');
});
Handlebars.registerHelper('viewButton', function(id) {
	// The edit button should always be available, because it also works as an info button, to see the booking details
	return new Handlebars.SafeString('<button onclick="viewBooking(' + this.id + ', this);" class="btn btn-default btn-info"><i class="fa fa-eye fa-fw"></i> View</button>');
});
Handlebars.registerHelper('editButton', function(id) {
	// The edit button should always be available, because it also works as an info button, to see the booking details
	return new Handlebars.SafeString('<button onclick="editBooking(' + this.id + ', this);" class="btn btn-default btn-warning"><i class="fa fa-pencil fa-fw"></i> Edit</button>');
});
Handlebars.registerHelper('cancelButton', function() {
	var disabled = '';
	var btnText  = 'Cancel';

	if(this.status === 'cancelled')
		disabled = ' disabled';

	if(this.status === 'temporary')
		btnText = 'Discard';

	return new Handlebars.SafeString('<button onclick="cancelBooking(' + this.id + ', \'' + this.status + '\', this);" class="btn btn-danger pull-right"' + disabled + '><i class="fa fa-times fa-fw"></i> ' + btnText + '</button>');
});
Handlebars.registerHelper('changeRefButton', function(id) {
	if (this.agent !== null) {
		return new Handlebars.SafeString('<button onclick="changeRef(' + id + ')" class="btn btn-warning"><i class="fa fa-pencil fa-fw"></i> Change Agent Ref</button> ');
	}
});

Handlebars.registerHelper('totalSurcharged', function (id) {
	var booking = _.findWhere(window.bookings, {id: id});
	return booking.sums.surcharge;
});

Handlebars.registerHelper('hasSurcharge', function (id, options) {
	var booking = _.findWhere(window.bookings, {id: id});
	if (booking.sums.surcharge !== "0.00") {
		return options.fn(this);
	}
	return options.inverse(this);
});

//var display;

$(function() {

	Customer.getAllCustomers(function(data) {
		window.customers = _.indexBy(data, 'id');
	});

	Booking.getAll(function(data) {
		// window.bookings = _.indexBy(data, 'id');
		window.bookings = _.sortBy(data, function(booking) { return -booking.id; });

		Course.getAll(function (data) {
			window.courses = _.indexBy(data, 'id');
			Package.getAllPackages(function (data) {
				window.packages = _.indexBy(data, 'id');
				renderBookingList(window.bookings);
			});
		});

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
			renderBookingList(data, "all");

			btn.html('Find Booking');
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			if(data.errors) pageMssg(data.errors[0]);
			btn.html('Find Booking');
		});
	});

	$('#find-booking-form').on('reset', function(event) {
		renderBookingList(window.bookings);
	});

	$("#booking-types").on('click', ':button', function(){
		var display = $(this).attr('display');
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
	
	$('#change-ref-modal').on('click', '#btn-change-ref', function () {
		var params = {
			booking_id: window.changeBookingRefId,
			ref: $('#new-booking-ref').val(),
			_token: window.token
		};
		
		Booking.changeRef(params, function success(response) {
			pageMssg(response.status, 'success');
			renderBookingList(window.bookings);
			$('#change-ref-modal').modal('hide');
		},
		function error(xhr) {
			var error = (JSON.parse(xhr.responseText)).errors[0];
			pageMssg(error, 'danger');
		});
	});

});

function extractCustomers(details) {
	var customers = [];
	for(var i in details) {
		var customer = {
			id   : details[i].customer.id,
			name : details[i].customer.firstname + ' ' + details[i].customer.lastname
		};
		if(_.findWhere(customers, customer) == null) {
			customers.push(customer);
		}
	}
	return customers;
}

function generateCustomerList(customers) {
	$('#customers-list').empty();
	for(var i in customers) {
		$('#customers-list').append('<option value="' + customers[i].id + '">' + customers[i].name + '</option>');
	}
}

/**
 * @todo move these two functions to global helper file
 */
function isset(obj) {
	return !(obj === undefined || obj === null || obj === [] || obj === {});
}

function calcNumNights(accomm) {
	return ' ( ' + parseInt(parseFloat(accomm.decimal_price) / parseFloat(accomm.decimal_price_per_day)).toString() + ' nights)';
}

function generateInvoice(customer_id, booking) {

	console.log(booking);

	var discountPercentage = (booking.discount / (booking.price + booking.discount)) * 100;

	var invoice = {};
	// Sort bookingdetails by start date
	booking.bookingdetails = _.sortBy(booking.bookingdetails, function(detail) {
		if(detail.session)
			return detail.session.start;
		else if(detail.training_session)
			return detail.training_session.start;
		else
			return '0'; // Temporary/un-dated sessions should be displayed on top
	});

	// Sort accommodations by start date
	/*invoice.accommodations = _.sortBy(booking.accommodations, function(accom) {
		return accom.pivot.start;
	});*/
	invoice.accommodations = _.filter(booking.accommodations, function (accomm) {
		if (accomm.customer.id !== customer_id) {
			return;
		}
		accomm.name += calcNumNights(accomm);
		return accomm;
	});

	// Generate booked items list (for the price table)
	var packagesSummary = [];
	var coursesSummary  = [];
	var ticketsSummary  = [];
	var addonsSummary   = [];
	invoice.subTotal    = 0;
	invoice.total       = 0;

	_.each(booking.bookingdetails, function(detail) {
		if (detail.customer_id !== customer_id) {
			return;
		}
		if (detail.packagefacade) { // This catches NULL and UNDEFINED
			if (!packagesSummary[detail.packagefacade.id]) {
				packagesSummary[detail.packagefacade.id] = detail.packagefacade.package;
				invoice.subTotal += parseFloat(detail.packagefacade.package.decimal_price);
			}
		}
		else if (detail.course) {
			if (!coursesSummary[detail.customer.id + '-' + detail.course.id]) {
				coursesSummary[detail.customer.id + '-' + detail.course.id] = detail.course;
				invoice.subTotal += parseFloat(detail.course.decimal_price);
			}
		}
		else if (detail.ticket) {
			ticketsSummary.push(detail.ticket);
			invoice.subTotal += parseFloat(detail.ticket.decimal_price);
		}

		_.each(detail.addons, function(addon) {
			if (!addon.pivot.packagefacade_id) {
				if (addonsSummary[addon.id]) {
					addonsSummary[addon.id].qtySummary += parseInt(addon.pivot.quantity);
				}
				else {
					addon.qtySummary = parseInt(addon.pivot.quantity);
					addonsSummary[addon.id] = addon;
				}
			}
		});
	});

	invoice.packages = packagesSummary;
	invoice.courses  = coursesSummary;
	invoice.tickets  = ticketsSummary;
	invoice.addons   = addonsSummary;

	// This is a hack as handlebars cannot iterate over an array that uses malformed
	// index's, i.e. courses can use index's such as 67-12 therefore we need to add them back.
	var courses = [];
	for (var i in invoice.courses) {
		courses.push(invoice.courses[i]);
	}
	invoice.courses = courses;

	// Calculate addons total and quantity
	for (var j in invoice.addons) {
		invoice.subTotal += parseFloat(invoice.addons[j].decimal_price) * invoice.addons[j].qtySummary;
		invoice.addons[j].name += ' x ' + invoice.addons[j].qtySummary.toString();
	}
	
	_.each(invoice.accommodations, function (accommodation) {
		invoice.subTotal += parseFloat(accommodation.decimal_price);
	});

	// Calculate total and discounted amount
	invoice.total    = (invoice.subTotal * (1 - (discountPercentage / 100))).toFixed(2);
	invoice.discount = discountPercentage.toFixed(2);

	return invoice;
}

function getFileName(ref) {
	return $('#customers-list').find(":selected").text() + ' booking invoice (' + ref + ')';
}

function loadCustomerInvoice(customer_id) {
	customer_id = parseInt(customer_id);
	var invoiceTemplate = Handlebars.compile($('#invoice-template').html());
	//var invoice = generateInvoice(customer_id, window.currentBookingSelected.bookingdetails);
	var invoice = generateInvoice(customer_id, window.currentBookingSelected);
	$('#invoice-container').empty().append(invoiceTemplate(invoice));
	$('#tbl-customer-invoice').DataTable({
		"pageLength": 50,
		"dom": 'Bt',
		"bSort" : false,
		"buttons": [
			{
				extend : 'print',
				title  : getFileName(window.currentBookingSelected.reference)
			}
		]
	});
}

function viewInvoices(id) {
	var customerInvoiceTemplate = Handlebars.compile($('#customer-invoice-template').html());
	Booking.get(id, function (data) {
		window.currentBookingSelected = data;
		$('#modalWindows')
			.append( customerInvoiceTemplate({ reference : data.reference}) )
			.children('#modal-customer-invoice')
			.reveal({
				animation: 'fadeAndPop',
				animationSpeed: 300,
				closeOnBackgroundClick: true,
				dismissModalClass: 'close-modal',
				onOpenedModal: function() {
					var customers = extractCustomers(data.bookingdetails);
					generateCustomerList(customers);
					if(customers.length > 0) {
						loadCustomerInvoice(customers[0].id);
					} else {
						$('#invoice-container').empty().append('<h1 style="text-align: center">No customers available</h1>')
					}
				},
				onFinishModal: function() {
					$('#modal-customer-invoice').remove();   // Remove the modal from the DOM
				}
			});
		//$('#view-invoices-modal').modal('show');
	});
}

function emailCustomer(id) {

	$('#email-customer-modal').modal('show');
	var emailCustomerTemplate = Handlebars.compile($("#email-customer-template").html());
	$("#email-customer-details").html(emailCustomerTemplate(window.customers[id]));

}

var bookingListItem = Handlebars.compile( $('#booking-list-item-template').html() );
function renderBookingList(bookings, display) {

	if(!display) display = "confirmed";

	$(".btn-switch").removeClass("btn-primary");
	$("#filter-"+display).addClass("btn-primary");

	var results = [];

	var results = _.filter(bookings, function(booking) {

		/*if(booking.agent !== null)
		{
			if(booking.agent.terms === 'deposit')
			{
				var net_price = (booking.decimal_price * (1 - (booking.agent.commission / 100)));
				net_price = Math.round(net_price * 100) / 100;
				booking.decimal_price = net_price;	
			}
		}*/
		booking.sums = {};
		Booking.prototype.calculateSums.call(booking);
		Booking.prototype.setStatus.call(booking);
		// console.log(booking);
		if(display != "all") {
			if(booking.status === display) return true;
		}
		else
			return true;
	});

	console.log('res', results);

	$('#booking-table-div').html( bookingListItem({bookings: results}) );

	// Initiate tooltips
	$('#booking-list').find('[data-toggle=tooltip]').tooltip();

	if(results.length > 0) createDataTable();

}

function viewBooking(booking_id, self) {
	// Set loading indicator
	$(self).after('<span id="save-loader" class="loader"></span>');

	// Load booking data and redirect to add-booking tab
	Booking.get(booking_id, function success(object) {
		window.booking      = object;
		window.booking.mode = 'view';
		window.clickedEdit  = true;

		window.location.hash = 'add-booking';
	});
}

function editBooking(booking_id, self) {
	// Set loading indicator
	$(self).after('<span id="save-loader" class="loader"></span>');

	// Load booking data and redirect to add-booking tab
	Booking.startEditing(booking_id, function success(object) {
		window.booking      = object;
		window.booking.mode = 'edit';
		window.clickedEdit  = true;

		window.location.hash = 'add-booking';
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		if(data.errors) pageMssg(data.errors[0]);
		$('.loader').remove();
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

function cancelBooking(booking_id, booking_status, self) {

	// Set loading indicator
	var btn = $(self);
	btn.html('<i class="fa fa-cog fa-spin fa-fw"></i> Cancel');

	var params = {
		'_token': getToken(),
		'booking_id': booking_id
	};

	$('#modalWindows')
	.append( cancellationFeeTemplate({'status': booking_status, 'booking_id': booking_id}) ) // Create the modal
	.children('#modal-cancellation-fee')            // Directly find it and use it
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
	
	params.cancel_reason = $('#cancel_reason').val();

	if (!($("input[type=radio]:checked").length > 0)) {
		pageMssg('Please select a cancellation option', 'danger');
		btn.html('Cancel Booking');
		return;
	}

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
		if(data.errors) pageMssg(data.errors[0]);
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
        "pageLength": 10,
		"dom": '<"col-md-6 dt-buttons"B><"col-md-6"f>rt<"col-md-6"l><"col-md-6"p>',
		"buttons": [
			{
				extend : 'excel',
	   			title  : 'Bookings List'
			},
			{
				extend : 'pdf',
				title  : 'Bookings List',
				orientation: 'landscape'
			},
			{
				extend : 'print',
				title  : 'Bookings List'
			}
		]
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
			var self = this;
			Booking.get(parseInt($(self).attr('data-id')), function (data) {
				window.bookings = _.indexBy(window.booking, 'id');
				window.bookings[data.id] = data;
				row.child( generateBookingDetails($(self).attr('data-id')) ).show();
			}, function (xhr) {
				console.log('fail', xhr);		
			});
            tr.addClass('shown');
        }
    });
}

function changeRef(id) {
	window.changeBookingRefId = id;
	$('#change-ref-modal').modal('show');
}
