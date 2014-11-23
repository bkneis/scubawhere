window.token;
$.get("/token", null, function(data) {
	window.token = data;
});

Handlebars.registerHelper("freeSpaces", function(capacity) {
	var freeSpaces = capacity[1] - capacity[0];
	return freeSpaces;
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
		$("#agents").append(agentTemplate({agents:data}));
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

	var addonsTemplate = Handlebars.compile($("#addons-template").html());

	Addon.getAllAddons(function(data){
		$("#addons").append(addonsTemplate({addons:data}));
	});

	var customersTemplate = Handlebars.compile($("#customers-list-template").html());

	Customer.getAllCustomers(function(data){
		window.customers = _.indexBy(data, 'id');
		$("#existing-customers").append(customersTemplate({customers:data}));
	});

	Session.filter('', function(data){
		window.sessions = _.indexBy(data, 'id');
	});

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

	var id = $("#edit-customer-details").find('input[name="id"]').val();
	var params = $(this).serializeArray();
	var btn = $(this).find('button[type="submit"]');

	btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

	params.push({name: "_token", value: window.token});
	params.push({name: "id", value: id});

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
	var params = form.serializeArray();
	var btn = $(this).find('button[type="submit"]');

	btn.html('<i class="fa fa-cog fa-spin"></i> Adding...');

	params.push({name: "_token", value: window.token});

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
			compileSessionsList();
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
	compileSessionsList($(this).serialize());
});

$('#session-tab').on('click', '.assign-session', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Assigning...');

	var ticketId = $('#session-tickets').children('.active').first().data('id');
	var customerId = $('#session-customers').children('.active').first().data('id');
	var sessionId = btn.data('id');
	
	if(booking.lead == customerId) {
		var isLead = 1;
	}else{
		var isLead = 0;
	}

	var params = {};
	params._token = window.token;
	params.customer_id = $('#session-customers').children('.active').first().data('id');
	params.ticket_id = $('#session-tickets').children('.active').first().data('id');
	params.session_id = btn.data('id');
	params.is_lead = isLead;

	booking.addDetail(params, function(data) {
		$('.free-spaces[data-id="'+sessionId+'"]').html('<i class="fa fa-refresh fa-spin"></i>');
		compileSessionsList($("#session-filters").serialize());
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
		compileSessionsList($("#session-filters").serialize());
		$("#booking-details").html(bookingDetailsTemplate({details:booking.bookingdetails}));
	}, function() {
		btn.html('Unassign');
	});
	
});

$('#session-tab').on('click', '.sessions-finish', function() {
	$('[data-target="#addon-tab"]').tab('show');
	$("#addon-booking-details").html(addonBookingDetailsTemplate({details:booking.bookingdetails}));
	booking.currentStep = 5;
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
	var id = $(this).data('id');
	var addon = $('#addons-summary').find('[data-id="'+id+'"]');
	var price = addon.find('.price').text();
	var basePrice = $('#baseprice-'+id).text();
	var qty = parseInt(addon.find('.qty').text(), 10);

	if(qty > 1) {
		addon.find('.qty').text(qty-1);
		addon.find('.price').text(price-basePrice);
	}else{
		addon.remove();
	}

	addonTotal -= basePrice;
	$('#addons-summary-total').html('£'+addonTotal);
});

$('#addon-tab').on('click', '.addon-finish', function() {
	var btn = $(this);
	btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

	$('#addons-summary').children('.summary-item').each(function(k,v) {
		var params = [
			{name: "_token", value: window.token},
			{name: "booking_id", value: booking.id},
			{name: "session_id", value: $(v).data("session-id")},
			{name: "customer_id", value: $(v).data("customer-id")},
			{name: "addon_id", value: $(v).data("addon-id")},
			{name: "quantity", value: $(v).find(".qty").text()}
		];

		Booking.addAddon(params, function(data) {
			btn.html('Save');
			$('[data-target="#extra-tab"]').tab('show');
		});
	});

	
});

$(document).on('submit', '#extra-form', function(e) {
	e.preventDefault();

	var btn = $(this).find('[type="submit"]');
	btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

	var params = $(this).serializeArray();
	params.push({name: "_token", value: window.token});
	params.push({name: "booking_id", value: booking.id});
	
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
		$("#sessions-table tbody").html(sessionsTemplate({sessions:data}));
	});
}


var handleSummary = [];
function addToAddonSummary(addon) {

	var selectedSubBooking = $('#addon-sessions').find('.active');
	addon.sessionId = selectedSubBooking.data('id');
	addon.customer = selectedSubBooking.find('.customer-name').text();
	addon.customerId = selectedSubBooking.data('customer-id');
	addon.ticket = selectedSubBooking.find('.ticket-name').text();
	addon.trip = selectedSubBooking.find('.trip-name').text();
	addon.start = selectedSubBooking.find('.start-date').text();

	handleSummary.push({
		"id":addon.id,
		"customer_id":addon.customerId,
		"session_id":addon.sessionId,
		"customer":addon.customer,
		"ticket":addon.ticket,
		"trip":addon.trip,
		"start":addon.start,
		"addon":addon.name,
		"price":addon.basePrice,
		"qty":addon.inputQty
	});

	var addonsSummaryTemplate = Handlebars.compile($("#addons-summary-template").html());
	$("#addons-summary").html('').append(addonsSummaryTemplate({addonsSummary:handleSummary}));
}

function addToAssignedSessions(bookingDetail) {
	var addedBookingdetailsTemplate = Handlebars.compile($("#added-bookingdetails-template").html());
	$("#added-bookingdetails").append(addedBookingdetailsTemplate(bookingDetail));
}

//Add the session to the addons page
function generateAddonSessions(sessions) {
	var addonSessionsTemplate = Handlebars.compile($("#addon-sessions-template").html());
	var handleData = [];
	var maxSessions = sessions.length;

	$.each(sessions, function(i, session) {
		var handleItem = [];
		//Build "sessions" (or in database terms, booking_details)
		Customer.getCustomer("id="+session.customer_id, function(data) {
			handleItem.customer = data.firstname+" "+data.lastname;
		});

		Ticket.getTicket("id="+session.ticket_id, function(data) {
			handleItem.ticket = data.name;
		});

		Session.getSpecificSession("id="+session.id, function(data) {
			handleItem.start = data.start;

			Trip.getSpecificTrip("id="+data.trip_id, function(data) {
				handleItem.trip = data.name;

				//We are doing this in here to wait for all ajax to finish
				handleData.push({"id":session.id, "customer":handleItem.customer, "customer_id":session.customer_id, "ticket":handleItem.ticket, "start":handleItem.start, "trip":handleItem.trip});

				//On the last loop, render the view
				if(i == (maxSessions-1))
				{
					$("#addon-sessions").html('').append(addonSessionsTemplate({sessions:handleData}));
					$('[data-target="#addon-tab"]').tab('show');
				}
			});
		});
	});

}

function listGroupRadio(selector, additionalClass) {
	//This function treats list-group-items (http://getbootstrap.com/components/#list-group) like radios buttons
	selector.siblings().removeClass('active '+additionalClass, selector.siblings().hasClass('active '+additionalClass));
	selector.toggleClass('active '+additionalClass, !selector.siblings().hasClass('active '+additionalClass));
}

function friendlyDate(date) {
	var d = new Date(date);

	//Adds 0 to single digits for date/times.
	function addZ(n){return n<10? '0'+n:''+n;}

	//Why doesn't javascript have a nice Date like PHP?!
	return d.getDate()+"/"+(addZ(d.getMonth()+1))+"/"+(d.getFullYear())+" "+(addZ(d.getHours()))+":"+(addZ(d.getMinutes()));
}