var pickupList;
$(function() {

	$('input.datepicker').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
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

	pickupList = Handlebars.compile($("#pick-up-schedule-template").html());

	$("#date-select").val(getToday());

	var params = { date : $("#date-select").val() };
	Report.getPickupSchedule(params, function success(data) {
		$("#pickup-table").append( pickupList({ pickups : data }) );
	});

	$('#date-select').on('change', function() {
		loadPickups();
	});

});

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

	var date = yyyy+'-'+mm+'-'+dd;

	return date;
}

function loadPickups() {
	var params = { date : $("#date-select").val() };
	Report.getPickupSchedule(params, function success(data) {
		$("#pickup-table").empty().append( pickupList({ pickups : data }) );
	});
}