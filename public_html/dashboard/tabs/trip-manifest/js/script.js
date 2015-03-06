var tripsList;
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

});