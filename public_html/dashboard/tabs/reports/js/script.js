$(function() {
	/*var params = {
		after: '2015-01-01',
		before: '2015-05-01', // This date is EXCLUSIVE, so it needs to be one day AFTER the final date that should be included
	};*/

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
			"emptyTable": "There are no transactions between these dates"
		}
	});

	var dates = {
		after : $("#start-date").val(),
		before : $("#end-date").val()
	}

	$.ajax({
		url: '/api/payment/filter',
		data: dates,
		success: function(data) {
			console.log(data);
		}
	});

});
