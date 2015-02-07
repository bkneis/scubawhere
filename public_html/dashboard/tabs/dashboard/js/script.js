window.todaySessions;
window.todayBookings;
var todaySession;
var customerDetails;

Handlebars.registerHelper('getTime', function(obj){
	return obj.substring(obj.length-3, 10);
});

Handlebars.registerHelper('getEnd', function(obj, duration){
	console.log(obj);
	var date = moment(obj);
	date.add(duration, "hours");
	console.log(date);

	return date.format("HH:mm");
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
		for(var i =0; i < data.length; i++) {
			getCustomers(data[i].id);
		}
	},
	function error(xhr){
		console.log('could not retrieve sessions');
	});

	$('#sessions-list').on('click', '.accordion-header', function() {
		$(this).toggleClass('expanded');
		$('.accordion-' + this.getAttribute('data-id')).toggle();
	});

});

function getCustomers(id) {

	customerDetails = Handlebars.compile($('#customer-details-template').html());
	var params = "id=" + id;
	Session.getAllCustomers(params, function sucess(data){
		console.log(data.customers);
		$('#customer-table-'+id).append( customerDetails( {customers : data.customers} ) );
		$('#customers-'+id).DataTable({
        "paging":   false,
        "ordering": false,
        "info":     false,
        "pageLength" : 10,
        "language": {
      		"emptyTable": "There are no customers booked for this trip"
    	}
    	});
	});

}

