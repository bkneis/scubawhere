getToken();

Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});

Handlebars.registerHelper("freeSpaces", function(capacity) {
	var freeSpaces = capacity[1] - capacity[0];
	var percentage = (capacity[0] / capacity[1]) * 100;

	var color = '#5cb85c'; var bgClasses = 'bg-success border-success';
	if(percentage >= 75) { color = '#f0ad4e'; bgClasses = 'bg-warning border-warning'; }
	if(percentage == 1) { color = '#d9534f'; bgClasses = 'bg-danger border-danger'; }

	var html = '';
	html += '<div data-id="' + this.id + '" class="percentage-bar-container ' + bgClasses + '">';
	html += '	<div class="percentage-bar" style="background-color: ' + color + '; width: ' + percentage + '%">&nbsp;</div>';
	html += '	<span class="percentage-left">' + freeSpaces + '</span>';
	html += '</div>';

	return new Handlebars.SafeString(html);
});

Handlebars.registerHelper("tripFinish", function(start, duration) {
	return friendlyDate( moment(start).add(duration, 'hours') );
});

Handlebars.registerHelper("friendlyDate", function(date) {
	return friendlyDate(date);
});

Handlebars.registerHelper("isLead", function (id) {
	if(booking.lead_customer && booking.lead_customer.id == id) {
		return new Handlebars.SafeString('<small><span class="label label-warning">LEAD</span></small>');
	}
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
	return window.countries[id].name;
});

// Load all initial handlebars templates

var agentTemplate = Handlebars.compile($("#agents-list-template").html());
var ticketTemplate = Handlebars.compile($("#tickets-list-template").html());
var tripTemplate = Handlebars.compile($("#trips-list-template").html());
var addonsTemplate = Handlebars.compile($("#addons-list-template").html());
var accommodationsTemplate = Handlebars.compile($("#accommodations-list-template").html());
var customersTemplate = Handlebars.compile($("#customers-list-template").html());
var countriesTemplate = Handlebars.compile($("#countries-template").html());

$.when(

	Agent.getAllAgents(function(data){
		window.agents = _.indexBy(data, 'id');
	}),

	Ticket.getAllTickets(function(data){
		window.tickets = _.indexBy(data, 'id');
	}),

	Trip.getAllTrips(function(data){
		window.trips = _.indexBy(data, 'id');
	}),

	Addon.getAllAddons(function(data){
		window.addons = _.indexBy(data, 'id');
	}),

	Accommodation.getAll(function(data){
		window.accommodations = _.indexBy(data, 'id');
	}),

	Customer.getAllCustomers(function(data){
		window.customers = _.indexBy(data, 'id');
	}),

	/**
	 * Having the list pre-populated is of no great use, because the list needs to be filtered by
	 * at least the ticket_id that is to be assigned. But we only know the ticket_id that should
	 * be assigned, when we arrive at the assignemnt screen (Sessions screen).
	 */
	/*
	Session.filter('', function(data){
		window.sessions = _.indexBy(data, 'id');
	});
	*/


	$.get("/api/country/all", function(data) {
		window.countries = _.indexBy(data, 'id');
	})

).then(function() {

	$("#agents-list").append(agentTemplate({agents:window.agents}));
	$("#tickets-list").append(ticketTemplate({tickets:window.tickets}));
	$("#existing-customers").append(customersTemplate({customers:window.customers}));
	$("#accommodations-list").append(accommodationsTemplate({accommodations:window.accommodations}));
	$("#trips").append(tripTemplate({trips:window.trips}));
	$("#addons-list").append(addonsTemplate({addons:window.addons}));
	$("#add-customer-countries").find('#country_id').append(countriesTemplate({countries:window.countries}));
	$("#edit-customer-countries").find('#country_id').append(countriesTemplate({countries:window.countries}));

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

	booking.initiate(params, function(status) {
		$('[data-target="#ticket-tab"]').tab('show');
	},
	function error(xhr) {
		$('.source-finish').html('Next');
	});
});

/*
*************************
******** Tickets ********
*************************
*/

var ticketsBasketTemplate = Handlebars.compile($("#selected-tickets-template").html());

$('[data-target="#ticket-tab"]').on('show.bs.tab', function () {
	booking.currentStep = 2;

	$("#selected-tickets").html(ticketsBasketTemplate({tickets:booking.selectedTickets}));
});

$('#ticket-tab').on('click', '.add-ticket', function() {

	var id = $(this).data('id');

	//Add ticket to selectedTickets if new, otherwise increase qty
	if(typeof booking.selectedTickets[id] != "undefined") {
		booking.selectedTickets[id].qty++;
	}else{
		booking.selectedTickets[id] = window.tickets[id];
		booking.selectedTickets[id].qty = 1;
	}

	//Draw the basket
	$("#selected-tickets").html(ticketsBasketTemplate({tickets:booking.selectedTickets}));
});

$('#ticket-tab').on('click', '.remove-ticket', function() {
	var id = $(this).data('id');

	//Lower quantity, if last ticket, remove from selected tickets.
	if(booking.selectedTickets[id].qty > 1) {
		booking.selectedTickets[id].qty--;
	}else{
		delete booking.selectedTickets[id];
	}

	$("#selected-tickets").html(ticketsBasketTemplate({tickets:booking.selectedTickets}));
});

$('#ticket-tab').on('click', '.tickets-finish', function() {
	$('[data-target="#customer-tab"]').tab('show');
});

/*
*************************
******* Customers *******
*************************
*/

var selectedCustomersTemplate = Handlebars.compile($("#selected-customers-template").html());

$('[data-target="#customer-tab"]').on('show.bs.tab', function () {
	booking.currentStep = 3;

	$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));
});

$('#customer-tab').on('click', '.add-customer', function() {
	var id = $('#existing-customers').val();
	booking.selectedCustomers[id] = window.customers[id];

	$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));

	if( _.size(booking.selectedCustomers) === 1 )
		booking.setLead({_token: window.token, customer_id: id}, function success(status) {
			$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));
		},
		function error(xhr) {
			//
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

$('#customer-tab').on('click', '.remove-customer', function() {
	var id = $(this).data('id');

	var details = _.filter(booking.bookingdetails, function(detail) {
		return detail.customer.id == id;
	});
	if( details.length > 0 ) {
		var question = confirm('This customer has already tickets assigned.\n\n Do you want to remove the customer anyway?');
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
		},
		function error(xhr) {
			// TODO Show error message
		});
	});

	delete booking.selectedCustomers[id];

	$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));

	// Check if we just removed the lead customer
	if(booking.lead_customer.id == id) {
		booking.lead_customer = false;
		if(_.size(booking.selectedCustomers) > 0) {
			booking.setLead(
				{
					_token: window.token,
					customer_id: _.find(booking.selectedCustomers, function(){return true;}).id // Returns the first selected customer
				},
				function success(status) {
					$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));
				},
				function error(xhr) {
					//
				}
			);
		}
		else {
			booking.setLead(
				{
					_token: window.token,
					customer_id: null // unset lead_customer_id on the server
				},
				function success(status) {
					// All good
				},
				function error(xhr) {
					// TODO Show error message
				}
			);
		}
	}
});

$('#customer-tab').on('submit', '#edit-customer-form', function(e) {
	e.preventDefault();

	var btn = $(this).find('button[type="submit"]');
	btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

	var params = $(this).serializeObject();
	params._token = window.token;

	Customer.updateCustomer(params, function() {
		Customer.getCustomer("id="+params.id, function(data) {
			//Updated selectedCustomers data
			window.customers[params.id] = data;
			booking.selectedCustomers[params.id] = window.customers[params.id];
			booking.lead_customer = booking.selectedCustomers[params.id];

			btn.html('Save');
			$('#edit-customer-modal').modal('hide');

			$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));
		});
	}, function() {
		// TODO Show error message
		btn.html('Save');
	});
});

$('#customer-tab').on('submit', '#new-customer', function(e) {
	e.preventDefault();
	var form = $(this);

	var btn = $(this).find('button[type="submit"]');
	btn.html('<i class="fa fa-cog fa-spin"></i> Adding...');

	var params = form.serializeObject();
	params._token = window.token;

	Customer.createCustomer(params, function success(data){
		Customer.getCustomer("id="+data.id, function(data) {

			window.customers[data.id] = data;
			booking.selectedCustomers[data.id] = window.customers[data.id];

			btn.html('Add');
			form[0].reset();

			$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));

			if( _.size(booking.selectedCustomers) === 1 )
				booking.setLead({_token: window.token, customer_id: data.id}, function success(status) {
					$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));
				},
				function error(xhr) {
					//
				});
		});
	}, function() {
		btn.html('Add');
	});
});

$('#customer-tab').on('click', '.clear-form', function() {
	$(this).parents('form')[0].reset();
});

$('#customer-tab').on('click', '.lead-customer', function() {
	booking.setLead( {_token: window.token, customer_id: $(this).data('id')}, function success(status) {
		$("#selected-customers").html( selectedCustomersTemplate({customers:booking.selectedCustomers}) );
	},
	function error(xhr) {
		//
	});
});

$('#customer-tab').on('click', '.customers-finish', function() {

	if(!booking.lead_customer) {
		showAlert("danger", "Please designate a lead customer.");
		return false;
	}
	if(!booking.lead_customer.email) {
		showAlert("danger", "Lead customer requires an email!");
		return false;
	}
	if(!booking.lead_customer.phone) {
		showAlert("danger", "Lead customer requires a phone number!");
		return false;
	}
	if(!booking.lead_customer.country_id) {
		showAlert("danger", "Lead customer requires a country!");
		return false;
	}

	$('[data-target="#session-tab"]').tab('show');
	// compileSessionsList();

});

/*
*************************
******* Sessions *******
*************************
*/

var sessionCustomersTemplate = Handlebars.compile($("#session-customers-template").html());
var sessionTicketsTemplate = Handlebars.compile($("#session-tickets-template").html());
var bookingDetailsTemplate = Handlebars.compile($("#booking-details-template").html());

$('[data-target="#session-tab"]').on('show.bs.tab', function () {
	booking.currentStep = 4;

	$("#session-customers").html(sessionCustomersTemplate({customers:booking.selectedCustomers}));
	$("#session-customers").children().first().addClass('active');

	$("#session-tickets").html(sessionTicketsTemplate({tickets:booking.selectedTickets}));
	$("#session-tickets").children().first().addClass('active');

	$("#booking-details").html(bookingDetailsTemplate({details:booking.bookingdetails}));

	setTimeout(function() {
		$('#session-filters').submit();
	}, 100);
});

$('#session-tab').on('click', '#session-tickets > a', function() {
	setTimeout(function() {
		$('#session-filters').submit();
	}, 100); // Need to give the browser time to set the .active class on the new list-item first
});

$('#session-tab').on('submit', '#session-filters', function(e) {
	e.preventDefault();

	$('#session-filters [type=submit]').html('Filter <i class="fa fa-cog fa-spin"></i>');

	var params = $(this).serializeObject();
	if( $('#session-tickets .active').length !== 0 )
		params.ticket_id = $('#session-tickets .active').first().data('id');

	compileSessionsList(params);
});

$('#session-tab').on('click', '.assign-session', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Assigning...');

	var ticket_id = $('#session-tickets').children('.active').first().data('id');
	var customer_id = $('#session-customers').children('.active').first().data('id');
	var session_id = btn.data('id');

	var params = {};
	params._token = window.token;
	params.customer_id = customer_id;
	params.ticket_id = ticket_id;
	params.session_id = session_id;

	booking.addDetail(params, function(status) {
		$('.free-spaces[data-id="'+session_id+'"]').html('<i class="fa fa-refresh fa-spin"></i>');

		var params = $("#session-filters").serializeObject();
		if( $('#session-tickets .active').length !== 0 ) {
			params.ticket_id = $('#session-tickets .active').first().data('id');
		}

		compileSessionsList(params);

		//List customer's bookingdetails in selectedCustomers for accommodations tab
		var details = _.filter(booking.bookingdetails, function (detail) {
		    return detail.customer.id == customer_id;
		});

		booking.selectedCustomers[customer_id].bookingdetails = details;

		$("#booking-details").html(bookingDetailsTemplate({details:booking.bookingdetails}));

	}, function() {
		btn.html('Assign');
	});
});

$('#session-tab').on('click', '.unassign-session', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Unassigning...');

	var params = {};
	params._token = window.token;
	params.bookingdetail_id = $(this).data('id');

	booking.removeDetail(params, function() {
		var params = $("#session-filters").serializeObject();
		if( $('#session-tickets .active').length !== 0 )
			params.ticket_id = $('#session-tickets .active').first().data('id');

		compileSessionsList(params);

		$("#booking-details").html(bookingDetailsTemplate({details:booking.bookingdetails}));
	}, function() {
		btn.html('Unassign');
	});

});

$('#session-tab').on('click', '.sessions-finish', function() {
	if(_.size(window.addons) > 0) {

		$('[data-target="#addon-tab"]').tab('show');

	}else if(_.size(window.accommodations) > 0){

		$('[data-target="#accommodation-tab"]').tab('show');

	}else{

	}
});

/*
************************
******** Addons ********
************************
*/

var addonBookingDetailsTemplate = Handlebars.compile($("#addon-booking-details-template").html());
var selectedAddonsTemplate = Handlebars.compile($("#selected-addons-template").html());

$('[data-target="#addon-tab"]').on('show.bs.tab', function () {
	booking.currentStep = 5;

	$("#addon-booking-details").html(addonBookingDetailsTemplate({details:booking.bookingdetails}));
	$("#addon-booking-details").children().first().addClass('active');
});

var addonTotal = 0;
$('#addon-tab').on('click', '.add-addon', function() {
	var btn = $(this);
	// var qty = $('.addon-qty[data-id="'+$(this).data('id')+'"]');

	btn.html('<i class="fa fa-cog fa-spin"></i> Adding...');

	var params = {};
	params._token = window.token;
	params.bookingdetail_id = $('#addon-booking-details').children('.active').first().data('id');
	params.addon_id = $(this).data('id');
	params.quantity = $('.addon-qty[data-id="'+$(this).data('id')+'"]').val();

	booking.addAddon(params, function() {
		btn.html('Add');
		$("#selected-addons").html(selectedAddonsTemplate({details:booking.bookingdetails}));
	});
});

$('#addon-tab').on('click', '.remove-addon', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Removing...');

	var params = {};
	params._token = window.token;
	params.bookingdetail_id = $(this).data('bookingdetail-id');
	params.addon_id = $(this).data('id');

	booking.removeAddon(params, function() {
		btn.html('Remove');
		$("#selected-addons").html(selectedAddonsTemplate({details:booking.bookingdetails}));
	});
});

$('#addon-tab').on('click', '.addon-finish', function() {
	if(_.size(window.accommodations) > 0){
		$('[data-target="#accommodation-tab"]').tab('show');
	}else{
		$('[data-target="#extra-tab"]').tab('show');
	}
});

/*
*************************
***** Accommodation *****
*************************
*/

var accommodationCustomersTemplate = Handlebars.compile($("#accommodation-customers-template").html());
var assignedAccommodationsTemplate = Handlebars.compile($("#assigned-accommodations-template").html());

$('[data-target="#accommodation-tab"]').on('show.bs.tab', function () {
	booking.currentStep = 6;

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

	var params = {};
	params._token = window.token;
	params.accommodation_id = $(this).data('id');
	params.customer_id = $('#accommodation-customers').children('.active').first().data('id');
	params.start = $(this).parents('.accommodation-item').find('[name="start"]').val();
	params.end = $(this).parents('.accommodation-item').find('[name="end"]').val();

	booking.addAccommodation(params, function() {
		$("#assigned-accommodations").html(assignedAccommodationsTemplate({accommodations:booking.accommodations}));
	});
});

$('#accommodation-tab').on('click', '.remove-accommodation', function() {

	var accommodation_id = $(this).data('id');
	var customer_id = $(this).data('customer-id');

	var params = {};
	params._token = window.token;
	params.accommodation_id = accommodation_id;
	params.customer_id = customer_id;

	booking.removeAccommodation(params, function() {
		$("#assigned-accommodations").html(assignedAccommodationsTemplate({customers:booking.selectedCustomers}));
	});
});

$('#accommodation-tab').on('click', '.accommodation-finish', function() {
	$('[data-target="#extra-tab"]').tab('show');
	booking.currentStep = 7;
});

/*
*************************
****** Extra Info *******
*************************
*/

$('[data-target="#extra-tab"]').on('show.bs.tab', function () {
	booking.currentStep = 7;
});

$('#extra-tab').on('submit', '#extra-form', function(e) {
	e.preventDefault();

	var btn = $(this).find('[type="submit"]');
	btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

	var params = $(this).serializeObject();
	params._token = window.token;

	booking.editInfo(params, function(status) {
		btn.html('Next');
		$('[data-target="#summary-tab"]').tab('show');
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
	booking.currentStep = 8;

	$("#summary-booking-details").html(summaryBookingDetailsTemplate({bookingdetails:booking.bookingdetails}));
	$("#summary-accommodations").html(summaryAccommodationsTemplate({accommodations:booking.accommodations}));
	$("#summary-lead").html(summaryLeadTemplate(booking.lead_customer));
	$("#summary-price").html(summaryPriceTemplate(booking));

});

$('#summary-tab').on('click', '.save-booking', function() {

	var params = {};
	params._token = window.token;

	booking.save(params, function success(status) {
		showAlert("success", "Booking saved successfully!");
	}, function error(xhr) {
		showAlert("danger", "There was an error saving this booking.");
	});

});

$('#summary-tab').on('click', '.reserve-booking', function() {

	var params = {};
	params._token = window.token;

	//Frontend will reserve for 1, 2, 3 hours etc

	booking.save(params, function success(status) {
		showAlert("success", "Booking saved successfully!");
	}, function error(xhr) {
		showAlert("danger", "There was an error saving this booking.");
	});

});

/*
***************************
**** Functions & Other ****
***************************
*/

$(document).ready(function() {

	$('#agent-info').hide();
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

		if(!$(e.target).hasClass('done') && !$(e.target).hasClass('selected')) {
			$(e.target).parent().prevAll().children().removeClass('selected').addClass('done');
			$(e.target).addClass('selected').tab('show');
		}

		if(booking.currentStep > 1) {
			$('[data-target="#source-tab"]').removeAttr("data-toggle");
		}
	});

	$('a[data-toggle="tab"]').on('click', function () {
		if(!$(this).hasClass('done') && !$(this).hasClass('selected')) {
			return false;
		}
	});

	$(document).on('click', '.list-group-radio', function() {
		listGroupRadio($(this));
	});

	$('.alert-container').remove();

});

function compileSessionsList(params) {
	if(typeof(params) === 'undefined') params = "";

	var sessionsTemplate = Handlebars.compile($("#sessions-table-template").html());

	Session.filter(params, function(data){
		window.sessions = _.indexBy(data, 'id');
		$("#sessions-table tbody").html(sessionsTemplate({sessions:data}));
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

function showAlert(type, error) {
	$('.alert-container').remove();
	$(booking.currentTab).find('.row-header').append('<div class="col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4 alert-container"><div class="alert alert-'+type+'" role="alert">'+error+'</div></div>');
	$('.alert-container').hide().fadeIn();
}

function addTransaction() {
	window.clickedEdit = true;
	window.location.hash = 'add-transaction';
}

if(typeof booking !== 'undefined' && booking.currentStep > 1) {
	$('[data-target="'+booking.currentTab+'"]').tab('show');
}else{
	var booking = new Booking();
	booking.currentStep = 1;
}
