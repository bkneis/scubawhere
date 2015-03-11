var tripsList;
var sessionList;
$(function() {

	tripsList = Handlebars.compile($("#trips-list-template").html());
	Trip.getAllTrips(function(data) {
		$("#trip-select").append( tripsList({trips : data}) );
	});

	$('input.datepicker').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		}
	});

	$('.reports-table').DataTable({
		"paging":   false,
		"ordering": false,
		"info":     false,
		"pageLength" : 10,
		"searching" : false,
		"language": {
			"emptyTable": "There are no trips between these dates"
		}
	});

	$("#start-date").val(getDates().lastWeek);
	$("#end-date").val(getDates().todayDate);

	var dates = {
		after : $("#start-date").val(),
		before : $("#end-date").val()
	};

	sessionList = Handlebars.compile($("#sessions-list-template").html());
	Session.filter(dates, function success(data) {
		console.log(data);
		$("#sessions-table").append( sessionList({sessions : data}) );
	});

});

function getDates() {
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

	var lastWeek = new Date();
	lastWeek.setDate(today.getDate() - 7);
	var dd2 = lastWeek.getDate();
	var mm2 = lastWeek.getMonth()+1; //January is 0!
	var yyyy2 = lastWeek.getFullYear();

	if(dd2<10) {
	    dd2='0'+dd2
	} 

	if(mm2<10) {
	    mm2='0'+mm2
	}

	var dates = {
		todayDate : yyyy+'-'+mm+'-'+dd,
		lastWeek : yyyy2+'-'+mm2+'-'+dd2
	};

	return dates;
}