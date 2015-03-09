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

	$("#date-select").val("2015-03-09");

	loadPickups();

	$('#date-select').on('change', function() {
		loadPickups();
	});

});

function loadPickups() {
	var params = { date : $("#date-select").val() };
	$.ajax({
		url: 'api/company/pick-up-schedule',
		data: params,
		success: function(data) {
			$("#pickup-table").empty().append( pickupList({ pickups : data }) );
			console.log(data);
		}
	});
}