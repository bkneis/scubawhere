window.todaySessions;
window.todayBookings;
var todaySession;
var customerDetails;

Handlebars.registerHelper('getTime', function(obj){
	return obj.substring(obj.length, 10);
});

Handlebars.registerHelper('getPer', function(capacity){
	var booked = capacity[0];
	var max = capacity[1];
	return parseInt((booked / max) * 100) + '%';
});

$(function () {

	todaySession = Handlebars.compile($('#today-session-template').html());
	Session.getToday(function success(data){
		window.todaySessions = _.indexBy(data, 'id');
		console.log(data);
		$('#sessions-list').append( todaySession( {sessions : data} ) );
		//getAllLocations(data);
	},
	function error(xhr){
		console.log('could not retrieve sessions');
	});

	$("#start-tour").on('click', function(event) {
		var introd = introJs();
          introd.setOptions({
            steps: [
              { 
                intro: "Welcome to scubawhereRMS. This is the dashboard, where you will find an overview of the most relevant information for the day ahead."
              },
              {
                element: '#todays-sessions',
                intro: 'Here is a summary of the trips to depart today',
                position : 'right',
                step : 1
              },
              {
              	element: '#sessions-list',
              	intro: 'If you click on a trip, it displays all of the customers on that trip',
              	position: 'right'
              },
              {
                element: '#todays-stats',
                intro: 'Content goes here',
                position : 'left'
              },
              {
                element: '#recent-bookings',
                intro: 'Here is a brief summary of the last 5 bookings. Click on a booking to view its transactions or edit it.',
                position : 'right'
              },
              {
                element: '#feedback-form',
                intro: 'If you experience any bugs within our system, or have any suggestions on improving it, please feel free to tell us!',
                position : 'left'
              }
            ],
            showStepNumbers : false
          });
			introd.onchange(function(targetElement) {
			    console.log(targetElement.id); 
			    switch (targetElement.id) 
			        { 
			        case "sessions-list": 
			            //document.getElementById("todays-session-143").click();
			        break; 
			        }
			});
		introd.start();/*.oncomplete(function() {
        	window.location.href = '#accommodations?multipage=true';
        });*/
	});

	$('#sessions-list').on('click', '.accordion-header', function() {
		$(this).toggleClass('expanded');
		$('.accordion-' + this.getAttribute('data-id')).toggle();
	});

	//$('.cust-tbl').dataTable();

	/*customerDetails = Handlebars.compile($('#customer-details-template').html());
	Booking.getToday(function success(data){
		window.todayBookings = _.indexBy(data, 'id');
		console.log(data);
		for(var i = 0; i < data.length; i++){
			$('#customer-table-'+data[i].id).append( customerDetails( {customers : data} ) );
		}
	});*/	 

});

/*function getLocations(params, i, sessions){
	Trip.getSpecificTrip(params, function sucess(data){
		var spots = "";
		for(var j=0; j < data.locations.length; j++){
			if(! j == (data.locations.length - 1)) spots += (data.locations[j].name + ' , ');
			else spots += (data.locations[j].name);
		}
		$('#locations-'+sessions[i].id).append(spots);
	});
}

function getAllLocations(sessions){

	for(var i=0; i < sessions.length; i++){
		var params = 'id=' + sessions[i].trip.id;
		getLocations(params, i, sessions);
	}
}*/
