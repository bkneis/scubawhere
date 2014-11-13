var booking = {};

window.token;
$.get("/token", null, function(data) {
	window.token = data;
});

Handlebars.registerHelper("freeSpaces", function(capacity) {
	var freeSpaces = capacity[1] - capacity[0];
	return freeSpaces;
});

Handlebars.registerHelper("tripFinish", function(start, duration) {
	var d = new Date(start);
	d.setHours(d.getHours()+duration);
	var f = d.toISOString().slice(0, 19).replace('T', ' ');

	return friendlyDate(f);
});

Handlebars.registerHelper("friendlyDate", function(d) {
	return friendlyDate(d);
});

function friendlyDate(date) {
	var d = new Date(date);

	//Adds 0 to single digits for date/times.
	function addZ(n){return n<10? '0'+n:''+n;}

	//Why doesn't javascript have a nice Date like PHP?!
	return d.getDate()+"/"+(addZ(d.getMonth()+1))+"/"+(d.getFullYear())+" "+(addZ(d.getHours()))+":"+(addZ(d.getMinutes()));
}

// Load all of the agents, tickets and packages for dive center to select
$(function(){

	var agentTemplate = Handlebars.compile($("#agents-list-template").html());

	Agent.getAllAgents(function(data){
		$("#agents").append(agentTemplate({agents:data}));
	});

	var ticketTemplate = Handlebars.compile($("#tickets-list-template").html());

	Ticket.getAllTickets(function(data){
		$("#tickets").append(ticketTemplate({tickets:data}));
	});

	var tripTemplate = Handlebars.compile($("#trips-list-template").html());

	Trip.getAllTrips(function(data){
		$("#trips").append(tripTemplate({trips:data}));
	});

	var packageTemplate = Handlebars.compile($("#packages-list-template").html());

	Package.getAllPackages(function(data){
		$("#available-packages").append(packageTemplate({packages:data}));
	});

	var addonsTemplate = Handlebars.compile($("#addons-template").html());

	Addon.getAllAddons(function(data){
		$("#addons").append(addonsTemplate({addons:data}));
	});

	var customersTemplate = Handlebars.compile($("#customers-list-template").html());

	Customer.getAllCustomers(function(data){
		$("#existing-customers").append(customersTemplate({customers:data}));
	});

});

$(document).ready(function() {

	$('#agent-info').hide();
	$('#existing-customers').select2();
	$('#trips').select2();
	var token = getToken();

	$(document).on('click', '.booking-source a', function() {
		listGroupRadio($(this), 'btn-primary');

		if($(this).data('type') == 'agent') {
			$('#agent-info').slideDown();
		}else{
			$('#agent-info').slideUp();
		}
	});

	$(document).on('click', '.list-group-radio', function(e) {
		listGroupRadio($(this));
	});

	$(document).on('click', '.source-finish', function() {
		$(this).html('<i class="fa fa-cog fa-spin"></i> Initiating...');

		var type = $('.booking-source').children('.active').first().data("type");

		if(type == "agent") {
			var agentId = $('#agents').children('.active').data('id');
			var params = [{name: "_token", value: window.token}, {name: "agent_id", value: agentId}];
		}else{
			var params = [{name: "_token", value: window.token}, {name: "source", value: type}];
		}

		Booking.initiate(params, function(data) {
			$('[data-target="#ticket-tab"]').tab('show');
			booking.id = data.id;
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

	var sessionTicketsTemplate = Handlebars.compile($("#session-tickets-template").html());

	function addBookingTicket(id) {
		var param = "id=" + id;

		Ticket.getTicket(param, function success(data){
			$("#session-tickets").append(sessionTicketsTemplate(data));
		});
	}

	$(document).on('click', '.remove-ticket', function() {
		var id = $(this).data('id');
		var ticket = $('#ticket-'+id);
		var qty = parseInt(ticket.find('.qty').text(), 10);

		if(qty > 1) {
			ticket.find('.qty').text(qty-1);
		}else{
			ticket.remove();
		}
	});

	$(document).on('click', '.tickets-finish', function() {
		$('[data-target="#customer-tab"]').tab('show');	
	});

	//Customers

	$(document).on('click', '.add-customer', function() {
		var id = $('#existing-customers').val();
		addBookingCustomer(id);
	});

	$(document).on('click', '.remove-customer', function() {
		$(this).parents('.list-group-item').remove();
	});

	$('#new-customer').submit(function(e) {
		e.preventDefault();
		var form = $(this);
		var params = form.serializeArray();
		params.push({name: "_token", value: window.token});

		Customer.createCustomer(params, function success(data){
			addBookingCustomer(data.id);
			form[0].reset();
		});
	});

	$(document).on('click', '.clear-customer', function() {
		$(this).parents('form')[0].reset();
	});

	$(document).on('click', '.customers-finish', function() {
		$('[data-target="#session-tab"]').tab('show');
		compileSessionsList();
	});

	var addedCustomersTemplate = Handlebars.compile($("#added-customers-template").html());
	var sessionCustomersTemplate = Handlebars.compile($("#session-customers-template").html());

	function addBookingCustomer(id) {
		var param = "id=" + id;

		Customer.getCustomer(param, function success(data){
			$("#added-customers").append(addedCustomersTemplate(data));
			$("#session-customers").append(sessionCustomersTemplate(data));
		});
	}

	function compileSessionsList() {
		var sessionsTemplate = Handlebars.compile($("#sessions-table-template").html());

		Session.filter("", function(data){
			$("#sessions-table tbody").html('').append(sessionsTemplate({sessions:data}));
		});
	}

	$(document).on('click', '.assign-session', function() {
		var sessionId = $(this).data('id');
		var customerId = $('#session-customers').children('.active').first().data('id');
		var ticketId = $('#session-tickets').children('.active').first().data('id');

		booking.session_id = sessionId;
		booking.customer_id = customerId;
		booking.ticket_id = ticketId;

		$("#free-spaces"+sessionId).html('<i class="fa fa-refresh fa-spin"></i>');

		var params = [
			{name: "_token", value: window.token},
			{name: "booking_id", value: booking.id},
			{name: "customer_id", value: customerId},
			{name: "is_lead", value: false},
			{name: "ticket_id", value: ticketId},
			{name: "session_id", value: sessionId}
		];

		Booking.addDetails(params, function(data) {
			compileSessionsList();
			btn.html('Assign');
		});
	});

	var addonTotal = 0;
	$(document).on('click', '.add-addon', function() {
		var id = $(this).data('id');
		var basePrice = parseFloat($(this).parents('li').find('.price').text());
		var name = $(this).parents('li').find('.addon-name').text();
		var inputQty = parseInt($(this).parents('li').find('input[name="qty"]').val(), 10);

		if($('#addons-basket').find('#addon-'+id).length) {
			var qty = parseInt($('#addon-'+id).find('.qty').text(), 10);
			var price = parseFloat($('#addon-'+id).find('.price').text(), 10);

			$('#addon-'+id).find('.qty').text(qty+inputQty);
			$('#addon-'+id).find('.price').text((basePrice*inputQty)+price);
		}else{
			var qty = inputQty;
			var price = (basePrice * qty);
			$('#addons-basket').append('<p class="list-group-item-text addon-item" id="addon-'+id+'" data-id="'+id+'"><a href="javascript:void(0);" title="Click to remove addon" class="remove-addon" data-id="'+id+'">'+name+'</a> <span class="badge qty">'+qty+'</span> <span class="price pull-right">'+price+'</span></p>');
		}

		addonTotal += (basePrice * inputQty);
		$('#addons-total').html('£'+addonTotal);
	});

	$(document).on('click', '.remove-addon', function() {
		var id = $(this).data('id');
		var addon = $('#addon-'+id);
		var price = $('#addon-'+id).find('.price').text();
		var basePrice = $('#baseprice-'+id).text();
		var qty = parseInt(addon.find('.qty').text(), 10);

		if(qty > 1) {
			addon.find('.qty').text(qty-1);
			addon.find('.price').text(price-basePrice);
		}else{
			addon.remove();
		}

		addonTotal -= basePrice;
		$('#addons-total').html('£'+addonTotal);
	});

	$(document).on('click', '.addon-finish', function() {
		var btn = $(this);
		btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

		$('#addons-basket').children('.addon-item').each(function(k,v) {
			var params = [
				{name: "_token", value: window.token},
				{name: "booking_id", value: booking.id},
				{name: "session_id", value: booking.session_id},
				{name: "customer_id", value: booking.customer_id},
				{name: "addon_id", value: $(v).data("id")},
				{name: "quantity", value: $(v).find(".qty").text()}
			];

			console.log(params);

			Booking.addAddon(params, function(data) {
				btn.html('Next');
				$('[data-target="#detail-tab"]').tab('show');
			});
		});

		
	});

});

function listGroupRadio(selector, additionalClass) {
	//This function treats list-group-items (http://getbootstrap.com/components/#list-group) like radios buttons
	selector.siblings().removeClass('active '+additionalClass, selector.siblings().hasClass('active '+additionalClass));
	selector.toggleClass('active '+additionalClass, !selector.siblings().hasClass('active '+additionalClass));
}