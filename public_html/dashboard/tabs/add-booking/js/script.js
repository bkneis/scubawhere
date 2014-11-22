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
		$("#tickets").append(ticketTemplate({tickets:data}));
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

//Sources

var booking = new Booking();

$(document).on('click', '.booking-source a', function() {
	//Converts bootsrap list into a "radio button style" form element
	listGroupRadio($(this), 'btn-primary');

	//If agent select, display list of agents
	if($(this).data('type') == 'agent') {
		$('#agent-info').slideDown();
	}else{
		$('#agent-info').slideUp();
	}

});

$(document).on('click', '.source-finish', function() {

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
		$('[data-target="#ticket-tab"]').tab('show');
	});
});

//Tickets

$(document).on('click', '.btn-ticket', function() {
	//Get data from the ticket
	var id = $(this).data('id');
	var name = $(this).find('.ticket-name').html();

	//Get the specific font awesome icon (without size increase)
	var icon = $(this).find('.fa').attr('class').split(' ')[1];

	//Check if ticket is already in basket
	if($('#basket').find('#ticket-'+id).length) {
		var qty = parseInt($('#ticket-'+id).find('.qty').text(), 10);

		$('#ticket-'+id).find('.qty').text(qty+1);
	}else{
		var qty = 1;
		$('#basket').append('<p class="list-group-item-text" id="ticket-'+id+'"><i class="fa '+icon+'"></i> <a href="javascript:void(0);" title="Click to remove" class="remove-ticket" data-id="'+id+'">'+name+'</a> <span class="badge qty">'+qty+'</span></p>');
	}

	addBookingTicket(id);
});

$(document).on('click', '.remove-ticket', function() {
	var id = $(this).data('id');
	var ticket = $('#ticket-'+id);
	var qty = parseInt(ticket.find('.qty').text(), 10);

	if(qty > 1) {
		ticket.find('.qty').text(qty-1);
	}else{
		ticket.remove();
	}

	removeBookingTicket(id);
});

$(document).on('click', '.tickets-finish', function() {
	$('[data-target="#customer-tab"]').tab('show');	
});

//Customers

$(document).on('click', '.add-customer', function() {
	var id = $('#existing-customers').val();
	var btn = $(this);

	btn.html('<i class="fa fa-cog fa-spin"></i> Adding...');

	addBookingCustomer(id, function() {
		btn.html('Add to booking');
	});
});

$(document).on('click', '.edit-customer', function() {
	var id = $(this).data('id');

	$('#edit-customer-modal').modal('show');

	var editCustomerTemplate = Handlebars.compile($("#edit-customer-template").html());
	Customer.getCustomer("id="+id, function(data) {
		$("#edit-customer-details").html('').append(editCustomerTemplate(data));

		if(data.country_id) {
			$('#country_id').val(data.country_id);
		}else{
			$('#country_id').val("");
		}
	});
});

$(document).on('submit', '#edit-customer-form', function(e) {
	e.preventDefault();

	var id = $("#edit-customer-details").find('input[name="id"]').val();
	var params = $(this).serializeArray();
	var btn = $(this).find('button[type="submit"]');

	btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

	params.push({name: "_token", value: window.token});
	params.push({name: "id", value: id});

	Customer.updateCustomer(params, function() {
		addBookingCustomer(id, function() {
			btn.html('Save');
			$('#edit-customer-modal').modal('hide');
		});
	});
});

$(document).on('click', '.remove-customer', function() {
	var id = $(this).data('id');
	$(this).parents('.list-group-item').remove();
	removeBookingCustomer(id);
});

$(document).on('submit', '#new-customer', function(e) {
	e.preventDefault();
	var form = $(this);
	var params = form.serializeArray();
	var btn = $(this).find('button[type="submit"]');

	btn.html('<i class="fa fa-cog fa-spin"></i> Adding...');

	params.push({name: "_token", value: window.token});

	Customer.createCustomer(params, function success(data){
		addBookingCustomer(data.id, function() {
			btn.html('Add Customer');
			form[0].reset();
		});
	});
});

$(document).on('click', '.clear-form', function() {
	$(this).parents('form')[0].reset();
});

$(document).on('click', '.lead-customer', function() {
	var id = $(this).data('id');
	$("#session-customers").find('[data-id="'+id+'"]').attr('data-lead', 1).siblings('li').attr('data-lead', 0);

	$("#added-customers").find('[data-id="'+id+'"]').siblings('li').attr('data-lead', 0).find('.is-lead').html('');
	$("#added-customers").find('[data-id="'+id+'"]').attr('data-lead', 1).find('.is-lead').html('Lead');
});

$(document).on('click', '.customers-finish', function() {
	var leadCustomer = $('#added-customers').children('li[data-lead="1"]');

	var emailCheck = leadCustomer.find('.customer-email').text().length;
	var phoneCheck = leadCustomer.find('.customer-phone').text().length;
	var countryCheck = leadCustomer.data('country-id');

	if(!leadCustomer.length) {
		alert("Please designate a lead customer.");
		return false;
	}else if(!emailCheck) {
		alert("Lead customer requires an email!");
		return false;
	}else if(!phoneCheck) {
		alert("Lead customer requires a phone number!");
		return false;
	}else if(!countryCheck) {
		alert("Lead customer requires a country!");
		return false;
	}else{
		booking.lead_id = leadCustomer.data('id')
		$('[data-target="#session-tab"]').tab('show');
		compileSessionsList();
	}
});

$(document).on('submit', '#session-filters', function(e) {
	e.preventDefault();
	compileSessionsList($(this).serialize());
});

$(document).on('click', '.assign-session', function() {
	var ticket = $('#session-tickets').children('.active').first();
	var btn = $(this);

	var bookingdetail = {};

	bookingdetail.sessionId = $(this).data('id');
	bookingdetail.sessionStart = $(this).parents('tr').find('.session-start').text();
	bookingdetail.sessionTrip = $(this).parents('tr').find('.session-trip').text().trim();
	bookingdetail.customerId = $('#session-customers').children('.active').first().data('id');
	bookingdetail.customerName = $('#session-customers').children('.active').first().text().trim();
	bookingdetail.ticketId = ticket.data('id');
	bookingdetail.ticketName = ticket.text().trim();

	btn.html('<i class="fa fa-cog fa-spin"></i> Assigning...');

	if(bookingdetail.customerId == booking.lead_id) {
		var isLead = 1;
	}else{
		var isLead = 0;
	}

	var params = [
		{name: "_token", value: window.token},
		{name: "booking_id", value: booking.id},
		{name: "customer_id", value: bookingdetail.customerId},
		{name: "is_lead", value: isLead},
		{name: "ticket_id", value: bookingdetail.ticketId},
		{name: "session_id", value: bookingdetail.sessionId}
	];

	console.log("Session Params");
	console.log(params);

	Booking.addDetails(params, function(data) {
		addToAssignedSessions(bookingdetail);

		$("#free-spaces"+(bookingdetail.sessionId)).html('<i class="fa fa-refresh fa-spin"></i>');

		compileSessionsList($("#session-filters").serialize());
		sessions.push({"id": bookingdetail.sessionId, "customer_id": bookingdetail.customerId, "ticket_id": bookingdetail.ticketId});
			
		if($('#session-tickets').children('.unused-ticket').length == 1) {
			$('.session-requirements').slideUp();
			$('.assign-session').attr("disabled", "disabled");
		}

		ticket.removeClass('unused-ticket');
	}, function() {
		btn.html('Assign');
	});
});

$(document).on('click', '.unassign-session', function() {
	var item = $(this).parents('li');
	var customerId = $(this).data('customer-id');
	var sessionId = $(this).data('session-id');
	var ticketId = $(this).data('ticket-id');

	$(this).html('<i class="fa fa-cog fa-spin"></i>');

	var params = [
		{name: "_token", value: window.token},
		{name: "booking_id", value: booking.id},
		{name: "customer_id", value: customerId},
		{name: "session_id", value: sessionId}
	];

	//Remove the session-customer-ticket at end of loop otherwise loop will carry on through deleted
	var maxLoops = sessions.length;
	$.each(sessions, function(i,v) {
		if(v.id == sessionId) {
			var r = i;
		}

		if(i == (maxLoops - 1)) {
			sessions.splice(r,1);
		}
	});

	Booking.removeDetails(params, function() {
		item.remove();
		$('#session-tickets').find('[data-id="'+ticketId+'"]').addClass('unused-ticket');
	});
	
});

$(document).on('click', '.sessions-finish', function() {
	$(this).html('<i class="fa fa-cog fa-spin"></i> Loading...');
	generateAddonSessions(sessions);
});

var addonTotal = 0;
$(document).on('click', '.assign-addon', function() {
	var addon = [];
	addon.id = $(this).data('id');
	addon.basePrice = parseFloat($(this).parents('li').find('.price').text());
	addon.name = $(this).parents('li').find('.addon-name').text();
	addon.inputQty = parseInt($(this).parents('li').find('input[name="qty"]').val(), 10);

	var summaryItem = $('#addons-summary').find('[data-addon-id="'+addon.id+'"]');

	if(summaryItem.length) {
		addon.qty = parseInt(summaryItem.find('.qty').text(), 10);
		addon.price = parseFloat(summaryItem.find('.price').text(), 10);

		summaryItem.find('.qty').text(addon.qty+addon.inputQty);
		summaryItem.find('.price').text((addon.basePrice*addon.inputQty)+addon.price);
		
		addonTotal += (addon.basePrice * addon.inputQty);
		$('#addons-summary-total').html(addonTotal);
	}else{
		addToAddonSummary(addon);
	}

	addonTotal += (addon.basePrice * addon.inputQty);
	$('#addons-summary-total').html(addonTotal);
	
});

$(document).on('click', '.remove-addon', function() {
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

$(document).on('click', '.addon-finish', function() {
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

function addBookingCustomer(id, done) {

	var param = "id=" + id;

	var addedCustomersTemplate = Handlebars.compile($("#added-customers-template").html());
	var sessionCustomersTemplate = Handlebars.compile($("#session-customers-template").html());

	Customer.getCustomer(param, function success(data){
		$("#added-customers").find('li[data-id="'+id+'"]').remove();
		$("#session-customers").find('a[data-id="'+id+'"]').remove();

		$("#added-customers").append(addedCustomersTemplate(data));
		$("#session-customers").append(sessionCustomersTemplate(data));
		done();
	});
}

function removeBookingCustomer(id) {
	$('#session-customers').find('[data-id="'+id+'"]').remove();
}

function addBookingTicket(id) {
	var param = "id=" + id;
	var sessionTicketsTemplate = Handlebars.compile($("#session-tickets-template").html());

	Ticket.getTicket(param, function success(data){
		$("#session-tickets").append(sessionTicketsTemplate(data));
	});
}

function removeBookingTicket(id) {
	$("#session-tickets").find('[data-id="'+id+'"]').remove();
}

function compileSessionsList(params) {
	if(typeof(params)==='undefined') params = "";
	
	var sessionsTemplate = Handlebars.compile($("#sessions-table-template").html());

	Session.filter(params, function(data){
		$("#sessions-table tbody").html('').append(sessionsTemplate({sessions:data}));
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