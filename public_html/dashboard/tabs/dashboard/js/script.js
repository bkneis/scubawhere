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
		$('#accordion').append( todaySession( {sessions : data} ) );
		getAllLocations(data);
	},
	function error(xhr){
		console.log('could not retrieve sessions');
	});

	/*customerDetails = Handlebars.compile($('#customer-details-template').html());
	Booking.getToday(function success(data){
		window.todayBookings = _.indexBy(data, 'id');
		console.log(data);
		for(var i = 0; i < data.length; i++){
			$('#customer-table-'+data[i].id).append( customerDetails( {customers : data} ) );
		}
	});*/

	//$('#accordion').on('shown.bs.collapse', toggleChevron);
	//$('#accordion').on('hidden.bs.collapse', toggleChevron);

});

function getLocations(params, i, sessions){
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
}