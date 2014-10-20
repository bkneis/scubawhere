var bookingID;
var sessionID = 0;
var startDate;
var endDate;
var bookingCost = 0;
//var numCustomers = 0;
var customersArray = [];

var init = false;

window.token;
$.get("/token", null, function(data) {
	window.token = data;
});

// Load all of the agents, tickets and packages for dive center to select
$(function(){

	var ticketSource = $("#tickets-list-template").html();
	var ticketTemplate = Handlebars.compile(ticketSource);

	Ticket.getAllTickets(function(data){
		$("#available-tickets").append(ticketTemplate({tickets:data}));
	});

	var agentSource = $("#agents-list-template").html();
	var agentTemplate = Handlebars.compile(agentSource);

	Agent.getAllAgents(function(data){
		$("#agents").append(agentTemplate({agents:data}));
	});

	var packageSource = $("#packages-list-template").html();
	var packageTemplate = Handlebars.compile(packageSource);

	Package.getAllPackages(function(data){
		$("#available-packages").append(packageTemplate({packages:data}));
	});

	var addonsSource = $("#addons-template").html();
	var addonsTemplate = Handlebars.compile(addonsSource);

	Addon.getAllAddons(function(data){
		$("#addons").append(addonsTemplate({addons:data}));
	});

});

// Dispaly agents option if source of booking is through an agent
function validateSob() {
	var choice = document.getElementById("sob").value;
	if(choice == "agent") {
		document.getElementById('agent-info').style.display = 'block';
	}
	else {
		document.getElementById('agent-info').style.display = 'none';
	}
}

//Initiate the booking process by sending API source of booking details, either agent_id or source
//Also recieve back booking ID to add details to
function initiate() {

	if(!init){
		var choice = document.getElementById("sob").value;


		if(choice == "agent") {
			var agentID = document.getElementById("agents").value;
			// var agentID = agents.options[agents.selectedIndex].id;
			var data = {_token : window.token, agent_id : agentID};
		}
		else {
			var data = {_token : window.token, source : choice};
		}

		Booking.initiate(data, function success(data) {
			alert("Booking initiated");
			bookingID = data.id;
			console.log(bookingID);
		});

		init = true;
	}
}

// Add selected ticket to both list and select box for dive center to select when filtering through sessions and assigning to a customer
function selectTicket(ticket, id, price) {

	var customerTickets = document.getElementById("customer-tickets");
	var option = document.createElement("option");
	option.text = ticket;
	option.value = id;
	option.setAttribute("data-price", price);
	bookingCost += parseFloat(price);
	var totalBookingCost = document.getElementById("totalBookingCost");
	totalBookingCost.innerHTML = bookingCost;
	//console.log(bookingCost);
	customerTickets.add(option);

	var selectedTickets = document.getElementById("selected-tickets");
	var entry = document.createElement('li');
	entry.appendChild(document.createTextNode(ticket));
	selectedTickets.appendChild(entry);

	alert(ticket + " was added")

}

// Same as selectTicket but for packages
function selectPackage(package, id, price) {

	/*var customerPackages = document.getElementById("customer-packages");
	var option = document.createElement("option");
	option.text = package;
	option.value = id;
	option.setAttribute("data-price", price);*/
	bookingCost += parseFloat(price);
	var totalBookingCost = document.getElementById("totalBookingCost");
	totalBookingCost.innerHTML = bookingCost;
	//customerPackages.add(option);

	var selectedPackages = document.getElementById("selected-tickets");
	var entry = document.createElement('li');
	entry.appendChild(document.createTextNode(package));
	selectedPackages.appendChild(entry);
	alert(package + " was added");

	var param = "id=" + id;
		var customerPackageTickets = document.getElementById("customer-tickets");
		Package.getPackage(param, function success(data){
			console.log(data.tickets);
			console.log(data);
			var numTickets = data.tickets.length;
			for(var i=0; i < numTickets; i++) {
				var quantity = data.tickets[i].pivot.quantity;
				var optGroup = document.createElement('optgroup')
				optGroup.label = package;
				for(var j=0; j < quantity; j++){
					var option = document.createElement("option");
					option.text = data.tickets[i].name;
					option.value = data.tickets[i].id;
					option.setAttribute("data-price", (price / quantity));
					option.setAttribute("data-package", id);
					optGroup.appendChild(option);
					//customerPackageTickets.add(option);
				}
				customerPackageTickets.add(optGroup);
			}
		}); 

}

// This is to load the second select box

	/*var packageID = document.getElementById("customer-packages").value;

	if(packageID == 0) {
		document.getElementById("packages-select").style.display = "none";
		document.getElementById("tickets-select").style.display = "inline";
	}
	else {
		var i;
		var customerPackageTickets = document.getElementById("customer-tickets");
		//var customerPackageTickets = document.getElementById("customer-package-tickets");
		var customerTickets = document.getElementById("tickets-select");

		//customerPackageTickets.style.display = "inline";
		//customerTickets.style.display = "none";

		var param = "id=" + packageID;
		Package.getPackage(param, function success(data){
			//console.log(data.tickets);
			var numTickets = data.tickets.length;
			for(i=0; i < numTickets; i++) {
				var option = document.createElement("option");
				option.text = data.tickets[i].name;
				option.value = data.tickets[i].id;
				customerPackageTickets.add(option);
			}
		}); 
	}

}*/ 

function selectPackageTicket() {

		var param = "id=" + packageID;
		var customerPackageTickets = document.getElementById("customer-tickets");
		Package.getPackage(param, function success(data){
			//console.log(data.tickets);
			var numTickets = data.tickets.length;
			for(var i=0; i < numTickets; i++) {
				var option = document.createElement("option");
				option.text = data.tickets[i].name;
				option.value = data.tickets[i].id;
				customerPackageTickets.add(option);
			}
		});
}

function getToday() {
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();

	if(dd<10) {
		dd='0'+dd
	} 

	if(mm<10) {
		mm='0'+mm
	} 

	today = yyyy + '-' + mm + '-' + dd;
	//alert(today);
	return today;
}

function showSessions() {

	$('#calendar').fullCalendar('removeEvents');  //Removes all events

	var i;
	//var ticketID = 10;
	var ticketID = document.getElementById("customer-tickets").value;
	//console.log(ticketID);
	
	//var param = "ticket_id=" + ticketID + "&after=2014-07-01";
	var param = "ticket_id=" + ticketID + "&after=" + getToday();
	var param2;

	window.sessions;
	window.trips;

	Session.filter(param, function success(data) {

		window.sessions = _.indexBy(data, 'id');
		//console.log(window.sessions);

		_.each(window.sessions, function(value) {

			param2 = "id=" + value.trip_id;
			//console.log(param2);

			Trip.getSpecificTrips(param2, function success(data2){

				var startTime = $.fullCalendar.moment.utc(window.sessions[value.id].start);
				var endTime = $.fullCalendar.moment(startTime).add('hours', data2.duration);
				//var endTime = $.fullCalendar.moment(startTime).add('hours', window.sessions[value.id].duration);
				//console.log(window.sessions[value.id].duration);
				//console.log("duration " + data2.duration);

				var event = 
				{
					title : data2.name,
					start : startTime,
					end : endTime,
					sessionID : window.sessions[value.id].id
				};
				$('#calendar').fullCalendar( 'renderEvent', event, true );
				//console.log(event);
			});
		});

		
	});

}

function validateLead(params) {
	var email = params.email;
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

	if (!filter.test(email)) {
		alert('Please provide a valid email address');
		return false;
	}
	else if(!params.phone.match(/^\d+$/)){
		alert("Please enter a valid phone number");
		return false;
	}
	else if(params.firstname == "" || params.firstname == null || params.lastname == "" || params.lastname == null || params.country_id == 0){
		alert("Please fill in all details for lead customer");
		return false;
	}
	else return true;
}

function addCustomer(count, checkLead){

	var btn = document.getElementById("add-cust-"+count);
	if(btn.getAttribute("task") == "edit") {
		editCustomer(count);
	}
	else {

		var firstName = document.getElementById("fname"+count).value;
		var lastName = document.getElementById("lname"+count).value;
		var customerID;
		var validated = false;

		var params = 
		{
			_token : window.token,
			firstname : firstName,
			lastname : lastName,
			email : null,
			country_id : 1,
			phone : null
		};

		//var checkLead = document.getElementById("is_lead"+count).checked;
		console.log(checkLead);

		if(checkLead){ // So they are the lead customer
			params.email = document.getElementById("email"+count).value;
			//params.country_id = document.getElementById("leadCountry").value;
			params.country_id = 1;
			params.phone = document.getElementById("phone"+count).value;

			validated = validateLead(params);
		}
		else {
			if(params.firstname == "" || params.lastname == "" ){
				alert("Please fill in the customers first and last name");
			}
			else {
				validated = true;
			}
		}

		if(validated) {
			Customer.createCustomer(params, function sucess(data){
				console.log(data);
			//document.getElementById("add-cust-"+count).style.display = "none";
			document.getElementById("add-cust-"+count).innerHTML = "Edit Customer";
			customerID = data.id;

			var customerSelect = document.getElementById("customers");
			var option = document.createElement("option");
			option.text = firstName + " " + lastName;
			option.value = customerID;
			option.setAttribute("data-lead", checkLead);
			option.setAttribute("data-count", count);
			customerSelect.add(option);
			//numCustomers++;
			document.getElementById("assign-ticket").style.display = "inline";
			var customerObject = {count : count, id : customerID};
			customersArray.push(customerObject);
			btn.setAttribute("task", "edit");
			console.log(customersArray);
		});
		}

	}
}

function editCustomer(count) {

	console.log(count);
	var i;
	var custID;
	var validated = false;
	for(i = 0; i < customersArray.length; i++){
		if(customersArray[i].count == count){
			custID = customersArray[i].id;
			console.log(custID);
			break;
		}
	}

	var firstName = document.getElementById("fname"+count).value;
	var lastName = document.getElementById("lname"+count).value;

	var params = 
	{
		_token : window.token,
		id : custID,
		firstname : firstName,
		lastname : lastName,
		email : null,
		country_id : 1,
		phone : null
	};

	var checkLead = document.getElementById("is_lead"+count).checked;
	console.log(checkLead);

	if(checkLead){ // So they are the lead customer
		params.email = document.getElementById("email"+count).value;
		params.country_id = 1;
		params.phone = document.getElementById("phone"+count).value;
		validated = validateLead(params);
	}
	else {
		if(params.firstname == "" || params.lastname == "" ){
			alert("Please fill in the customers first and last name");
		}
		else {
			validated = true;
		}
	}

	if(validated){
		Customer.updateCustomer(params, function success(data) {
			console.log(data);
			alert("Customer Updated");
		});
	}
}

function test9() {
	$('#calendar').fullCalendar('render');
}

function addAddon(){
	var addons = document.getElementById('addons');
	var addonIDsub = addons.options[addons.selectedIndex].id;
	var addonID = addonIDsub.substring(5);
	var customerID = document.getElementById("customers").value;

	var params = 
	{
		_token : window.token,
		booking_id : bookingID,
		session_id : sessionID,
		customer_id : customerID,
		addon_id : addonID
	};

	Booking.addAddon(params, function success(data){
		console.log(data);
	});
}

// Used to assign customers their ticket and send API call to add detials of booking
function assignTicket() {

	var validated = false;
	var customer = document.getElementById("customers");
	var trip = document.getElementById("customer-tickets");

	if(customer.value == 0 || trip.value == 0 || sessionID == 0) {
		alert("Please select a customer a ticket and a session to assign the trip");
	}
	else {
		validated = true;
	}

	if(validated){

		var customerID = document.getElementById("customers").value;
		var isLead = customer.options[customer.selectedIndex].getAttribute("data-lead"); //- TRY NOW ADDED!!!!!!!!!!!
		//var customerCount = document.getElementById("customers").getAttribute("data-count");
		var ticketID = trip.value;
		//var packageID = document.getElementById("customer-packages").value;
		var packageID = trip.options[trip.selectedIndex].getAttribute("data-package");
		var customerName = customer.options[customer.selectedIndex].text;
		var tripName = trip.options[trip.selectedIndex].text;
		var price = trip.options[trip.selectedIndex].getAttribute("data-price");
		//console.log(trip.options[trip.selectedIndex]); // chnage vars for trip to ticket
		var params = 
		{
			_token : window.token,
			booking_id : bookingID,
			customer_id : customerID,
			is_lead : isLead,
			ticket_id : ticketID,
			session_id : sessionID,
			package_id : packageID
		};
		/*if(ticketID == 0){
			ticketID = document.getElementById("customer-package-tickets").value;
		}*/
		
		Booking.addDetails(params, function success(data){
			console.log(data);
			trip.remove(trip.selectedIndex);
			var table = document.getElementById("customers-trips-table");
			//alert(isLead); - TALK TO SOREN, ALWAYS SAVES TO DB AS 0
			var row = table.insertRow(-1);
			var cell1 = row.insertCell(0);
			var cell2 = row.insertCell(1);
			var cell3 = row.insertCell(2);
			var cell4 = row.insertCell(3);
			var cell5 = row.insertCell(4);
			cell1.innerHTML = customerName;
			cell2.innerHTML = tripName;
			cell3.innerHTML = String(startDate).slice(0, -8);
			cell4.innerHTML = String(endDate).slice(0, -8);
			cell5.innerHTML = price;
			alert("Trip Assigned");
			if(document.getElementById("customer-tickets").length == 1){
				$.fancybox.close();
			}
			$('#calendar').fullCalendar('removeEvents');
			sessionID = 0;
		});
	
		var numAddons = document.getElementById("addons").length;
		console.log(numAddons);
		if(numAddons > 1){
			addAddon();
		}

	}
}

function validateBooking() {

	var cash = document.getElementById('pay-cash').value;
	var card = document.getElementById('pay-card').value;
	var cheque = document.getElementById('pay-cheque').value;
	var bank = document.getElementById('pay-bank').value;
	var pob = document.getElementById('pay-pob').value;

	/*var params = {
		_token : window.token,
		booking_id : bookingID
	}*/
	var params = "booking_id=" + bookingID;

	if((cash + card + cheque + bank + pob) == bookingCost){
		console.log(params);
		Booking.validateBooking(params, function success(data){
			console.log(data);
			alert('Booking is validated, you can view/edit your booking in manage bookings');
		})
	}
	else {
		alert('Please ensure the amount paid is the same as the total booking cost');
	}
}

function refreshCal() {

	$("#calendar").fullCalendar( 'changeView', 'basicWeek' );

}

function selectAddons() {

	
}
