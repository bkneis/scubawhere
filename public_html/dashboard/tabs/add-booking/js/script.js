
window.promises = {};

window.promises.loadedToken = $.Deferred();
getToken(function callback() {
	window.promises.loadedToken.resolve();
});

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
	var freeSpaces = capacity[1] ? capacity[1] - capacity[0] : '∞';
	var percentage = capacity[1] ? (capacity[0] / capacity[1]) * 100 : 0;

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
	startDate = friendlyDate(start);
	endDate   = friendlyDate( moment(start).add(duration, 'hours') );

	if(startDate.substr(0, 11) === endDate.substr(0, 11))
		// Only return the time, if the date is the same
		return endDate.substr(12);
	else
		// Only return the date and the Month (and time)
		return endDate.substr(0, 6) + ' ' + endDate.substr(12);
});

Handlebars.registerHelper("friendlyDate", function(date) {
	return friendlyDate(date);
});

Handlebars.registerHelper("friendlyDateNoTime", function(date) {
	return friendlyDateNoTime(date);
});

Handlebars.registerHelper("firstChar", function(s) {
	return s[0];
});

Handlebars.registerHelper("isLead", function (customer, options) {
	return (booking.lead_customer && booking.lead_customer.id == customer.id) ? options.fn(customer) : options.inverse(customer);
});

Handlebars.registerHelper("notEmptyObj", function (item, options) {
	return $.isEmptyObject(item) ? options.inverse(this) : options.fn(this);
});

Handlebars.registerHelper('pricerange', function(base_prices, prices) {
	var min = 9007199254740992, // http://stackoverflow.com/questions/307179/what-is-javascripts-highest-integer-value-that-a-number-can-go-to-without-losin
	    max = 0;

	if( base_prices.length === 1 && prices.length === 0) {
		return window.company.currency.symbol + ' ' + base_prices[0].decimal_price;
	}

	_.each(base_prices, function(value) {
		min = Math.min(value.decimal_price, min).toFixed(2);
		max = Math.max(value.decimal_price, max).toFixed(2);
	});

	_.each(prices, function(value) {
		min = Math.min(value.decimal_price, min).toFixed(2);
		max = Math.max(value.decimal_price, max).toFixed(2);
	});

	return window.company.currency.symbol + ' ' + min + ' - ' + max;
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

Handlebars.registerHelper('qty', function(training_quantity) {
	if(this.qty)
		return this.qty;
	else if(this.pivot && this.pivot.quantity) {
		this.qty = this.pivot.quantity;
		return this.qty;
	}
	else if(training_quantity) {
		this.qty = training_quantity;
		return this.qty;
	}
	else alert('ERROR: Cannot determine qty!');
});

Handlebars.registerHelper('UID', function() {
	if(this.UID)
		return this.UID;

	this.UID = randomString();
	return this.UID;
});

Handlebars.registerHelper('real_decimal_price', function() {
	return (parseFloat(this.decimal_price) + parseFloat(this.discount)).toFixed(2);
});

/* Handlebars.registerHelper('addonMultiplyPrice', function(decimal_price, quantity) {
	return (parseFloat(decimal_price) * quantity).toFixed(2);
}); */

// Load all initial handlebars templates

var agentTemplate          = Handlebars.compile($("#agents-list-template").html());
var ticketTemplate         = Handlebars.compile($("#tickets-list-template").html());
var packageTemplate        = Handlebars.compile($("#package-list-template").html());
var courseTemplate         = Handlebars.compile($("#course-list-template").html());
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
	$("#agents-list").html(agentTemplate({agents: window.agents}));
	window.promises.loadedAgents.resolve();
});

window.promises.loadedTickets = $.Deferred();
Ticket.getAllTickets(function(data){
	window.tickets = _.indexBy(data, 'id');
	$("#tickets-list").html(ticketTemplate({tickets: window.tickets}));
	window.promises.loadedTickets.resolve();
});

window.promises.loadedPackages = $.Deferred();
Package.getAllPackages(function(data){
	window.packages = _.indexBy(data, 'id');
	$("#package-list").html(packageTemplate({packages: window.packages}));
	window.promises.loadedPackages.resolve();
});

window.promises.loadedCourses = $.Deferred();
Course.getAll(function(data){
	window.courses = _.indexBy(data, 'id');
	$("#course-list").html(courseTemplate({courses: window.courses}));
	window.promises.loadedCourses.resolve();
});

window.promises.loadedCustomers = $.Deferred();
Customer.getAllCustomers(function(data){
	window.customers = _.indexBy(data, 'id');
	$("#existing-customers").html(customersTemplate({customers: window.customers}));
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
else {
	$("#add-customer-countries").find('#country_id').html(countriesTemplate({countries:window.countries}));
	$("#edit-customer-countries").find('#country_id').html(countriesTemplate({countries:window.countries}));
	window.promises.loadedCountries.resolve();
}

window.promises.loadedTrips = $.Deferred();
Trip.getAllTrips(function(data){
	window.trips = _.indexBy(data, 'id');
	$("#trips").html(tripTemplate({trips: window.trips}));
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
	/* _.each(window.addons, function(addon) {
		addon.compulsory = parseInt(addon.compulsory);
	}); */
	$("#addons-list").html(addonsTemplate({addons: window.addons}));
	window.promises.loadedAddons.resolve();
});

window.promises.loadedAccommodations = $.Deferred();
Accommodation.getAll(function(data){
	window.accommodations = _.indexBy(data, 'id');
	$("#accommodations-list").html(accommodationsTemplate({accommodations: window.accommodations}));
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

window.promises.loadedToken.done(function() {
	$('#source-tab').on('click', '.source-finish', function() {

		//Get that cog spinning!
		$(this).html('<i class="fa fa-cog fa-spin"></i> Initiating...');

		//Find the source type that has been selected
		var type = $('.booking-source').children('.active').first().data("type");

		if(typeof type === "undefined") {
			pageMssg('Please select the source of the booking.', 'warning');
			$('.source-finish').html('Next');
			return false;
		}

		//If agent type selected, find the selected agent and prepare the ajax params
	  	var params = {};

		if(type == "agent") {
			var agentId   = $('#agents-list').children('.active').data('id');
			var reference = $('#agent-reference-' + agentId).val();
			params = {
				_token: window.token,
				agent_id: agentId,
				agent_reference: reference
			};
		} else {
			params = {
				_token: window.token,
				source: type
			};
		}

		if(type == "agent" && typeof(agentId) === 'undefined') {
			pageMssg('Please select an agent from the list to continue.', 'warning');
			$('.source-finish').html('Next');
			return false;
		}

		// Instantiate new Booking
		window.booking = new Booking();
		booking.initiate(params, function(status) {
			$('[data-target="#ticket-tab"]').tab('show');
			drawBasket();
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.errors[0], 'danger');
			$('.source-finish').html('Next');
		});
	});
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
			booking.selectedTickets[id] = $.extend(true, {}, window.tickets[id]);
			booking.selectedTickets[id].qty = 1;
		}

		booking.store();

		//Draw the basket
		drawBasket();
		pageMssg('<b>' + window.tickets[id].name + '</b> added to basket.', 'success');
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

		drawBasket();

		pageMssg('<b>' + window.tickets[id].name + '</b> removed from basket.', 'success');

		// If on trips screen, redraw session-ticket list
		if($('#session-tab').hasClass('active'))
			drawSessionTicketsList();
	});
});

window.promises.loadedPackages.done(function() {
	$('#ticket-tab').on('click', '.add-package', function() {

		var id = $(this).data('id');
		var UID = randomString();

		var package = $.extend(true, {}, window.packages[id]);

		package.UID = UID;

		// Individualise potentially contained courses
		if(package.courses) {
			var courses = $.extend(true, {}, package.courses);
			package.courses = {};

			for(var i = 0; i < Object.keys(courses).length; i++) {
				for(var j = 0; j < courses[i].pivot.quantity; j++) {
					courseUID = randomString();
					package.courses[courseUID] = $.extend(true, {}, courses[i]);
					package.courses[courseUID].UID = courseUID;
				}
			}
		}

		// Add package to selectedPackages
		booking.selectedPackages[UID] = package;

		booking.store();

		drawBasket();

		pageMssg('<b>' + window.packages[id].name + '</b> added to basket.', 'success');
	});

	$('#booking-summary').on('click', '.remove-package', function() {
		var UID = $(this).data('uid');
		var id  = booking.selectedPackages[UID].id;

		if(!UID) {
			pageMssg('<b>ERROR</b> Could not find package UID on click element!');
			return false;
		}

		// Remove from selected packages
		delete booking.selectedPackages[UID];

		booking.store();

		drawBasket();

		// If on trips screen, redraw session-ticket list
		if($('#session-tab').hasClass('active'))
			drawSessionTicketsList();

		pageMssg('<b>' + window.packages[id].name + '</b> removed from basket.', 'success');
	});
});

window.promises.loadedCourses.done(function() {
	$('#ticket-tab').on('click', '.add-course', function() {

		var id = $(this).data('id');
		var UID = randomString();

		// Add course to selectedCourses
		booking.selectedCourses[UID] = $.extend(true, {}, window.courses[id]);
		booking.selectedCourses[UID].UID = UID;

		booking.store();

		//Draw the basket
		drawBasket();

		pageMssg('<b>' + window.courses[id].name + '</b> added to basket.', 'success');
	});

	$('#booking-summary').on('click', '.remove-course', function() {
		var UID = $(this).data('uid');
		var id  = booking.selectedCourses[UID].id;

		if(!UID) {
			pageMssg('<b>ERROR</b> Could not find course UID on click element!');
			return false;
		}

		// Remove from selected courses
		delete booking.selectedCourses[UID];

		booking.store();

		drawBasket();

		// If on trips screen, redraw session-ticket list
		if($('#session-tab').hasClass('active'))
			drawSessionTicketsList();

		pageMssg('<b>' + window.courses[id].name + '</b> removed from basket.', 'success');
	});
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

		drawBasket();

		if( _.size(booking.selectedCustomers) === 1 ) {
			booking.setLead({_token: window.token, customer_id: id}, function success(status) {
				drawBasket();
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
	$("#edit-customer-details").html(editCustomerTemplate(window.customers[id]));

	//Set the country dropdown to the customers country (if they have one)
	$('#edit-customer-countries').find('#country_id').val(window.customers[id].country_id);
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
			drawBasket();
			// All good
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.errors[0], 'danger');
		});
	});

	delete booking.selectedCustomers[id];
	booking.store();

	drawBasket();

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
				drawBasket();
			}, function error(xhr) {
				var data = JSON.parse(xhr.responseText);
				pageMssg(data.errors[0], 'danger');
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
				pageMssg(data.errors[0], 'danger');
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

		Customer.updateCustomer(params, function success(data) {
			pageMssg(data.status, 'success');
			Customer.getCustomer("id="+params.id, function(data) {
				//Updated selectedCustomers data
				window.customers[params.id] = data;

				$("#existing-customers").html(customersTemplate({customers:window.customers}));

				booking.selectedCustomers[params.id] = window.customers[params.id];
				booking.store();
				booking.lead_customer = booking.selectedCustomers[params.id];

				btn.html('Save');
				$('#edit-customer-modal').modal('hide');

				drawBasket();
				$("#selected-customer").html(selectedCustomerTemplate(window.customers[params.id]));
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

		Customer.createCustomer(params, function success(data) {
			pageMssg(data.status, 'success');
			Customer.getCustomer("id="+data.id, function success(data) {

				window.customers[data.id] = data;

				$("#existing-customers").html(customersTemplate({customers:window.customers}));

				booking.selectedCustomers[data.id] = window.customers[data.id];
				booking.store();

				btn.html('Add');
				form[0].reset();

				drawBasket();

				if( _.size(booking.selectedCustomers) === 1 ) {
					booking.setLead({_token: window.token, customer_id: data.id}, function success(status) {
						booking.store();
						drawBasket();
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
	$(this).closest('form')[0].reset();
});

$('#booking-summary').on('click', '.lead-customer', function() {
	booking.setLead( {_token: window.token, customer_id: $(this).data('id')}, function success(status) {
		drawBasket();
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
	});
});

$('[data-target="#session-tab"]').on('show.bs.tab', function (e) {
  if(!booking.lead_customer) {
		pageMssg("Please designate a lead customer.", "danger");
		$('[data-target="#session-tab"]').data('validated', false);
		return false;
	}
	if(!booking.lead_customer.email) {
		pageMssg("The lead customer requires an email!", "danger");
		$('[data-target="#session-tab"]').data('validated', false);
		return false;
	}
	if(!booking.lead_customer.phone) {
		pageMssg("The lead customer requires a phone number!", "danger");
		$('[data-target="#session-tab"]').data('validated', false);
		return false;
	}
	if(!booking.lead_customer.country_id) {
		pageMssg("The lead customer requires a country!", "danger");
		$('[data-target="#session-tab"]').data('validated', false);
		return false;
	}

	$('[data-target="#session-tab"]').data('validated', true);
});

/*
*************************
******* Sessions *******
*************************
*/

var sessionCustomersTemplate = Handlebars.compile($("#session-customers-template").html());
var sessionTicketsTemplate   = Handlebars.compile($("#session-tickets-template").html());
var sessionPackagesTemplate  = Handlebars.compile($("#session-packages-template").html());
var sessionCoursesTemplate   = Handlebars.compile($("#session-courses-template").html());

$('[data-target="#session-tab"]').on('show.bs.tab', function (e) {
	if($('[data-target="#session-tab"]').data('validated') === false) return false;

	$("#session-customers").html(sessionCustomersTemplate({customers:booking.selectedCustomers}));
	$("#session-customers").children().first().addClass('active');

	drawSessionTicketsList();

	// $('#session-filters').submit();
});

$('#session-tab').on('click', '#session-tickets .list-group-item', function() {
	setTimeout(function() {
		$('#session-filters').submit();
	}, 50); // Need to give the browser time to set the .active class on the clicked list-item first
});

$('#session-tab').on('submit', '#session-filters', function(e) {
	e.preventDefault();

	$('#session-filters [type=submit]').html('Filter <i class="fa fa-cog fa-spin"></i>');

	var params = $(this).serializeObject();
	if( $('#session-tickets .active').length > 0 ) {
		var data = $('#session-tickets .active').first().data();
		if(data.type === 'ticket') {
			params.ticket_id = data.id;

			if(data.parent === 'package')
				params.package_id = data.parentId;
			else if(data.parentParent === 'package')
				params.package_id = data.parentParentId;
		}
		else if(data.type === 'training') {
			params.training_id = data.id;
		}
		else {
			pageMssg('ERROR Type could not be determined!', 'danger');
			return false;
		}

		if(data.parent === 'course')
			params.course_id = data.parentId;

		params.type = data.type;

		redrawSessionsList(params);
	}
	else {
		redrawSessionsList('This string makes the session list show the "Your search did not match any trips." message.'); // Hack? :P
	}
});

$('#session-tab').on('click', '.assign-session', function() {
	var btn = $(this);

	btn.html('<i class="fa fa-cog fa-spin"></i> Assigning...');
	btn.addClass('waiting');

	var $selected = $('#session-tickets').find('.active').first();
	var data = $selected.data();

	if(!data || !data.id) {
		pageMssg('Please select a ticket or class.', 'warning');
		btn.html('Assign');
		return false;
	}

	var params = {};
	params.customer_id = $('#session-customers').children('.active').first().data('id');

	if(data.parentParent || data.parent === 'course') {
		// Is a ticket or class in a course (and maybe even in a package)

		// Check if course identifier is given and if so, check if the customer is the same
		if(data.identifier) {
			var ownerId = data.identifier.split('-')[1];
			if(ownerId != params.customer_id) {
				pageMssg('This course is <b>already assigned</b> to <u>' + booking.selectedCustomers[ownerId].firstname + ' ' + booking.selectedCustomers[ownerId].lastname + '</u> and cannot be assigned to another customer.', 'danger', true);
				return false;
			}
		}
		else {
			// Generate course identifier for next time
			var identifier = booking.id + '-' + params.customer_id + '-' + data.parentId;

			// Add identifier to the course
			if(data.parentParent) {
				booking.selectedPackages[data.parentParentUid].courses[data.parentUid].identifier = identifier;
			}
			else {
				booking.selectedCourses[data.parentUid].identifier = identifier;
			}
		}

		params.course_id  = data.parentId;

		if(data.parentParent)
			params.package_id = data.parentParentId;
	}
	else if(data.parent === 'package')
		params.package_id = data.parentId;

	if(data.packagefacade)
		params.packagefacade_id = data.packagefacade;

	if(data.type === 'ticket') {
		params.ticket_id  = data.id;
		params.session_id = btn.data('id');
	}
	else
		params.training_session_id = btn.data('id');

	params._token = window.token;

	if(data.type === 'ticket') {
		// Determine if we need to submit a boatroom_id
		var session = window.sessions[params.session_id];
		var trip    = window.trips[session.trip_id];

		var start = moment(session.start);
		var end   = moment(start).add(trip.duration, 'hours');

		if(start.format('YYYY-MM-DD') !== end.format('YYYY-MM-DD')) {
			// The trip is overnight

			var ticket  = window.tickets[params.ticket_id];

			var boatBoatrooms   = _.pluck(session.boat.boatrooms, 'id');
			var ticketBoatrooms = _.pluck(ticket.boatrooms, 'id');
			var intersectingBoatrooms = [];

			if(boatBoatrooms.length === 1) {
				submitAddDetail(params, data);
				return;
			}

			if(ticketBoatrooms.length > 0) {
				intersectingBoatrooms = _.intersection(boatBoatrooms, ticketBoatrooms);
				if(intersectingBoatrooms.length === 1) {
					boatroomDetermined = true;
					submitAddDetail(params, data);
					return;
				}
			}

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
			.data('params', params)                         // Assign the params to the modal DOM element
			.data('data', data)                             // Assign the data to the modal DOM element
			.reveal({                                       // Open modal window | Options:
				animation: 'fadeAndPop',                    // fade, fadeAndPop, none
				animationSpeed: 300,                        // how fast animtions are
				closeOnBackgroundClick: true,               // if you click background will modal close?
				dismissModalClass: 'close-modal',           // the class of a button or element that will close an open modal
				btn: btn,                                   // Submit by reference to later get it as this.btn for resetting
				onFinishModal: function() {
					// Aborted action
					if(!window.sw.modalClosedBySelection) {
						this.btn.html('Assign');            // Reset the button
					} else {
						delete window.sw.modalClosedBySelection;
					}

					$('#modal-boatroom-select').remove();   // Remove the modal from the DOM
				}
			});

			return false;
		}
	}

	submitAddDetail(params, data);
});

$('#modalWindows').on('click', '.boatroom-select-option', function(event) {
	var modal  = $(event.target).closest('.reveal-modal');
	var params = modal.data('params');
	var data   = modal.data('data');
	params.boatroom_id = $(event.target).data('id');

	submitAddDetail(params, data);

	// Close modal window
	window.sw.modalClosedBySelection = true;
	$('#modal-boatroom-select .close-reveal-modal').click();
});

function submitAddDetail(params, data) {
	console.info('Add-detail params:');
	console.log(params);

	booking.addDetail(params, function success(status, packagefacade_id) {
		// $('.free-spaces[data-id="' + params.session_id + '"]').html('<i class="fa fa-refresh fa-spin"></i>');

		// var params = $("#session-filters").serializeObject();
		// if( $('#session-tickets .active').length !== 0 ) {
		// 	params.ticket_id = $('#session-tickets .active').first().data('id');
		// }

		// redrawSessionsList(params); // Now triggered by drawSessionTicketsList()

		//List customer's bookingdetails in selectedCustomers for accommodations tab
		/*var details = _.filter(booking.bookingdetails, function (detail) {
		    return detail.customer.id == customer_id;
		});*/

		//booking.selectedCustomers[customer_id].bookingdetails = details;
		//booking.store();

		// Validate and assign packagefacade_id, if returned
		if(packagefacade_id) {
			// If a packagefacade_id already exists, validate that they are the same
			if(params.packagefacade_id && params.packagefacade_id != packagefacade_id) {
				pageMssg('<b>ERROR: UNDEFINED BEHAVIOR</b> Submitted and returned packagefacade_id do not match!', 'danger', true);
				return false;
			}

			var packageUID;
			if(data.parentParent) // Is a course in a package
				packageUID = data.parentParentUid;
			else // Is a package
				packageUID = data.parentUid;

			booking.selectedPackages[packageUID].packagefacade = packagefacade_id;
		}

		// Reduce selected quantity
		var parentPointer = booking;
		var itemPointer = null;
		if(packageUID) {
			// It's a top-level package
			parentPointer = parentPointer.selectedPackages[packageUID];
		}
		if(data.parent === 'course') {
			if(data.parentParent)
				// It's a course in a package
				parentPointer = parentPointer.courses[data.parentUid];
			else
				// It's a top-level course
				parentPointer = parentPointer.selectedCourses[data.parentUid];
		}
		if(data.type === 'ticket') {
			if(data.parent)
				// It's a ticket in a course or package
				itemPointer = _.find(parentPointer.tickets, function(ticket) {
					return ticket.id === data.id;
				});
			else
				// It's a top-level ticket
				itemPointer = parentPointer.selectedTickets[data.id];
		}
		if(data.type === 'training') {
				// It's a training in a course
				itemPointer = parentPointer.training;
		}

		itemPointer.qty--;

		// Check if quantity is now 0 and if so, remove item from parent
		if(itemPointer.qty <= 0) {
			if(data.type === 'ticket') {
				if(data.parent)
					parentPointer.tickets = _.reject(parentPointer.tickets, function(ticket) {
						return ticket.qty === 0;
					});
				else
					delete parentPointer.selectedTickets[data.id];
			}
			if(data.type === 'training') {
				delete parentPointer.training;
			}
		}

		// Check if parent still contains something unassigned, otherwise remove parent
		// TODO ... need parentParentPointer... :$

		booking.store();

		$('#sessions-table .waiting').removeClass('waiting').html('Assign');

		drawSessionTicketsList();

		drawBasket(function() {
			$('[data-parent="#booking-summary-trips"]').last().trigger('click'); //When basket has been refreshed, expand latest bookingdetail
		});

	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		$('#sessions-table .waiting').removeClass('waiting').html('Assign');
	});
}

function drawSessionTicketsList() {
	var $list = $("#session-tickets");
	$list.html(sessionTicketsTemplate({tickets:booking.selectedTickets}));
	$list.append(sessionPackagesTemplate({packages:booking.selectedPackages}));
	$list.append(sessionCoursesTemplate({courses:booking.selectedCourses}));

	var firstItem = $list.find('.list-group-item').first();

	if(firstItem.length > 0) {
		firstItem.addClass('active').click();

		// Open the containers that contain the firstItem
		var panels = firstItem.parentsUntil('.panel-default', '.panel');

		panels.each(function() {
			$(this).find('.accordion-heading').click();
		});
	}

	// If the list is empty, submit the filter form anyway to show "the note"
	$('#session-filters').submit();
}

$('#booking-summary').on('click', '.unassign-session', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Unassigning...');

	var params = {};
	params._token           = window.token;
	params.bookingdetail_id = $(this).data('id');

	booking.removeDetail(params, function success(status, detail) {
		// First, take care of attached addons that need to be re-added to a selectedPackage
		if(detail.ticket) reAddPackagedAddons(detail.addons);

		// Re-add a removed item to selectedTickets/selectedCourses/selectedPackages
		do { // Enclose the whole procedure in a one-time do-while loop to be able to "break" out of it at any time.
			if(!detail.packagefacade) {
				// Not packaged
				if(!detail.course && detail.ticket) {
					// Is standalone ticket
					if(booking.selectedTickets[detail.ticket.id]) {
						booking.selectedTickets[detail.ticket.id].qty++;
					}
					else {
						detail.ticket.qty = 1;
						booking.selectedTickets[detail.ticket.id] = detail.ticket;
					}
				}
				else {
					// Is course
					var identifier = booking.id + '-' + detail.customer.id + '-' + detail.course.id;

					var relatedCourse = _.find(booking.selectedCourses, function(course) {
						return course.identifier === identifier;
					});

					if(relatedCourse === undefined) break;

					if(detail.ticket) {
						// Is ticket in course
						var existingTicket = _.find(relatedCourse.tickets, function(ticket) {
							return ticket.id == detail.ticket.id;
						});

						if(existingTicket !== undefined)
							existingTicket.qty++;
						else {
							detail.ticket.qty = 1;
							relatedCourse.tickets.push(detail.ticket);
						}
					}
					else {
						// Is class in course
						if(relatedCourse.training) {
							// training object exists
							relatedCourse.training.qty++;
						}
						else {
							detail.training_session.training.qty = 1;
							relatedCourse.training = detail.training_session.training;
						}
					}
				}
			}
			else {
				// Is packaged
				var relatedPackage = _.find(booking.selectedPackages, function(package) {
					return package.packagefacade == detail.packagefacade.id;
				});

				if(relatedPackage === undefined) break;

				if(!detail.course && detail.ticket) {
					// Is packaged ticket
					var existingTicket = _.find(relatedPackage.tickets, function(ticket) {
						return ticket.id == detail.ticket.id;
					});

					if(existingTicket !== undefined)
						existingTicket.qty++;
					else {
						detail.ticket.qty = 1;
						relatedPackage.tickets.push(detail.ticket);
					}
				}
				else {
					// Is course
					var identifier = booking.id + '-' + detail.customer.id + '-' + detail.course.id;

					var relatedCourse = _.find(relatedPackage.courses, function(course) {
						return course.identifier === identifier;
					});

					if(relatedCourse === undefined) break;

					if(detail.ticket) {
						// Is ticket in course in package
						var existingTicket = _.find(relatedCourse.tickets, function(ticket) {
							return ticket.id == detail.ticket.id;
						});

						if(existingTicket !== undefined)
							existingTicket.qty++;
						else {
							detail.ticket.qty = 1;
							relatedCourse.tickets.push(detail.ticket);
						}
					}
					else {
						// Is class in course in package
						if(relatedCourse.training) {
							// training object exists
							relatedCourse.training.qty++;
						}
						else {
							detail.training_session.training.qty = 1;
							relatedCourse.training = detail.training_session.training;
						}
					}
				}
			}
		} while(false);

		booking.store();

		drawSessionTicketsList();

		drawBasket();
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
var packagedAddonsListTemplate = Handlebars.compile($("#packaged-addons-list-template").html());

$('[data-target="#addon-tab"]').on('show.bs.tab', function () {
	setUpAddonsTab();
});

function setUpAddonsTab() {
	$("#addon-booking-details").html(addonBookingDetailsTemplate({details: booking.bookingdetails}));
	$("#addon-booking-details").children().first().addClass('active');

	updatePackagedAddonsList();
}

function updatePackagedAddonsList() {
	$("#packaged-addons-list-container").empty();

	// Find first addon in selected packages to check if the list needs to be rendered
	var packagedAddonsExist = _.find(booking.selectedPackages, function(package) {
		return package.addons && package.addons.length > 0;
	});

	if(packagedAddonsExist !== undefined)
		$("#packaged-addons-list-container").html(packagedAddonsListTemplate({packages: booking.selectedPackages}));
}

$('#addon-tab').on('click', '#packaged-addons-list-container .list-group-item', function() {
	setTimeout(function() {
		var addonPackagefacadeId = $('#packaged-addons-list-container .active').data('packagefacadeId');
		// Filter booked trips and display the ones that are in the same package as the clicked addon
		var eligableDetails = _.filter(booking.bookingdetails, function(detail) {
			return (detail.packagefacade && detail.packagefacade.id === addonPackagefacadeId && detail.training_session === null);
		});

		// Fetch currently selected detail ID
		var selectedID = $("#addon-booking-details .list-group-item.active").first().data('id');

		$("#addon-booking-details").html(addonBookingDetailsTemplate({details: eligableDetails}));

		// Automatically select the first eligable detail
		$("#addon-booking-details").children().first().addClass('active');

		// Try to find the previously selected detail ID in the eligable details and re-select it
		$("#addon-booking-details .list-group-item[data-id=" + selectedID + "]").first().click();

	}, 50); // Need to give the browser time to set the .active class on the clicked list-item first
});

$('#addon-tab').on('click', '.add-packaged-addon', function() {

	// Check if both list items are selected
	if( $('#addon-booking-details').children('.active').length < 1 || $('#packaged-addons-list').children('.active').length < 1 ) {
		pageMssg('Please select both a packaged add-on and a trip to assign it to.', 'info');
		return false;
	}

	var btn = $(this);
	var addon = $('#packaged-addons-list .active').first();

	btn.html('<i class="fa fa-cog fa-spin"></i> Adding...');

	var params = {};
	params._token           = window.token;
	params.bookingdetail_id = $('#addon-booking-details').children('.active').first().data('id');
	params.addon_id         = addon.data('id');
	params.quantity         = 1;
	params.packagefacade_id = addon.data('packagefacadeId');

	booking.addAddon(params, function success(status) {
		// Reduce qty
		for(var i = 0; i < booking.selectedPackages[addon.data('packageUid')].addons.length; i++) {
			if(booking.selectedPackages[addon.data('packageUid')].addons[i].id == params.addon_id) {
				booking.selectedPackages[addon.data('packageUid')].addons[i].qty--;

				// Check if qty is now 0, and if so remove the addon from the array
				if(booking.selectedPackages[addon.data('packageUid')].addons[i].qty === 0)
					booking.selectedPackages[addon.data('packageUid')].addons.splice(i, 1);
			}
		}

		booking.store();

		pageMssg(status, 'success');

		drawBasket();

		updatePackagedAddonsList();

		// Rerender session-list (to show all previously removed sessions as well)
		$("#addon-booking-details").html(addonBookingDetailsTemplate({details: booking.bookingdetails}));
		$("#addon-booking-details").children().first().addClass('active');

		btn.html('Add');
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		btn.html('Add');
	});
});

$('#addon-tab').on('click', '.add-addon', function() {
	var btn = $(this);

	btn.html('<i class="fa fa-cog fa-spin"></i> Adding...');

	var params = {};
	params._token           = window.token;
	params.bookingdetail_id = $('#addon-booking-details').children('.active').first().data('id');
	params.addon_id         = $(this).data('id');
	params.quantity         = $('.addon-qty[data-id="'+$(this).data('id')+'"]').val();

	booking.addAddon(params, function success(status) {
		booking.store();
		pageMssg(status, 'success');
		drawBasket();
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
	params.bookingdetail_id = $(this).data('bookingdetailId');
	params.addon_id         = $(this).data('id');
	if($(this).data('packagefacadeId'))
		params.packagefacade_id = $(this).data('packagefacadeId');

	booking.removeAddon(params, function success(status, removedAddon) {
		// If the addon was packaged, re-add it to the selected package (if it exists)
		reAddPackagedAddons([removedAddon]);

		pageMssg(status, 'success');

		booking.store();
		drawBasket();
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		btn.html('Remove');
	});
});

function reAddPackagedAddons(removedAddons) {
	_.each(removedAddons, function(removedAddon) {
		if(removedAddon.compulsory === 1) return;

		if(removedAddon.pivot.packagefacade_id) {
			var relatedPackage = _.find(booking.selectedPackages, function(package) {
				return package.packagefacade == removedAddon.pivot.packagefacade_id;
			});

			if(relatedPackage !== undefined) {
				// Check if the addon already exists in the package
				var existingAddon = _.find(relatedPackage.addons, function(addon) {
					return removedAddon.id == addon.id;
				});

				if(existingAddon !== undefined) {
					existingAddon.qty += removedAddon.pivot.quantity;
				}
				else {
					removedAddon.qty = removedAddon.pivot.quantity;
					relatedPackage.addons.push(removedAddon);
				}

				updatePackagedAddonsList();
			}
		}
	});
}

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
var packagedAccommodationsListTemplate = Handlebars.compile($("#packaged-accommodations-list-template").html());

$('[data-target="#accommodation-tab"]').on('show.bs.tab', function () {
	$("#accommodation-customers").html(accommodationCustomersTemplate({customers:booking.selectedCustomers}));
	$("#accommodation-customers").children().first().addClass('active');

	updatePackagedAccommodationsList();

	// Find first bookingdetail departure date
	var firstDepartureDate = _.first(
		_.map(booking.bookingdetails, function(detail) {
			if(detail.session !== null)
				return detail.session.start;
			else
				return detail.training_session.start;
		}).sort()
	);

	// Set all accommodations' start fields
	var startDate = moment(firstDepartureDate).subtract(1, 'days').format('YYYY-MM-DD');
	$('.accommodation-start').val(startDate);
	$('#packaged-accommodations-list .accommodation-start').change();
});

$('#accommodation-tab').on('change', '#packaged-accommodations-list .accommodation-start', function() {
	// Find the end date field
	var self = $(this);
	var endField = self.closest('.form-group').find('.accommodation-end').first();

	var endDate = moment(self.val()).add(self.data('qty'), 'days');

	endField.val(endDate.format('YYYY-MM-DD'));
});

$('#accommodation-tab').on('dp.show', '#accommodations-list .datepicker', function() {
	updateDatePickerMinMaxDates($(this).closest('.form-group').find('.accommodation-start').first(), false);
});

$('#accommodation-tab').on('dp.show', '#packaged-accommodations-list .datepicker', function() {
	updateDatePickerMinMaxDates($(this).closest('.form-group').find('.accommodation-start').first(), true);
});

function updateDatePickerMinMaxDates(startField, setMaxDate) {
	var endField = startField.closest('.form-group').find('.accommodation-end').first();

	startField.data('DateTimePicker').setMinDate(moment().subtract(1, 'days'));

	endField.data('DateTimePicker').setMinDate(moment(startField.val()).add(1, 'days'));

	if(setMaxDate) {
		var endDate = moment(startField.val()).add(startField.data('qty'), 'days');
		endField.data('DateTimePicker').setMaxDate(endDate);
	}
}

function updatePackagedAccommodationsList() {
	$("#packaged-accommodations-list-container").empty();

	// Find first accommodation in selected packages to check if the list needs to be rendered
	var packagedAccommodationsExist = _.find(booking.selectedPackages, function(package) {
		return package.accommodations && package.accommodations.length > 0;
	});

	if(packagedAccommodationsExist !== undefined) {
		$("#packaged-accommodations-list-container").html(packagedAccommodationsListTemplate({packages: booking.selectedPackages}));

		// Find first bookingdetail departure date
		var firstDepartureDate = _.first(
			_.map(booking.bookingdetails, function(detail) {
				if(detail.session !== null)
					return detail.session.start;
				else
					return detail.training_session.start;
			}).sort()
		);

		// Set all accommodations' start fields
		var startDate = moment(firstDepartureDate).subtract(1, 'days').format('YYYY-MM-DD');
		$('#packaged-accommodations-list .accommodation-start').val(startDate).change();

		$('#packaged-accommodations-list .datepicker').datetimepicker({
			pickDate: true,
			pickTime: false,
			icons: {
				time: 'fa fa-clock-o',
				date: 'fa fa-calendar',
				up:   'fa fa-chevron-up',
				down: 'fa fa-chevron-down'
			},
		});
	}
}

$('#accommodation-tab').on('click', '.add-packaged-accommodation', function() {
	var btn = $(this);
	btn.prepend('<i class="fa fa-cog fa-spin"></i>&nbsp;');

	var params = {};
	params._token           = window.token;
	params.accommodation_id = $(this).data('id');
	params.customer_id      = $('#accommodation-customers').children('.active').first().data('id');
	params.start            = $(this).closest('.accommodation-item').find('[name="start"]').val();
	params.end              = $(this).closest('.accommodation-item').find('[name="end"]').val();
	params.package_id       = booking.selectedPackages[btn.data('packageUid')].id;
	params.packagefacade_id = btn.data('packagefacadeId');

	booking.addAccommodation(params, function success(status, packagefacade_id) {

		if(packagefacade_id) {
			// If a packagefacade_id already exists, validate that they are the same
			if(params.packagefacade_id && params.packagefacade_id != packagefacade_id) {
				pageMssg('<b>ERROR: UNDEFINED BEHAVIOR</b> Submitted and returned packagefacade_id do not match!', 'danger', true);
				return false;
			}

			booking.selectedPackages[btn.data('packageUid')].packagefacade = packagefacade_id;
		}

		// Calculate how many nights where booked
		var quantity = moment(params.end).diff(moment(params.start), 'days');

		// Reduce qty
		for(var i = 0; i < booking.selectedPackages[btn.data('packageUid')].accommodations.length; i++) {
			if(booking.selectedPackages[btn.data('packageUid')].accommodations[i].id == params.accommodation_id) {
				booking.selectedPackages[btn.data('packageUid')].accommodations[i].qty -= quantity;

				// Check if qty is now 0, and if so remove the accommodation from the array
				if(booking.selectedPackages[btn.data('packageUid')].accommodations[i].qty === 0)
					booking.selectedPackages[btn.data('packageUid')].accommodations.splice(i, 1);

				break;
			}
		}

		booking.store();

		pageMssg(status, 'success');

		drawBasket();

		updatePackagedAccommodationsList();

		btn.html("Add");
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		btn.html("Add");
	});
});

$('#accommodation-tab').on('click', '.add-accommodation', function() {
	var btn = $(this);
	btn.prepend('<i class="fa fa-cog fa-spin"></i>&nbsp;');

	var params = {};
	params._token           = window.token;
	params.accommodation_id = $(this).data('id');
	params.customer_id      = $('#accommodation-customers').children('.active').first().data('id');
	params.start            = $(this).closest('.accommodation-item').find('[name="start"]').val();
	params.end              = $(this).closest('.accommodation-item').find('[name="end"]').val();

	booking.addAccommodation(params, function success(status) {
		booking.store();
		pageMssg(status, 'success');
		drawBasket();
		btn.html("Add");
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
	var customer_id      = $(this).data('customerId');

	var params = {};
	params._token           = window.token;
	params.accommodation_id = accommodation_id;
	params.customer_id      = customer_id;
	params.start            = $(this).data('start');

	booking.removeAccommodation(params, function success(status, removedAccommodation) {
		// If the accommodation was packaged, re-add it to the selected package (if it exists)
		if(removedAccommodation.pivot.packagefacade_id) {
			var relatedPackage = _.find(booking.selectedPackages, function(package) {
				return package.packagefacade == removedAccommodation.pivot.packagefacade_id;
			});

			if(relatedPackage !== undefined) {
				// Check if the accommodations already exists in the package
				var existingAccommodation = _.find(relatedPackage.accommodations, function(accommodations) {
					return removedAccommodation.id == accommodations.id;
				});

				// Calculate how many nights got removed
				var quantity = moment(removedAccommodation.pivot.end).diff(moment(removedAccommodation.pivot.start), 'days');

				if(existingAccommodation !== undefined) {
					existingAccommodation.qty += quantity;
				}
				else {
					removedAccommodation.qty = quantity;
					relatedPackage.accommodations.push(removedAccommodation);
				}

				updatePackagedAccommodationsList();
			}
		}

		booking.store();
		pageMssg(status, 'success');
		drawBasket();
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

var extraInfoTemplate = Handlebars.compile($("#extra-info-template").html());

Handlebars.registerHelper('discountPercentage', function() {

})

$('[data-target="#extra-tab"]').on('show.bs.tab', function () {
	updateExtraInfoPanel();
	$('#discount').change();
});

function updateExtraInfoPanel() {
	$('#extra-info-container').html(extraInfoTemplate(booking));

	$('#extra-info-container .datepicker').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});

	$('#extra-info-container .timepicker').datetimepicker({
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
}

$('#extra-tab').on('submit', '#extra-form', function(e) {
	e.preventDefault();

	var btn = $(this).find('[type="submit"]');
	btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

	var params = $(this).serializeObject();
	params._token = window.token;

	booking.editInfo(params, function success(status) {
		pageMssg(status, 'success');
		btn.html('Save');

		drawBasket();
	}, function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
		btn.html('Save');
	});
});

$('#extra-tab').on('change', '#discount-percentage', function(e) {
	$discount            = $('#discount');
	$discount_percentage = $(e.target);

	var originalPrice = parseFloat(booking.decimal_price) + parseFloat(booking.discount);

	$discount.val( (originalPrice * $discount_percentage.val() / 100).toFixed(2) );

	$('#discounted-price').html( window.company.currency.symbol + ' ' + (originalPrice - $discount.val()).toFixed(2) );
});

$('#extra-tab').on('change', '#discount', function(e) {
	$discount            = $(e.target);
	$discount_percentage = $('#discount-percentage');

	var originalPrice = parseFloat(booking.decimal_price) + parseFloat(booking.discount);

	$discount.val(parseFloat($discount.val()).toFixed(2));

	$discount_percentage.val( ($discount.val() / originalPrice * 100).toFixed(2) );

	$('#discounted-price').html( window.company.currency.symbol + ' ' + (originalPrice - $discount.val()).toFixed(2) );
});

/*
*************************
******** Summary ********
*************************
*/


// var summaryBookingDetailsTemplate = Handlebars.compile($("#summary-booking-details-template").html());
// var summaryAccommodationsTemplate = Handlebars.compile($("#summary-accommodations-template").html());
// var summaryLeadTemplate           = Handlebars.compile($("#summary-lead-template").html());
// var summaryPriceTemplate          = Handlebars.compile($("#summary-price-template").html());

var summaryTemplate = Handlebars.compile($('#summary-template').html());

$('[data-target="#summary-tab"]').on('show.bs.tab', function () {
	// $("#summary-booking-details").html(summaryBookingDetailsTemplate({bookingdetails:booking.bookingdetails}));
	// $("#summary-accommodations").html(summaryAccommodationsTemplate({accommodations:booking.accommodations}));
	// $("#summary-lead").html(summaryLeadTemplate(booking.lead_customer));
	// $("#summary-price").html(summaryPriceTemplate(booking));

	// Sort bookingdetails by start date
	booking.bookingdetails = _.sortBy(booking.bookingdetails, function(detail) {
		if(detail.session)
			return detail.session.start;
		else
			return detail.training_session.start;
	});

	// Sort accommodations by start date
	booking.accommodations = _.sortBy(booking.accommodations, function(accom) {
		return accom.pivot.start;
	});

	$('#summary-container').html(summaryTemplate(booking));
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

$('#summary-tab').on('click', '.confirm-booking', function() {

	var params = {};
	params._token = window.token;

	booking.confirm(params, function success(status) {
		pageMssg(status, "success");
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
			$('#pick-up-time').val( window.pick_up_locations[ $('#pick-up-location').val() ].substr(0, 5) );
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

		// When the tab is the Extra Info tab, first save the info, before showing the next tab
		// TODO

	    $('.nav-wizard li').filter('.active').next('li').find('a[data-toggle="tab"]').tab('show');
	});

	$('a[data-toggle="tab"]').on('click', function () {
		if(!$(this).hasClass('done') && !$(this).hasClass('selected')) {
			return false;
		}
	});

	$('#wrapper').on('click', '.list-group-radio', function() {
		listGroupRadio($(this));
	});

	$('#booking-summary').on('click', '.accordion-heading', function() {
		if($(this).find('.expand-icon').hasClass('fa-plus-square-o')) {
			$(this).find('.expand-icon').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
		}else{
			$(this).find('.expand-icon').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
		}
	});

	$('#booking-summary').on('click', '.list-expand', function() {
		if($(this).hasClass('fa-plus-square-o')) {
			$(this).removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
			$(this).closest('.list-group-expandable').children().not('list-group-heading').slideDown();
		}else{
			$(this).removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
			$(this).closest('.list-group-expandable').children().not('list-group-heading').slideUp();
		}
	});

	$('.alert-container').remove();

});

var sessionsTemplate = Handlebars.compile($("#sessions-table-template").html());
function redrawSessionsList(params) {

	console.info('Filter params:');
	console.log(params);

	if(typeof(params) === 'undefined') params = "";

	if(typeof(params) === 'string') {
		// Render empty list with a note saying someting like "Your search did not match any trips."
		// TODO Maybe make this note more related to the actual error?
		$("#sessions-table tbody").html(sessionsTemplate({sessions: []}));
		$("#sessions-table tbody").css('opacity', 1);
		$('#session-filters [type=submit]').html('Filter');
		return;
	}

	// First, make the list opaque so the user knows that it should not be clicked
	$("#sessions-table tbody").css('opacity', 0.3);

	var model;
	if(params.type === 'ticket')
		model = Session;
	else
		model = Class;

	model.filter(params, function(data) {
		if(params.type === 'ticket') {
			window.sessions = _.indexBy(data, 'id');
		}
		else {
			window.training_sessions = _.indexBy(data, 'id');
		}

		$("#sessions-table tbody").html(sessionsTemplate({sessions:data}));
		$("#sessions-table tbody").css('opacity', 1);

		if(params.type === 'ticket') {
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
		}

		$('#session-filters [type=submit]').html('Filter');
	});
}

function listGroupRadio(selector, additionalClass) {
	//This function treats list-group-items (http://getbootstrap.com/components/#list-group) like radios buttons
	selector.siblings().removeClass('active '+additionalClass, selector.siblings().hasClass('active '+additionalClass));
	selector.toggleClass('active '+additionalClass, !selector.siblings().hasClass('active '+additionalClass));
}

function friendlyDate(date) {
	// return moment(date).format('DD/MM/YYYY HH:mm');
	return moment(date).format('DD MMM YYYY HH:mm');
}

function friendlyDateNoTime(date) {
	// return moment(date).format('DD/MM/YYYY HH:mm');
	return moment(date).format('DD MMM YYYY');
}

function addTransaction() {
	window.clickedEdit = true;
	window.location.hash = 'add-transaction';
}

function drawBasket(doneFn) {
	// Sort bookingdetails by start date
	booking.bookingdetails = _.sortBy(booking.bookingdetails, function(detail) {
		if(detail.session)
			return detail.session.start;
		else
			return detail.training_session.start;
	});

	// Sort accommodations by start date
	booking.accommodations = _.sortBy(booking.accommodations, function(accom) {
		return accom.pivot.start;
	});

	$('#booking-summary').html(bookingSummaryTemplate(booking)).promise().done(function(){
	    if($.isFunction(doneFn)) doneFn();
	});
}

Booking.initiateStorage();

// Check if the booking variable exists and the user explicitly loaded it
if(typeof booking !== 'undefined' && typeof clickedEdit !== 'undefined' && clickedEdit === true) {
	window.clickedEdit = false;
	booking.loadStorage();

	if(Object.keys(booking.selectedCustomers).length === 0) {
		// Load selectedCustomers from bookingdetails
		_.each(booking.bookingdetails, function(detail) {
			if(typeof booking.selectedCustomers[detail.customer.id] === 'undefined') {
				booking.selectedCustomers[detail.customer.id] = $.extend(true, {}, detail.customer);
			}
		});

		// If their are still no customers (edge case) set currentTab to customer selection
		/*if(Object.keys(booking.selectedCustomers).length === 0) {
			booking.currentTab = '#customer-tab';
		}*/

		booking.store();
	}

	/**
	 * The system has been changed to remove selected tickets/packages/courses from the list when they are assigned.
	 * Thus we cannot recreate the selected* lists from the existing bookingdetails.
	 * Instead, new selections need to be made if new tickets/packages/courses should be added.

	// TODO Remove when selected tickets are removed from the array when they are assigned
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

		// If their are still no tickets (edge case) set currentTab to ticket selection
		if(Object.keys(booking.selectedTickets).length === 0) {
			booking.currentTab = '#ticket-tab';
		}

		booking.store();
	}

	if(Object.keys(booking.selectedPackages).length === 0) {
		// Load selectedPackages from bookingdetails
		_.each(booking.bookingdetails, function(detail) {
			if(typeof detail.packagefacade === 'undefined' || detail.packagefacade === null) return;

			if(typeof booking.selectedPackages[detail.packagefacade.package.id] === 'undefined') {
				booking.selectedPackages[detail.packagefacade.package.id] = detail.packagefacade.package;
				booking.selectedPackages[detail.packagefacade.package.id].tickets = detail.packagefacade.package.tickets;
				booking.selectedPackages[detail.packagefacade.package.id].qty = 1;
			} else {
				booking.selectedPackages[detail.packagefacade.package.id].qty++;
			}
		});

		booking.store();
	}
	*/

	if(booking.currentTab === null) {
		booking.currentTab = '#ticket-tab';
		// if(Object.keys(booking.selectedTickets).length > 0) booking.currentTab = '#customer-tab';
		// if(Object.keys(booking.selectedCustomers).length > 0) booking.currentTab = '#session-tab';
		if(booking.bookingdetails.length > 0) booking.currentTab = '#summary-tab';

		booking.store();
	}

	$('[data-target="'+booking.currentTab+'"]').tab('show');
	drawBasket();
}
