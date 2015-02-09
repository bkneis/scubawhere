getToken();

window.promises = {};

Handlebars.registerHelper('currency', function() {
	if(typeof window.company !== 'undefined')
		return window.company.currency.symbol;
	else
		return '???'; // TODO Set placeholder and try again in a second (similar to the 'countryName' helper)
});

Handlebars.registerHelper("freeSpaces", function(capacity) {
	return generateFreeSpacesBar(capacity, this.id);
});

/**
 * Generate the free spaces percentage bar
 * @param  {array} capacity  Array of the following form: [used-up places, total places]
 * @return {string}          Handlebars.SafeString containing the progress bar markup
 */
function generateFreeSpacesBar(capacity, id) {
	var freeSpaces = capacity[1] - capacity[0];
	var percentage = (capacity[0] / capacity[1]) * 100;

	var color = '#5cb85c'; var bgClasses = 'bg-success border-success';
	if(percentage >= 75) { color = '#f0ad4e'; bgClasses = 'bg-warning border-warning'; }
	if(percentage == 1) { color = '#d9534f'; bgClasses = 'bg-danger border-danger'; }

	var html = '';
	html += '<div data-id="' + id + '" class="percentage-bar-container ' + bgClasses + '">';
	html += '	<div class="percentage-bar" style="background-color: ' + color + '; width: ' + percentage + '%">&nbsp;</div>';
	html += '	<span class="percentage-left">' + freeSpaces + '</span>';
	html += '</div>';

	return new Handlebars.SafeString(html);
}

Handlebars.registerHelper("tripFinish", function(start, duration) {
	return friendlyDate( moment(start).add(duration, 'hours') );
});

Handlebars.registerHelper("friendlyDate", function(date) {
	return friendlyDate(date);
});

Handlebars.registerHelper("firstChar", function(s) {
	return s[0];
});

Handlebars.registerHelper("isLead", function (id) {
	if(booking.lead_customer === false) return new Handlebars.SafeString('<small><i class="fa fa-cog fa-spin fa-fw"></i></small>');

	if(booking.lead_customer && booking.lead_customer.id == id) {
		return new Handlebars.SafeString('<small><span class="label label-warning">LEAD</span></small>');
	}
});

Handlebars.registerHelper("notEmptyObj", function (item, options) {
		return $.isEmptyObject(item) ? options.inverse(this) : options.fn(this);
});

Handlebars.registerHelper("priceRange", function(prices) {
	if(prices.length > 1) {
		var min=null, max=null;
		$.each(prices, function(item, value) {
			var price = parseFloat(value.decimal_price).toFixed(2);
			if ((min === null) || (price < min)) { min = price; }
			if ((max === null) || (price > max)) { max = price; }
		});
		if(min != max) {
			return min+" - "+max;
		}else{
			return min;
		}

	}else if(prices.length == 1){
		return prices[0].decimal_price;
	}else{
		return "hmmm";
	}
});

Handlebars.registerHelper("countryName", function(id) {
	if(typeof window.countries !== 'undefined')
		return window.countries[id].name;
	else {
		window.promises.loadedCountries.done(function() {
			var id = $('#countryPlaceholder').attr('data-id');
			$('#countryPlaceholder').html(window.countries[id].name);
		});
		return new Handlebars.SafeString('<span id="countryPlaceholder" data-id="' + id + '"><i class="fa fa-cog fa-spin"></i></span>');
	}
});

// Load all initial handlebars templates

var agentTemplate          = Handlebars.compile($("#agents-list-template").html());
var ticketTemplate         = Handlebars.compile($("#tickets-list-template").html());
var packageTemplate        = Handlebars.compile($("#package-list-template").html());
var tripTemplate           = Handlebars.compile($("#trips-list-template").html());
var addonsTemplate         = Handlebars.compile($("#addons-list-template").html());
var accommodationsTemplate = Handlebars.compile($("#accommodations-list-template").html());
var customersTemplate      = Handlebars.compile($("#customers-list-template").html());
var countriesTemplate      = Handlebars.compile($("#countries-template").html());
var boatroomModalTemplate  = Handlebars.compile($("#boatroom-select-modal-template").html());
var bookingSummaryTemplate = Handlebars.compile($("#booking-summary-template").html());


/**
 * Load all data in order of requirement
 */

window.promises.loadedAgents = $.Deferred();
Agent.getAllAgents(function(data){
	window.agents = _.indexBy(data, 'id');
	$("#agents-list").html(agentTemplate({agents:window.agents}));
	window.promises.loadedAgents.resolve();
});

window.promises.loadedTickets = $.Deferred();
Ticket.getAllTickets(function(data){
	window.tickets = _.indexBy(data, 'id');
	$("#tickets-list").html(ticketTemplate({tickets:window.tickets}));
	window.promises.loadedTickets.resolve();
});

window.promises.loadedPackages = $.Deferred();
Package.getAllPackages(function(data){
	window.packages = _.indexBy(data, 'id');
	$("#package-list").html(packageTemplate({packages:window.packages}));
	window.promises.loadedPackages.resolve();
});

window.promises.loadedCustomers = $.Deferred();
Customer.getAllCustomers(function(data){
	window.customers = _.indexBy(data, 'id');
	$("#existing-customers").html(customersTemplate({customers:window.customers}));
	window.promises.loadedCustomers.resolve();
});

window.promises.loadedCountries = $.Deferred();
if(typeof window.countries === 'undefined')
	$.get("/api/country/all", function(data) {
		window.countries = _.indexBy(data, 'id');
		$("#add-customer-countries").find('#country_id').html(countriesTemplate({countries:window.countries}));
		$("#edit-customer-countries").find('#country_id').html(countriesTemplate({countries:window.countries}));
		window.promises.loadedCountries.resolve();
	});
else
	window.promises.loadedCountries.resolve();

window.promises.loadedTrips = $.Deferred();
Trip.getAllTrips(function(data){
	window.trips = _.indexBy(data, 'id');
	$("#trips").html(tripTemplate({trips:window.trips}));
	window.promises.loadedTrips.resolve();
});

// Required for having names for the utilisation percentage bars
window.promises.loadedBoatrooms = $.Deferred();
Boatroom.getAll(function(data){
	window.boatrooms = _.indexBy(data, 'id');
	window.promises.loadedBoatrooms.resolve();
});

window.promises.loadedAddons = $.Deferred();
Addon.getAllAddons(function(data){
	window.addons = _.indexBy(data, 'id');
	$("#addons-list").html(addonsTemplate({addons:window.addons}));
	window.promises.loadedAddons.resolve();
});

window.promises.loadedAccommodations = $.Deferred();
Accommodation.getAll(function(data){
	window.accommodations = _.indexBy(data, 'id');
	$("#accommodations-list").html(accommodationsTemplate({accommodations:window.accommodations}));
	window.promises.loadedAccommodations.resolve();
});

window.promises.loadedAccommodations.done(function() {
	/*
	* Datepicker
	*/

	$('input.datetimepicker').datetimepicker({
		pickDate: true,
		pickTime: true,
		minuteStepping: 5,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
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

	$('input.timepicker').datetimepicker({
		pickDate: false,
		pickTime: true,
		minuteStepping: 5,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});
});

/*
*************************
******** Sources ********
*************************
*/

$('#source-tab').on('click', '.booking-source a', function() {
	//Converts bootsrap list into a "radio button style" form element
	listGroupRadio($(this), 'btn-primary');

	//If agent select, display list of agents
	if($(this).data('type') == 'agent') {
		$('#agent-info').slideDown();
	}else{
		$('#agent-info').slideUp();
	}

});

$('#source-tab').on('click', '.source-finish', function() {

	//Get that cog spinning!
	$(this).html('<i class="fa fa-cog fa-spin"></i> Initiating...');

	//Find the source type that has been selected
	var type = $('.booking-source').children('.active').first().data("type");

	//If agent type selected, find the selected agent and prepare the ajax params
  	var params = {};

	if(type == "agent") {
		var agentId = $('#agents-list').children('.active').data('id');
		params = {_token: window.token, agent_id: agentId};
	}else{
		params = {_token: window.token, source: type};
	}

	if(type == "agent" && typeof(agentId) === 'undefined') {

		pageMssg('Please select an agent from the list to continue.', 'warning');
		$('.source-finish').html('Next');

	}else{

		// Instantiate new Booking
		window.booking = new Booking();
		booking.initiate(params, function(status) {
			$('[data-target="#ticket-tab"]').tab('show');
			$('#booking-summary').html(bookingSummaryTemplate(booking));
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.error[0], 'danger');
			$('.source-finish').html('Next');
		});

	}
});

/*
****************************
***** Tickets/Packages *****
****************************
*/

window.promises.loadedTickets.done(function() {
	$('#ticket-tab').on('click', '.add-ticket', function() {

		var id = $(this).data('id');

		//Add ticket to selectedTickets if new, otherwise increase qty
		if(typeof booking.selectedTickets[id] != "undefined") {
			booking.selectedTickets[id].qty++;
		}else{
			booking.selectedTickets[id] = window.tickets[id];
			booking.selectedTickets[id].qty = 1;
		}

		booking.store();

		//Draw the basket
		$('#booking-summary').html(bookingSummaryTemplate(booking));
	});

	$('#booking-summary').on('click', '.remove-ticket', function() {
		var id = $(this).data('id');

		//Lower quantity, if last ticket, remove from selected tickets.
		if(booking.selectedTickets[id].qty > 1) {
			booking.selectedTickets[id].qty--;
		}else{
			delete booking.selectedTickets[id];
		}

		booking.store();

		$('#booking-summary').html(bookingSummaryTemplate(booking));
	});
});

window.promises.loadedPackages.done(function() {
	$('#ticket-tab').on('click', '.add-package', function() {

		var id = $(this).data('id');

		//Add ticket to selectedTickets if new, otherwise increase qty
		if(typeof booking.selectedPackages[id] != "undefined") {
			booking.selectedPackages[id].qty++;
		}else{
			booking.selectedPackages[id] = window.packages[id];
			booking.selectedPackages[id].qty = 1;
		}

		booking.store();

		//Draw the basket
		$('#booking-summary').html(bookingSummaryTemplate(booking));
	});
});

$('#booking-summary').on('click', '.remove-package', function() {

	var id = $(this).data('id');

	//Lower quantity, if last ticket, remove from selected tickets.
	if(booking.selectedPackages[id].qty > 1) {
		booking.selectedPackages[id].qty--;
	}else{
		delete booking.selectedPackages[id];
	}

	booking.store();

	$('#booking-summary').html(bookingSummaryTemplate(booking));
});

/*
*************************
******* Customers *******
*************************
*/

var selectedCustomerTemplate = Handlebars.compile($("#selected-customer-template").html());

window.promises.loadedCustomers.done(function() {
	$('#customer-tab').on('change', '#existing-customers', function() {
		var id = $('#existing-customers').val();

		if(id <= 0) return false;
			
		$("#selected-customer").html(selectedCustomerTemplate(window.customers[id]));	
	});

	$('#customer-tab').on('click', '.add-customer', function() {
		var id = $('#existing-customers').val();

		// Prevents adding of "empty" customer
		if(id <= 0) return false;

		booking.selectedCustomers[id] = window.customers[id];
		booking.store();

		$('#booking-summary').html(bookingSummaryTemplate(booking));

		if( _.size(booking.selectedCustomers) === 1 ) {
			booking.setLead({_token: window.token, customer_id: id}, function success(status) {
				$('#booking-summary').html(bookingSummaryTemplate(booking));
			}, function error(xhr) {
				var data = JSON.parse(xhr.responseText);
				pageMssg(data.errors[0], 'danger');
			});
		}
	});
});

$('#customer-tab').on('click', '.edit-customer', function() {
	var id = $(this).data('id');

	$('#edit-customer-modal').modal('show');

	var editCustomerTemplate = Handlebars.compile($("#edit-customer-template").html());
	$("#edit-customer-details").html(editCustomerTemplate(booking.selectedCustomers[id]));

	//Set the country dropdown to the customers country (if they have one)
	$('#country_id').val(booking.selectedCustomers[id].country_id);
	$('#country_id option[value="'+booking.selectedCustomers[id].country_id+'"]').attr('selected', 'selected');
});

$('#booking-summary').on('click', '.remove-customer', function() {
	var id = $(this).data('id');

	var details = _.filter(booking.bookingdetails, function(detail) {
		return detail.customer.id == id;
	});

	if( details.length > 0 ) {
		var question = confirm('This customer already has tickets assigned.\n\n Do you want to remove the customer anyway?');
		if(!question)
			return false;
	}

	// Delete all bookingdetails of this customer on the server
	_.each(details, function(detail) {
		var params = {
			_token: window.token,
			bookingdetail_id: detail.id
		};

		booking.removeDetail(params, function success(status) {
			// All good
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.error[0], 'danger');
		});
	});

	delete booking.selectedCustomers[id];
	booking.store();

	$('#booking-summary').html(bookingSummaryTemplate(booking));

	// Check if we just removed the lead customer
	if(booking.lead_customer.id == id) {
		booking.lead_customer = false;
		var params = {};
		if(_.size(booking.selectedCustomers) > 0) {
			params = {
				_token: window.token,
				customer_id: _.find(booking.selectedCustomers, function(){return true;}).id // Returns the first selected customer
			};
			booking.setLead(params, function success(status) {
				$('#booking-summary').html(bookingSummaryTemplate(booking));
			}, function error(xhr) {
				var data = JSON.parse(xhr.responseText);
				pageMssg(data.error[0], 'danger');
			});
		}
		else {
			params = {
				_token: window.token,
				customer_id: null // unset lead_customer_id on the server
			};
			booking.setLead(params, function success(status) {
				// All good
			}, function error(xhr) {
				var data = JSON.parse(xhr.responseText);
				pageMssg(data.error[0], 'danger');
			});
		}
	}
});

window.promises.loadedCustomers.done(function() {
	$('#customer-tab').on('submit', '#edit-customer-form', function(e) {
		e.preventDefault();

		var btn = $(this).find('button[type="submit"]');
		btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

		var params = $(this).serializeObject();
		params._token = window.token;

		Customer.updateCustomer(params, function success() {
			Customer.getCustomer("id="+params.id, function(data) {
				//Updated selectedCustomers data
				window.customers[params.id] = data;
				booking.selectedCustomers[params.id] = window.customers[params.id];
				booking.store();
				booking.lead_customer = booking.selectedCustomers[params.id];

				btn.html('Save');
				$('#edit-customer-modal').modal('hide');

				$('#booking-summary').html(bookingSummaryTemplate(booking));
			});
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.errors[0], 'danger');
			btn.html('Save');
		});
	});
});

window.promises.loadedCustomers.done(function() {
	$('#customer-tab').on('submit', '#new-customer', function(e) {
		e.preventDefault();
		var form = $(this);

		var btn = $(this).find('button[type="submit"]');
		btn.html('<i class="fa fa-cog fa-spin"></i> Adding...');

		var params = form.serializeObject();
		params._token = window.token;
		params.phone = (params.dialling_code).replace(/[^a-zA-Z 0-9]+/g, '') + ' ' + params.phone;

		Customer.createCustomer(params, function success(data){
			Customer.getCustomer("id="+data.id, function(data) {

				window.customers[data.id] = data;
				booking.selectedCustomers[data.id] = window.customers[data.id];
				booking.store();

				btn.html('Add');
				form[0].reset();

				$('#booking-summary').html(bookingSummaryTemplate(booking));

				if( _.size(booking.selectedCustomers) === 1 ) {
					booking.setLead({_token: window.token, customer_id: data.id}, function success(status) {
						$('#booking-summary').html(bookingSummaryTemplate(booking));
					}, function error(xhr) {
						var data = JSON.parse(xhr.responseText);
						pageMssg(data.errors[0], 'danger');
					});
				}
			});
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.errors[0], 'danger');
			btn.html('Add');
		});
	});
});

$('#customer-tab').on('click', '.clear-form', function() {
	$(this).parents('form')[0].reset();
});

$('#customer-tab').on('click', '.lead-customer', function() {
	booking.setLead( {_token: window.token, customer_id: $(this).data('id')}, function success(status) {
		$('#booking-summary').html(bookingSummaryTemplate(booking));
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
	});
});

$('[data-target="#session-tab"]').on('show.bs.tab', function (e) {
  if(!booking.lead_customer) {
		pageMssg("Please designate a lead customer.", "danger");
		return false;
	}
	if(!booking.lead_customer.email) {
		pageMssg("Lead customer requires an email!", "danger");
		return false;
	}
	if(!booking.lead_customer.phone) {
		pageMssg("Lead customer requires a phone number!", "danger");
		return false;
	}
	if(!booking.lead_customer.country_id) {
		pageMssg("Lead customer requires a country!", "danger");
		return false;
	}
});

/*
*************************
******* Sessions *******
*************************
*/

var sessionCustomersTemplate 	= Handlebars.compile($("#session-customers-template").html());
var sessionTicketsTemplate   	= Handlebars.compile($("#session-tickets-template").html());
var sessionPackagesTemplate   	= Handlebars.compile($("#session-packages-template").html());

$('[data-target="#session-tab"]').on('show.bs.tab', function () {
	$("#session-customers").html(sessionCustomersTemplate({customers:booking.selectedCustomers}));
	$("#session-customers").children().first().addClass('active');

	$("#session-tickets").html(sessionTicketsTemplate({tickets:booking.selectedTickets}));
	$("#session-tickets").append(sessionPackagesTemplate({packages:booking.selectedPackages}));
	$("#session-tickets").children().first().addClass('active');

	$('#session-filters').submit();
});

$('#session-tab').on('click', '#session-tickets > a', function() {
	setTimeout(function() {
		$('#session-filters').submit();
	}, 50); // Need to give the browser time to set the .active class on the clicked list-item first
});

$('#session-tab').on('submit', '#session-filters', function(e) {
	e.preventDefault();

	$('#session-filters [type=submit]').html('Filter <i class="fa fa-cog fa-spin"></i>');

	var params = $(this).serializeObject();
	if( $('#session-tickets .active').length !== 0 )
		params.ticket_id = $('#session-tickets .active').first().data('id');

	redrawSessionsList(params);
});

$('#session-tab').on('click', '.assign-session', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Assigning...');
	btn.addClass('waiting');

	var package_id  = $('#session-tickets').children('.active').first().data('package-id');
	var ticket_id   = $('#session-tickets').children('.active').first().data('id');
	var customer_id = $('#session-customers').children('.active').first().data('id');
	var session_id  = btn.data('id');

	var params = {};
	params._token      = window.token;
	params.customer_id = customer_id;
	if(package_id) params.package_id = package_id;
	params.ticket_id   = ticket_id;
	params.session_id  = session_id;

	// Determine if we need to submit a boatroom_id
	var session = window.sessions[session_id];
	var trip    = window.trips[session.trip_id];

	var start = moment(session.start);
	var end   = moment(start).add(trip.duration, 'hours');

	if(start.format('YYYY-MM-DD') !== end.format('YYYY-MM-DD')) {
		// The trip is overnight

		var ticket  = window.tickets[ticket_id];

		var boatroomDetermined = false;
		var boatBoatrooms   = _.pluck(session.boat.boatrooms, 'id');
		var ticketBoatrooms = _.pluck(ticket.boatrooms, 'id');
		var intersectingBoatrooms = [];

		if(boatBoatrooms.length === 1)
			boatroomDetermined = true;
		else if(ticketBoatrooms.length > 0) {
			intersectingBoatrooms = _.intersection(boatBoatrooms, ticketBoatrooms);
			if(intersectingBoatrooms.length === 1)
				boatroomDetermined = true;
		}

		if(boatroomDetermined === false) {
			// If the boatroom could not be determined, we need to ask the user:
			var boatrooms = {};
			if(intersectingBoatrooms.length > 0) {
				boatrooms = _.map(intersectingBoatrooms, function(value) {
					return window.boatrooms[value];
				});
			} else {
				boatrooms = session.boat.boatrooms;
			}

			$('#modalWindows')
			.append( boatroomModalTemplate({boatrooms: boatrooms}) )     // Create the modal
			.children('#modal-boatroom-select')             // Directly find it and use it
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
						this.btn.html('Assign');            // Reset the button
					else
						delete window.sw.modalClosedBySelection;

					$('#modal-boatroom-select').remove();   // Remove the modal from the DOM
				}
			});
		}
	} else {
		submitAddDetail(params);
	}
});

$('#modalWindows').on('click', '.boatroom-select-option ', function(event) {
	var modal = $(event.target).closest('.reveal-modal');
	var params = modal.data('params');
	params.boatroom_id = $(event.target).data('id');

	submitAddDetail(params);

	// Close modal window
	window.sw.modalClosedBySelection = true;
	$('#modal-boatroom-select .close-reveal-modal').click();
});

function submitAddDetail(params) {
	booking.addDetail(params, function success(status, customer_id) {
		// $('.free-spaces[data-id="' + params.session_id + '"]').html('<i class="fa fa-refresh fa-spin"></i>');

		var params = $("#session-filters").serializeObject();
		if( $('#session-tickets .active').length !== 0 ) {
			params.ticket_id = $('#session-tickets .active').first().data('id');
		}

		redrawSessionsList(params);

		//List customer's bookingdetails in selectedCustomers for accommodations tab
		var details = _.filter(booking.bookingdetails, function (detail) {
		    return detail.customer.id == customer_id;
		});

		//booking.selectedCustomers[customer_id].bookingdetails = details;
		//booking.store();

		$('#booking-summary').html(bookingSummaryTemplate(booking));

	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		var btn = $('#sessions-table .waiting');
		btn.html('Assign');
		btn.removeClass('waiting');
	});
}

$('#booking-summary').on('click', '.unassign-session', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Unassigning...');

	var params = {};
	params._token           = window.token;
	params.bookingdetail_id = $(this).data('id');

	booking.removeDetail(params, function success() {
		var params = $("#session-filters").serializeObject();
		if( $('#session-tickets .active').length !== 0 )
			params.ticket_id = $('#session-tickets .active').first().data('id');

		redrawSessionsList(params);

		$('#booking-summary').html(bookingSummaryTemplate(booking));
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		btn.html('Unassign');
	});

});

//TODO: Work out how to take below into account with new tab navigation
window.promises.loadedAddons.done(function() {
	window.promises.loadedAccommodations.done(function() {
		$('#session-tab').on('click', '.sessions-finish', function() {
			if(_.size(window.addons) > 0) {
				$('[data-target="#addon-tab"]').tab('show');
			}else if(_.size(window.accommodations) > 0){
				$('[data-target="#accommodation-tab"]').tab('show');
			}else{
				$('[data-target="#extra-tab"]').tab('show');
			}
		});
	});
});

/*
************************
******** Addons ********
************************
*/

var addonBookingDetailsTemplate = Handlebars.compile($("#addon-booking-details-template").html());

$('[data-target="#addon-tab"]').on('show.bs.tab', function () {
	$("#addon-booking-details").html(addonBookingDetailsTemplate({details:booking.bookingdetails}));
	$("#addon-booking-details").children().first().addClass('active');
});

var addonTotal = 0;
$('#addon-tab').on('click', '.add-addon', function() {
	var btn = $(this);

	btn.html('<i class="fa fa-cog fa-spin"></i> Adding...');

	var params = {};
	params._token           = window.token;
	params.bookingdetail_id = $('#addon-booking-details').children('.active').first().data('id');
	params.addon_id         = $(this).data('id');
	params.quantity         = $('.addon-qty[data-id="'+$(this).data('id')+'"]').val();

	booking.addAddon(params, function success() {
		$('#booking-summary').html(bookingSummaryTemplate(booking));
		btn.html('Add');
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		btn.html('Add');
	});
});

$('#booking-summary').on('click', '.remove-addon', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Removing...');

	var params = {};
	params._token           = window.token;
	params.bookingdetail_id = $(this).data('bookingdetail-id');
	params.addon_id         = $(this).data('id');

	booking.removeAddon(params, function success() {
		$('#booking-summary').html(bookingSummaryTemplate(booking));
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		btn.html('Remove');
	});
});

window.promises.loadedAccommodations.done(function() {
	$('#addon-tab').on('click', '.addon-finish', function() {
		if(_.size(window.accommodations) > 0){
			$('[data-target="#accommodation-tab"]').tab('show');
		}else{
			$('[data-target="#extra-tab"]').tab('show');
		}
	});
});

/*
*************************
***** Accommodation *****
*************************
*/

var accommodationCustomersTemplate = Handlebars.compile($("#accommodation-customers-template").html());

$('[data-target="#accommodation-tab"]').on('show.bs.tab', function () {
	$("#accommodation-customers").html(accommodationCustomersTemplate({customers:booking.selectedCustomers}));
	$("#accommodation-customers").children().first().addClass('active');
});

$('#accommodation-tab').on('click', '.accommodation-customer', function() {
	var start = $(this).find('.session-start').data('date');

	//Get day before and remove time.
	var date = moment(start).subtract(1, 'days').format('YYYY-MM-DD');

	//Update all accommodation start fields.
	$('.accommodation-start').val(date);
});

$('#accommodation-tab').on('click', '.add-accommodation', function() {
	var btn = $(this);
	btn.prepend('<i class="fa fa-cog fa-spin"></i>&nbsp;');

	var params = {};
	params._token           = window.token;
	params.accommodation_id = $(this).data('id');
	params.customer_id      = $('#accommodation-customers').children('.active').first().data('id');
	params.start            = $(this).parents('.accommodation-item').find('[name="start"]').val();
	params.end              = $(this).parents('.accommodation-item').find('[name="end"]').val();

	booking.addAccommodation(params, function success() {
		$('#booking-summary').html(bookingSummaryTemplate(booking));
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		btn.html("Add");
	});
});

$('#booking-summary').on('click', '.remove-accommodation', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Removing...');

	var accommodation_id = $(this).data('id');
	var customer_id      = $(this).data('customer-id');

	var params = {};
	params._token           = window.token;
	params.accommodation_id = accommodation_id;
	params.customer_id      = customer_id;

	booking.removeAccommodation(params, function success() {
		$('#booking-summary').html(bookingSummaryTemplate(booking));
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		btn.html('Remove');
	});
});

/*
*************************
****** Extra Info *******
*************************
*/

$('[data-target="#extra-tab"]').on('show.bs.tab', function () {
});

$('#extra-tab').on('submit', '#extra-form', function(e) {
	e.preventDefault();

	var btn = $(this).find('[type="submit"]');
	btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

	var params = $(this).serializeObject();
	params._token = window.token;

	booking.editInfo(params, function success(status) {
		btn.html('Next');
		$('[data-target="#summary-tab"]').tab('show');
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		btn.html('Next');
	});
});

/*
*************************
******** Summary ********
*************************
*/


var summaryBookingDetailsTemplate = Handlebars.compile($("#summary-booking-details-template").html());
var summaryAccommodationsTemplate = Handlebars.compile($("#summary-accommodations-template").html());
var summaryLeadTemplate           = Handlebars.compile($("#summary-lead-template").html());
var summaryPriceTemplate          = Handlebars.compile($("#summary-price-template").html());

$('[data-target="#summary-tab"]').on('show.bs.tab', function () {
	$("#summary-booking-details").html(summaryBookingDetailsTemplate({bookingdetails:booking.bookingdetails}));
	$("#summary-accommodations").html(summaryAccommodationsTemplate({accommodations:booking.accommodations}));
	$("#summary-lead").html(summaryLeadTemplate(booking.lead_customer));
	$("#summary-price").html(summaryPriceTemplate(booking));

});

$('#summary-tab').on('click', '.save-booking', function() {

	var params = {};
	params._token = window.token;

	booking.save(params, function success(status) {
		pageMssg("Booking saved successfully!", "success");
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
	});

});

$('#summary-tab').on('submit', '#reserve-booking', function(event) {
	event.preventDefault();

	var params = $(this).serializeObject();
	var reserved = params.reserved;

	params.reserved = moment().add(reserved.substr(0, 2), 'hours').add(reserved.substr(3, 2), 'minutes').format('YYYY-MM-DD HH:mm:ss'); // MUST be SQL date format
	params._token   = window.token;

	booking.reserve(params, function success(status) {
		pageMssg("Booking reserved successfully!", "success");
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], "danger");
	});

});

/*
***************************
**** Functions & Other ****
***************************
*/

$(document).ready(function() {

	$('#agent-info').hide();
	$('#booking-summary-column').hide();
	$('#booking-area-column').removeClass('col-md-9').addClass('col-md-12');
	$('#existing-customers').select2();
	$('#trips').select2();
	$('#country_id').select2();

	$('#pick-up-location').typeahead({
		items: 'all',
		minLength: 3,
		delay: 250,
		autoSelect: false,
		source: function(query, process) {
			$('#pick-up-location').siblings().filter('.input-group-addon').html('<i class="fa fa-cog fa-spin"></i>');
			Booking.pickUpLocations({ query: query }, function success(data) {
				window.pick_up_locations = data;
				var items = Object.keys(data);
				items = _.map(items, function(item) {
					return window.pick_up_locations[item].substr(0, 5) + ' ' + item;
				});
				process( items );
				$('#pick-up-location').siblings().filter('.input-group-addon').html('<i class="fa fa-search"></i>');
			});
		},
		updater: function(item) {
			return item.substr(6);
		},
		afterSelect: function() {
			$('#pick-up-time').val( window.pick_up_locations[ $('#pick-up-location').val() ] );
		}
	});

	//This function runs whenever a new step has loaded
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		booking.currentTab = $(e.target).data('target');
		booking.store();

		if(!$(e.target).hasClass('done') && !$(e.target).hasClass('selected')) {
			$(e.target).parent().prevAll().children().removeClass('selected').addClass('done');
			$(e.target).addClass('selected').tab('show');
		}

		if(booking.currentTab !== '#source-tab') {
			$('[data-target="#source-tab"]').removeAttr("data-toggle");
			$('#booking-area-column').removeClass('col-md-12').addClass('col-md-9');
			$('#booking-summary-column').show();
		}

	});

	$('.btn-prev').on('click', function() {
	    $('.nav-wizard li').filter('.active').prev('li').find('a[data-toggle="tab"]').tab('show');
	});

	$('.btn-next').on('click', function() {
	    $('.nav-wizard li').filter('.active').next('li').find('a[data-toggle="tab"]').tab('show');
	});

	$('a[data-toggle="tab"]').on('click', function () {
		if(!$(this).hasClass('done') && !$(this).hasClass('selected')) {
			return false;
		}
	});

	$(document).on('click', '.list-group-radio', function() {
		listGroupRadio($(this));
	});

	$('#booking-summary').on('click', '.accordian-heading', function() {
		if($(this).find('.expand-icon').hasClass('fa-plus-square-o')) {
			$(this).find('.expand-icon').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
		}else{
			$(this).find('.expand-icon').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
		}
	});

	$('#booking-summary').on('click', '.list-expand', function() {
		if($(this).hasClass('fa-plus-square-o')) {
			$(this).removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
			$(this).parents('.list-group-expandable').children().not('list-group-heading').slideDown();
		}else{
			$(this).removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
			$(this).parents('.list-group-expandable').children().not('list-group-heading').slideUp();
		}
		

	});

	$('.alert-container').remove();

});

function redrawSessionsList(params) {

	if(typeof(params) === 'undefined') params = "";

	var sessionsTemplate = Handlebars.compile($("#sessions-table-template").html());

	Session.filter(params, function(data){
		window.sessions = _.indexBy(data, 'id');
		$("#sessions-table tbody").html(sessionsTemplate({sessions:data}));

		window.promises.loadedBoatrooms.done(function() {
			// Generate popovers
			_.each(window.sessions, function(session) {
				var html = '<table>';
				_.each(session.capacity[2], function(capacity, key) {
					html += '<tr>';
					html += 	'<td>' + window.boatrooms[key].name + '</td>';
					html += 	'<td>' + generateFreeSpacesBar(capacity, session.id).toString() + '</td>';
					html += '</tr>';
				});
				html += '</table>';
				$('.percentage-bar-container[data-id=' + session.id + ']').popover({
					title: 'Free spaces by cabin',
					content: html,
					html: true,
					placement: 'top',
					trigger: 'hover',
				});
			});
		});

		$('#session-filters [type=submit]').html('Filter');
	});
}

function listGroupRadio(selector, additionalClass) {
	//This function treats list-group-items (http://getbootstrap.com/components/#list-group) like radios buttons
	selector.siblings().removeClass('active '+additionalClass, selector.siblings().hasClass('active '+additionalClass));
	selector.toggleClass('active '+additionalClass, !selector.siblings().hasClass('active '+additionalClass));
}

function friendlyDate(date) {
	return moment(date).format('DD/MM/YYYY HH:mm');
}

function addTransaction() {
	window.clickedEdit = true;
	window.location.hash = 'add-transaction';
}

Booking.initiateStorage();

// Check if the booking variable exists and if so, load it
if(typeof booking !== 'undefined') {
	booking.loadStorage();

	if(Object.keys(booking.selectedTickets).length === 0) {
		// Load selectedTickets from bookingdetails
		_.each(booking.bookingdetails, function(detail) {
			if(typeof booking.selectedTickets[detail.ticket.id] === 'undefined') {
				booking.selectedTickets[detail.ticket.id] = detail.ticket;
				booking.selectedTickets[detail.ticket.id].qty = 1;
			} else {
				booking.selectedTickets[detail.ticket.id].qty++;
			}
		});
		booking.store();
	}

	if(Object.keys(booking.selectedCustomers).length === 0) {
		// Load selectedCustomers from bookingdetails
		_.each(booking.bookingdetails, function(detail) {
			if(typeof booking.selectedCustomers[detail.customer.id] === 'undefined') {
				booking.selectedCustomers[detail.customer.id] = $.extend(true, {}, detail.customer);
				booking.selectedCustomers[detail.customer.id].bookingdetails = [];
			}

			booking.selectedCustomers[detail.customer.id].bookingdetails.push($.extend(true, {}, detail));
		});
		booking.store();
	}

	if(booking.currentTab === null) {
		booking.currentTab = '#ticket-tab';
		if(Object.keys(booking.selectedTickets).length > 0) booking.currentTab = '#customer-tab';
		if(Object.keys(booking.selectedCustomers).length > 0) booking.currentTab = '#session-tab';
		if(booking.bookingdetails.length > 0) booking.currentTab = '#summary-tab';

		booking.store();
	}

	$('[data-target="'+booking.currentTab+'"]').tab('show');
}
