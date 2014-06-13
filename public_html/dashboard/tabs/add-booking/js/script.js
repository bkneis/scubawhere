$(function(){
	var tripSource = $("#trip").html();
	var tripTemplate = Handlebars.compile(tripSource);

	$.ajax({
			url: "/company/trips",
			type: "GET",
			dataType: "json",
			async: false,
			success: function(data){
				$.each(data, function(){
					// console.log(tripTemplate(this));
					$("#trips").append(tripTemplate(this));
				});

			}
		});
});

$(function(){
	var agentSource = $("#agent").html();
	var agentTemplate = Handlebars.compile(agentSource);

	$.ajax({
			url: "/api/agent/all",
			type: "GET",
			dataType: "json",
			async: false,
			success: function(data){
				$.each(data, function(){
					// console.log(agentTemplate(this));
					$("#agents").append(agentTemplate(this));
				});

			}
		});
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

function addTrip(trip) {
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
	entry3.appendChild(document.createTextNode(100));
	list3.appendChild(entry3);

	// TALK TO SOREN ABOUT HOW TO MAKE API CALL FOR COST OF TRIP

	alert(trip + " was added");
}
