$(function(){

	var tripSource = $("#trips-list-template").html();
	var tripTemplate = Handlebars.compile(tripSource);

	Ticket.getAllTickets(function(data){
					$("#trips").append(tripTemplate({tickets:data}));
				});

	var agentSource = $("#agents-list-template").html();
	var agentTemplate = Handlebars.compile(agentSource);

	Agent.getAllAgents(function(data){
					$("#agents").append(agentTemplate({agents:data}));
				});

	var packageSource = $("#packages-list-template").html();
	var packageTemplate = Handlebars.compile(packageSource);

	Package.getAllPackages(function(data){
					$("#packages").append(packageTemplate({packages:data}));
				});

	/*var packageTicketSource = $("#package-tickets-list-template").html();
	var packageTicketTemplate = Handlebars.compile(packageTicketSource);

	Package.getPackage(packageId, function(data){
					$("#cust-package-tickets").append(packageTemplate({tickets:data}));
				});*/
});

function validateTob() {
	var choice = document.getElementById("tob");
	if(choice.value == "agent") {
		document.getElementById('agent-info').style.display = 'block';
	}
	else {
		document.getElementById('agent-info').style.display = 'none';
	}
}

function addTrip(trip, cost, id) {
	//add id into hidden list to refrence for calander and packages
	var list = document.getElementById('selected-trips-list');
	var entry = document.createElement('li');
	entry.appendChild(document.createTextNode(trip));
	entry.value = id;
	list.appendChild(entry);

	var list2 = document.getElementById('trips-list');
	var entry2 = document.createElement('li');
	entry2.appendChild(document.createTextNode(trip));
	list2.appendChild(entry2);

	var list3= document.getElementById('trips-cost-list');
	var entry3 = document.createElement('li');
	entry3.appendChild(document.createTextNode(cost));
	list3.appendChild(entry3);

	alert(trip + " was added");

	var x = document.getElementById("cust-trips");
	var option = document.createElement("option");
	option.text = trip;
	x.add(option);
	
}

function addPackage(trip, cost) {
	var list = document.getElementById('selected-trips-list');
	var entry = document.createElement('li');
	entry.appendChild(document.createTextNode(trip));
	list.appendChild(entry);

	var list2 = document.getElementById('trips-list');
	var entry2 = document.createElement('li');
	entry2.appendChild(document.createTextNode(trip));
	list2.appendChild(entry2);

	var list3= document.getElementById('trips-cost-list');
	var entry3 = document.createElement('li');
	entry3.appendChild(document.createTextNode(cost));
	list3.appendChild(entry3);

	alert(trip + " was added");

	var x = document.getElementById("cust-packages");
	var option = document.createElement("option");
	option.text = trip;
	x.add(option);
	
}

function tripSelect() {
	var tripsDiv = document.getElementById('trips-select');
	var tripsCombo = document.getElementById('cust-trips');
	var packagesDiv = document.getElementById('packages-select');
	var tripsVal = tripsCombo.value;

	if(tripsVal == 0) {
		packagesDiv.style.display = "inline";
	}
	else {
		packagesDiv.style.display = "none";
	}
}

function packageSelect() {
	var tripsDiv = document.getElementById('trips-select');
	var packagesDiv = document.getElementById('packages-select');
	var packagesCombo = document.getElementById('cust-packages');
	var packageTripCombo = document.getElementById('cust-package-tickets');
	var packagesVal = packagesCombo.value;

	if(packagesVal == 0) {
		tripsDiv.style.display = "inline";
		packageTripCombo.style.display = "none";
	}
	else {
		tripsDiv.style.display = "none";
		packageTripCombo.style.display = "inline";
	}
}

function test() {

	var index;

	var tripId = document.getElementById('cust-package-tickets').value;

	Sessions.getAllSessions(function success(data) {
		var session = data[index];
		for(index=0; index<data.length; index++){
			//console.log(data[index].trip_id);
			if(session.trip_id == tripId){
				//console.log(data[index].start);
				Ticket.getTicket(session.trip_id, function success2(data) {
					console.log(data);
				})
			}
		}
	});

}