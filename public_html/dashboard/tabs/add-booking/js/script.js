var bookingID;
var sessionID;

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
}

// Add selected ticket to both list and select box for dive center to select when filtering through sessions and assigning to a customer
function selectTicket(ticket, id) {

	var customerTickets = document.getElementById("customer-tickets");
	var option = document.createElement("option");
	option.text = ticket;
	option.value = id;
	customerTickets.add(option);

	var selectedTickets = document.getElementById("selected-tickets");
	var entry = document.createElement('li');
	entry.appendChild(document.createTextNode(ticket));
	selectedTickets.appendChild(entry);

	alert(ticket + " was added")

}

// Same as selectTicket but for packages
function selectPackage(package, id) {

	var customerPackages = document.getElementById("customer-packages");
	var option = document.createElement("option");
	option.text = package;
	option.value = id;
	customerPackages.add(option);

	var selectedPackages = document.getElementById("selected-tickets");
	var entry = document.createElement('li');
	entry.appendChild(document.createTextNode(package));
	selectedPackages.appendChild(entry);

	alert(package + " was added")

}

// This is to load the second select box next to the packages options with all tickets related to that package
function displayPackageTickets() {

	var packageID = document.getElementById("customer-packages").value;

	if(packageID == 0) {
		document.getElementById("packages-select").style.display = "none";
		document.getElementById("tickets-select").style.display = "inline";
	}
	else {
		var i;
		var customerPackageTickets = document.getElementById("customer-package-tickets");
		var customerTickets = document.getElementById("tickets-select");

		customerPackageTickets.style.display = "inline";
		customerTickets.style.display = "none";

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

}

/*function displaySessions(id) {

	var i;
	var ticketID = "id=" + id;

	var events = [];

	Ticket.getTicket(ticketID, function success(data) {
		var numTrips = data.trips.length;
		for(i = 0; i < numTrips; i++){
			var event = {
				title : data.trips[i].name,
				duration : data.trips[i].duration
			};

			var param = "ticket_id=" + data.trips[i].id;
			Sessions.filter(param, function success(data2){
				data2.start;
			});
		}
	});
}*/

// Used to calculate the 'end' for the calander by using the start + duration
Date.prototype.addHours= function(h){
    this.setHours(this.getHours()+h);
    return this;
}

function test() {
	var ticketID = "ticket_id=" + 10;

	Sessions.filter(ticketID, function success(data) {
		console.log(data);
	})
}

function test2() {

	var param = "id=" + 1;
	Trip.getSpecificTrips(param, function success(data){
		console.log(data);
	});
	
}

function showSessionsoriginal() {

	var i;
	//var ticketID = 10;
	var ticketID = document.getElementById("customer-tickets").value;

	if(ticketID == 0){
		ticketID = document.getElementById("customer-package-tickets").value;
	}
	var param = "ticket_id=" + ticketID;
	var param2;

	var events = [];

	window.sessions;
	window.trips;

	Sessions.filter(param, function success(data) {

		var numSessions = data.length;

		for(i = 0; i < numSessions; i++){
			
		// need to create new object as array is pointing to old object that has been changed. Create a new object each time for unique

			var event = 
			{
				title : null,
				start : data[i].start,
				end : null,
				sessionID : data[i].id
			};

			//event.start = data[i].start;
			//event.sessionID = data[i].id;

			param2 = "id=" + data[i].trip_id;
			//console.log(param2);

			Trip.getSpecificTrips(param2, function success(data2){
				event.title = data2.name;
				event.end = null; //event.start.setHours ( event.start.getHours() + event.duration ); // FIX add start to duration to get en
				console.log(data2);
			});

			events.push(event);
			console.log(event);
		}

		console.log(events);

	});

	$('#calendar').fullCalendar('removeEvents');  //Removes all events

	$('#calendar').fullCalendar( 'addEventSource', events); // load the new source

	$('#calendar').fullCalendar( 'refetchEvents' );

}

function showSessions() {

	$('#calendar').fullCalendar('removeEvents');  //Removes all events

	var i;
	//var ticketID = 10;
	var ticketID = document.getElementById("customer-tickets").value;

	if(ticketID == 0){
		ticketID = document.getElementById("customer-package-tickets").value;
	}
	var param = "ticket_id=" + ticketID;
	var param2;

	window.sessions;
	window.trips;

	Sessions.filter(param, function success(data) {

		window.sessions = _.indexBy(data, 'id');
		//console.log(window.sessions);

		_.each(window.sessions, function(value) {

			param2 = "id=" + value.trip_id;
			//console.log(param2);

			Trip.getSpecificTrips(param2, function success(data2){

				var startTime = $.fullCalendar.moment.utc(window.sessions[value.id].start);
				var endTime = $.fullCalendar.moment(startTime).add('hours', window.sessions[value.id].duration);

				var event = 
				{
					title : data2.name,
					start : startTime,
					//start : window.sessions[value.id].start,
					end : endTime,
					sessionID : window.sessions[value.id].id
				};
				$('#calendar').fullCalendar( 'renderEvent', event, true );
				//console.log(event);
			});
		});

		
	});

}

function test5() {
	param = "id=1";
	Trip.getSpecificTrips(param, function success(data){
		console.log(data);
	})
}

function addCustomer(count){

	var firstName = document.getElementById("fname"+count).value;
	var firstName = document.getElementById("lname"+count).value;
	var customerID;

	var params = 
	{
		_token : window.token,
		firstname : firstName,
		lastname : lastName,
		email : null,
		country_id : null,
		phone : null
	};

	var checkLead = document.getElementById("is_lead"+count).value;

	if(checkLead == 1){ // So they are the lead customer
		params.email = document.getElementById("email"+count).value;
		//params.country_id = document.getElementById("country"+count).value;
		params.country_id = "en";
		params.phone = document.getElementById("phone"+count).value;
	}

	Customer.createCustomer(params, function sucess(data){
		console.log(data);
		document.getElementById("add-cust-"+count).style.display = "none";
		customerID = data.id;

		var customerSelect = document.getElementById("customers");
		var option = document.createElement("option");
		option.text = firstName + " " + lastName;
		option.value = customerID;
		option.setAttribute("data-lead") = checkLead;
		option.setAttribute("data-count") = count;
		customerSelect.add(option);

		// add customer list
		var div = document.createElement('div');
		div.setAttribute('id', 'customer-'+count);
		var ul = document.createElement('ul');
		ul.setAttribute('id', 'customer-'+count+'-trips');

		var custDiv = document.getElementById('customers-trips-summary');
		div.appendChild(ul);
		custDiv.appendChild(div);
	});

}


// Used to assign customers their ticket and send API call to add detials of booking
function assignTicket() {

	//var customerID = document.getElementById("customers").value; - TRY NOW ADDED
	//var isLead = document.getElementById("customer-id").getAttribute("data-lead"); - TRY NOW ADDED
	//var customerCount = document.getElementById("customer-id").getAttribute("data-count");
	var ticketID = document.getElementById("customer-tickets").value;
	//var sessionID = document.getElementById("session-id").value;
	var packageID = document.getElementById("customer-packages").value;

	if(packageID == 0){
		packageID = null;
	}

	if(ticketID == 0){
		ticketID = document.getElementById("customer-package-tickets").value;
	}

	var params = 
	{
		_token : window.token,
		booking_id : bookingID,
		customer_id : 1, //customerID
		is_lead : 1, // isLead
		ticket_id : ticketID,
		session_id : sessionID,
		package_id : null 
	};

	Booking.addDetails(params, function success(data){
		console.log(data);
		//var tripItem = document.createElement('li');
		//tripItem.innerHTML = trip name and date
		//var custDiv = document.getElementById("customer-"+count+"-trips");
		//custDiv.appendChild(tripItem);
	});

}

function testadd() {

	var data = 
	{
		_token : window.token,
		booking_id : 1,
		customer_id : 1,//customerID
		is_lead : 1,
		ticket_id : ticketID,
		session_id : 1,
		package_id : null 
	};

	Booking.addDetails(data, function success(data){
		console.log(data);
	});

}

function test4() {

	var param = 
	{	
		_token : window.token, 
		firstname : "bryan", 
		lastname : "kneis", 
		email : "bryan2@iqwebcreations.com",
		birthday : "24-05-2014",
		gender : 1,
		address_1 : "46 ameythst road",
		//address_2 : "christchurch",
		city : "bournemouth",
		county : "dorset",
		postcode : "bh234eb",
		country_id : 1,
		phone : "07866965048",
		certificate_id : "57",
		last_dive : "22-04-2013"
	};

	Customer.createCustomer(param, function success(data){
		console.log(data);
	});
	
	/*$.ajax({
			type: "POST",
			url: "/api/customer/add",
			data: param,
			success: function success(data) {console.log(data);},
			error: function error(){console.log("fasil");}
		});*/

}

