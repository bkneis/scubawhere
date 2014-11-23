window.token;
$.get("/token", null, function(data) {
	window.token = data;
});

Handlebars.registerHelper("freeSpaces", function(capacity) {
	var freeSpaces = capacity[1] - capacity[0];
	var percentage = (capacity[0] / capacity[1]) * 100;

	var color = '#5cb85c'; bgClass = 'bg-success';
	if(percentage >= 75) { color = '#f0ad4e'; bgClass = 'bg-warning'; }
	if(percentage == 1) { color = '#d9534f'; bgClass = 'bg-danger'; }

	var html = '';
	html += '<div data-id="' + this.id + '" class="util-bar-container ' + bgClass + '">';
	html += '	<div class="util-bar" style="background-color: ' + color + '; width: ' + percentage + '%">&nbsp;</div>';
	html += '	<span class="util-spaces">' + freeSpaces + '</span>';
	html += '</div>';

	return new Handlebars.SafeString(html);
});

Handlebars.registerHelper("tripFinish", function(start, duration) {
	var d = new Date(Date.parse(start));
	d.setHours(d.getHours()+duration);
	var f = d.toISOString().slice(0, 19).replace('T', ' ');

	return friendlyDate(f);
});

Handlebars.registerHelper("friendlyDate", function(d) {
	return friendlyDate(d);
});

Handlebars.registerHelper("isLead", function (id) {
	if(booking.lead == id) {
		return new Handlebars.SafeString('<small><span class="label label-warning">LEAD</span></small>');
	}
});

Handlebars.registerHelper("priceRange", function(prices) {
	if(prices.length > 1) {
		var min=null, max=null;
		$.each(prices, function(i,v) {
			var price = parseFloat(v.decimal_price).toFixed(2);
			if ((min===null) || (price < min)) { min = price; }
			if ((max===null) || (price > max)) { max = price; }
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

// Load all of the agents, tickets and packages for dive center to select
$(function(){

	var agentTemplate = Handlebars.compile($("#agents-list-template").html());
	Agent.getAllAgents(function(data){
		$("#agents-list").append(agentTemplate({agents:data}));
	});

	var ticketTemplate = Handlebars.compile($("#tickets-list-template").html());
	Ticket.getAllTickets(function(data){
		window.tickets = _.indexBy(data, 'id');
		$("#tickets-list").append(ticketTemplate({tickets:data}));
	});

	var tripTemplate = Handlebars.compile($("#trips-list-template").html());
	Trip.getAllTrips(function(data){
		$("#trips").append(tripTemplate({trips:data}));
	});

	var addonsTemplate = Handlebars.compile($("#addons-list-template").html());
	Addon.getAllAddons(function(data){
		window.addons = _.indexBy(data, 'id');
		$("#addons-list").append(addonsTemplate({addons:data}));
	});

	var accommodationsTemplate = Handlebars.compile($("#accommodations-list-template").html());
	Accommodation.getAll(function(data){
		window.accommodations = _.indexBy(data, 'id');
		$("#accommodations-list").append(accommodationsTemplate({accommodations:data}));
	});

	var customersTemplate = Handlebars.compile($("#customers-list-template").html());
	Customer.getAllCustomers(function(data){
		window.customers = _.indexBy(data, 'id');
		$("#existing-customers").append(customersTemplate({customers:data}));
	});

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

	var countriesTemplate = Handlebars.compile($("#countries-template").html());

	$.get("/api/country/all", function(data) {
		$("#add-customer-countries").find('#country_id').append(countriesTemplate({countries:data}));
		$("#edit-customer-countries").find('#country_id').append(countriesTemplate({countries:data}));
	});

});

var booking = new Booking(); //Start the engines

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
	if(type == "agent") {
		var agentId = $('#agents').children('.active').data('id');
		var params = [{name: "_token", value: window.token}, {name: "agent_id", value: agentId}];
	}else{
		var params = [{name: "_token", value: window.token}, {name: "source", value: type}];
	}

	booking.initiate(params, function(data) {
		booking.currentStep = 2;
		$('[data-target="#ticket-tab"]').tab('show');
	});
});

/*
*************************
******** Tickets ********
*************************
*/

booking.selectedTickets = {};
var ticketsBasketTemplate = Handlebars.compile($("#selected-tickets-template").html());

$('#ticket-tab').on('click', '.add-ticket', function() {

	var id = $(this).data('id');

	//Add ticket to selectedTickets if new, otherwise increase qty
	if(typeof booking.selectedTickets[id] != "undefined") {
		booking.selectedTickets[id].qty += 1;
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
		booking.selectedTickets[id].qty -= 1;
	}else{
		delete booking.selectedTickets[id];
	}

	$("#selected-tickets").html(ticketsBasketTemplate({tickets:booking.selectedTickets}));
});

$('#ticket-tab').on('click', '.tickets-finish', function() {
	booking.currentStep = 3;
	$('[data-target="#customer-tab"]').tab('show');
});

/*
*************************
******* Customers *******
*************************
*/

booking.selectedCustomers = {};
var selectedCustomersTemplate = Handlebars.compile($("#selected-customers-template").html());

$('#customer-tab').on('click', '.add-customer', function() {
	var id = $('#existing-customers').val();
	booking.selectedCustomers[id] = window.customers[id];
	if( !booking.lead )
		booking.lead = id;

	$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));
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
	delete booking.selectedCustomers[id];

	$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));
});

$('#customer-tab').on('submit', '#edit-customer-form', function(e) {
	e.preventDefault();

	var btn = $(this).find('button[type="submit"]');
	btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

	var params = $(this).serializeObject();
	params._token = window.token;

	Customer.updateCustomer(params, function() {
		Customer.getCustomer("id="+id, function(data) {
			//Updated selectedCustomers data
			booking.selectedCustomers[id] = data;

			btn.html('Save');
			$('#edit-customer-modal').modal('hide');

			$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));
		});
	}, function() {
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
			booking.selectedCustomers[data.id] = data;

			btn.html('Add');
			form[0].reset();

			$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));
		});
	}, function() {
		btn.html('Add');
	});
});

$('#customer-tab').on('click', '.clear-form', function() {
	$(this).parents('form')[0].reset();
});

$('#customer-tab').on('click', '.lead-customer', function() {
	booking.lead = $(this).data('id');
	$("#selected-customers").html(selectedCustomersTemplate({customers:booking.selectedCustomers}));
});

$('#customer-tab').on('click', '.customers-finish', function() {

	if(!booking.lead) {
		alert("Please designate a lead customer.");
		return false;
	}else{
		var emailCheck = booking.selectedCustomers[booking.lead].email;
		var phoneCheck = booking.selectedCustomers[booking.lead].phone;
		var countryCheck = booking.selectedCustomers[booking.lead].country_id;

		if(!emailCheck) {
			alert("Lead customer requires an email!");
			return false;
		}else if(!phoneCheck) {
			alert("Lead customer requires a phone number!");
			return false;
		}else if(!countryCheck) {
			alert("Lead customer requires a country!");
			return false;
		}else{
			$('[data-target="#session-tab"]').tab('show');
			$("#session-customers").html(sessionCustomersTemplate({customers:booking.selectedCustomers}));
			$("#session-tickets").html(sessionTicketsTemplate({tickets:booking.selectedTickets}));
			// compileSessionsList();
			booking.currentStep = 4;
		}
	}
});

/*
*************************
******* Sessions *******
*************************
*/

var sessionCustomersTemplate = Handlebars.compile($("#session-customers-template").html());
var sessionTicketsTemplate = Handlebars.compile($("#session-tickets-template").html());
var bookingDetailsTemplate = Handlebars.compile($("#booking-details-template").html());

$('#session-tab').on('submit', '#session-filters', function(e) {
	e.preventDefault();
	var params = $(this).serializeObject();
	if( $('#session-tickets .active').length != 0 )
		params.ticket_id = $('#session-tickets .active').first().data('id');

	compileSessionsList(params);
});

$('#session-tab').on('click', '.assign-session', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Assigning...');

	var ticket_id = $('#session-tickets').children('.active').first().data('id');
	var customer_id = $('#session-customers').children('.active').first().data('id');
	var session_id = btn.data('id');

	if(booking.lead == customer_id) {
		var isLead = 1;
	}else{
		var isLead = 0;
	}

	var params = {};
	params._token = window.token;
	params.customer_id = customer_id;
	params.ticket_id = ticket_id;
	params.session_id = session_id;
	params.is_lead = isLead;

	booking.addDetail(params, function(status, id) {
		console.log(id);
		$('.free-spaces[data-id="'+session_id+'"]').html('<i class="fa fa-refresh fa-spin"></i>');

		var params = $("#session-filters").serializeObject();
		if( $('#session-tickets .active').length != 0 ) {
			params.ticket_id = $('#session-tickets .active').first().data('id');
		}

		compileSessionsList(params);

		//List customer's bookingdetails in selectedCustomers for accommodations tab
		var detail = _.find(booking.bookingdetails, function (detail) {
		    return detail.customer.id == customer_id;
		});

		booking.selectedCustomers[customer_id].bookingdetails = [];
		booking.selectedCustomers[customer_id].bookingdetails.push(detail);

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
		if( $('#session-tickets .active').length != 0 )
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
		$("#addon-booking-details").html(addonBookingDetailsTemplate({details:booking.bookingdetails}));
		booking.currentStep = 5;
	}else if(_.size(window.accommodations) > 0){
		$('[data-target="#accommodation-tab"]').tab('show');
		booking.currentStep = 6;
	}else{

	}
});

/*
*************************
******* Addons *******
*************************
*/

var addonBookingDetailsTemplate = Handlebars.compile($("#addon-booking-details-template").html());
var selectedAddonsTemplate = Handlebars.compile($("#selected-addons-template").html());

var addonTotal = 0;
$('#addon-tab').on('click', '.add-addon', function() {
	var btn = $(this);
	var qty = $('.addon-qty[data-id="'+$(this).data('id')+'"]');

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
		$("#accommodation-customers").html(accommodationCustomersTemplate({customers:booking.selectedCustomers}));
		$('[data-target="#accommodation-tab"]').tab('show');
		booking.currentStep = 6;
	}else{
		$('[data-target="#extra-tab"]').tab('show');
		booking.currentStep = 7;
	}
});

/*
*************************
***** Accommodation *****
*************************
*/

var accommodationCustomersTemplate = Handlebars.compile($("#accommodation-customers-template").html());

$('#accommodation-tab').on('click', '.accommodation-customer', function() {
	var start = $(this).find('.session-start').data('date');

	//Get day before and convert into nice format.
	var d = new Date(Date.parse(start));
	d.setDate(d.getDate()-1);
	var friendlyDate = d.getDate()+"/"+(addZ(d.getMonth()+1))+"/"+(d.getFullYear());

	//Update all accommodation start fields.
	$('.accommodation-start').val(friendlyDate);
});

$('#accommodation-tab').on('click', '.add-accommodation', function() {

	var start = $(this).parent().find('[name="start"]').val().split("/");
	var formattedStart = start[2]+"-"+start[1]+"-"+start[0];

	var end = $(this).parent().find('[name="end"]').val().split("/");
	var formattedEnd = end[2]+"-"+end[1]+"-"+end[0];
	
	var params = {};
	params._token = window.token;
	params.accommodation_id = $(this).data('id');
	params.customer_id = $('#accommodation-customers').children('.active').first().data('id');
	params.start = formattedStart;
	params.end = formattedEnd;

	booking.addAccommodation(params, function() {

	});
});

/*
*************************
****** Extra Info *******
*************************
*/

$(document).on('submit', '#extra-form', function(e) {
	e.preventDefault();

	var btn = $(this).find('[type="submit"]');
	btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

	var params = $(this).serializeObject();
	params._token = window.token;

	Booking.editInfo(params, function(data) {
		btn.html('Next');
		$('[data-target="#summary-tab"]').tab('show');
	});
});

$(document).ready(function() {

	$('#agent-info').hide();
	$('#existing-customers').select2();
	$('#trips').select2();
	$('#country_id').select2();

	//Form Wizard
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		if(!$(e.target).hasClass('done') && !$(e.target).hasClass('selected')) {
			$(e.relatedTarget).toggleClass('selected done');
			$(e.target).addClass('selected').tab('show');
		}
		$('.datepicker').datepicker();
	});

	$('a[data-toggle="tab"]').on('click', function (e) {
		if(!$(this).hasClass('done') && !$(this).hasClass('selected')) {
			return false;
		}
	});

	$(document).on('click', '.list-group-radio', function(e) {
		listGroupRadio($(this));
	});

});

function compileSessionsList(params) {
	if(typeof(params) === 'undefined') params = "";

	var sessionsTemplate = Handlebars.compile($("#sessions-table-template").html());

	Session.filter(params, function(data){
		window.sessions = _.indexBy(data, 'id');
		$("#sessions-table tbody").html(sessionsTemplate({sessions:data}));
	});
}

function listGroupRadio(selector, additionalClass) {
	//This function treats list-group-items (http://getbootstrap.com/components/#list-group) like radios buttons
	selector.siblings().removeClass('active '+additionalClass, selector.siblings().hasClass('active '+additionalClass));
	selector.toggleClass('active '+additionalClass, !selector.siblings().hasClass('active '+additionalClass));
}

function friendlyDate(date) {
	var d = new Date(Date.parse(date));

	//Why doesn't javascript have a nice Date like PHP?!
	return d.getDate()+"/"+(addZ(d.getMonth()+1))+"/"+(d.getFullYear())+" "+(addZ(d.getHours()))+":"+(addZ(d.getMinutes()));
}

//Adds 0 to single digits for date/times.
function addZ(n){return n<10? '0'+n:''+n;}