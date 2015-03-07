var report_type;
var report;
var filter;

Handlebars.registerHelper('getUtil', function(capacity, unassigned){
	return ((capacity/unassigned) * 100);
});

Handlebars.registerHelper('getDate', function(date){
	return (date.substring(0, date.length - 9));
});

$(function() {

	report_type = "transactions";

	$('input.datepicker').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
		autoclose : true
	});

	$('#start-date').val('2015-01-01');;
	$('#end-date').val('2015-03-01');

	$('.reports-table').DataTable({
		"paging":   false,
		"ordering": false,
		"info":     false,
		"pageLength" : 10,
		"searching" : false
	});

	$('#start-date, #end-date').on('change', function() {
		var dates = {
			after : $("#start-date").val(),
			before : $("#end-date").val()
		}
		switch(report_type) {
			case("transactions") :
				$.ajax({
					url: '/api/payment/filter',
					data: dates,
					success: function(data) {
						console.log(data);
						report = Handlebars.compile($("#transactions-report-template").html());
						$("#reports").empty().append( report({entries : data}) );
					}
				});
				break;
			case("agents") :
				/*filter = Handlebars.compile($("#agents-filter-template").html());
				Agent.getAllAgents(function sucess(data) {
					$("#report-filters").empty().append( filter({agents : data}) );
				});*/
				$.ajax({
					url: '/api/booking/filter-confirmed-by-agent',
					data: dates,
					success: function(data) {
						console.log(data);
						report = Handlebars.compile($("#agents-report-template").html());
						$("#reports").empty().append( report({entries : data}) );
					}
				});
				break;
			case("booking-history") :
				$.ajax({
					url: '/api/booking/filter-confirmed',
					data: dates,
					success: function(data) {
						console.log(data);
						report = Handlebars.compile($("#agents-report-template").html());
						$("#reports").empty().append( report({entries : data}) );
					}
				});
				break;
			case("utilisation") :
				$.ajax({
					url: '/api/report/utilisation',
					data: dates,
					success: function(data) {
						console.log(data);
						report = Handlebars.compile($("#utilisation-report-template").html());
						$("#reports").empty().append( report({entries : data}) );
					}
				});
				break;

		}
	});

	$("#report-type-btns").on('click', ':button', function(event){
		event.preventDefault();
		console.log($(this).attr("data-report"));
		report_type = $(this).attr("data-report");
		var dates = {
			after : $("#start-date").val(),
			before : $("#end-date").val()
		}
		$.ajax({
			url: $(this).attr("data-api"),
			data: dates,
			success: function(data) {
				console.log(data);
				report = Handlebars.compile($("#" + report_type + "-report-template").html());
				$("#reports").empty().append( report({entries : data}) );
			}
		});
		$(':button').removeClass("btn-primary");
		$(this).addClass("btn-primary");
	});

	var dates = {
		after: '2015-01-01',
		before: '2015-05-01', // This date is EXCLUSIVE, so it needs to be one day AFTER the final date that should be included
	};

	$.ajax({
		url: '/api/payment/filter',
		data: dates,
		success: function(data) {
			console.log(data);
			report = Handlebars.compile($("#transactions-report-template").html());
			$("#reports").empty().append( report({transactions : data}) );
		}
	});

});

function getDates() {
	var dates = {};
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();

	if(dd<10) dd='0'+dd;
	if(mm<10) mm='0'+mm;
	//dates.start = (today.getDate() - 7);
	today = yyyy+'-'+mm+'-'+dd;
	dates.end = yyyy+'-'+mm+'-'+dd;
	dates.start = (today.getDate() - 7);
	console.log(dates);
	//return dates;
}
