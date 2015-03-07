var report_type;
var report;
var filter;

Handlebars.registerHelper('getUtil', function(capacity, unassigned){
	return Math.round((100-((unassigned/capacity) * 100)));
});

Handlebars.registerHelper('getDate', function(date){
	return (date.substring(0, date.length - 9));
});

Handlebars.registerHelper('getTransAmount', function(date){
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
	$('#end-date').val('2015-05-01');

	$('.reports-table').DataTable({
		"paging":   false,
		"ordering": false,
		"info":     false,
		"pageLength" : 10,
		"searching" : false
	});

	$('#start-date, #end-date').on('change', function() {
		getReport(report_type);
	});

	$("#report-type-btns").on('click', ':button', function(event){
		event.preventDefault();
		report_type = $(this).attr("data-report");
		getReport(report_type);
		$(':button').removeClass("btn-primary");
		$(this).addClass("btn-primary");
	});

	getReport(report_type);

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

function getReport(reportType) {
	var dates = {
		after : $("#start-date").val(),
		before : $("#end-date").val()
	}
	$("#report-filters").empty();
	switch(reportType) {
		case("transactions") :
			filter = Handlebars.compile($("#transactions-filter-template").html());
			$.ajax({
				url: '/api/payment/paymentgateways',
				success: function(data) {
					$("#report-filters").empty().append( filter({gateways : data}) );
				}
			});
			$.ajax({
			url: '/api/payment/filter',
			data: dates,
			success: function(data) {
				console.log(data);
				report = Handlebars.compile($("#transactions-report-template").html());
				$("#reports").empty().append( report({entries : data}) );
				var totalCash = 0, totalCredit = 0, totalCheque = 0, totalBank = 0, totalPaypal = 0;
				for(var i=0; i < data.length; i++) {
					console.log(data[i].amount);
					console.log(data[i].paymentgateway_id);
					switch(data[i].paymentgateway_id) {
						case("1") :
							totalCash += parseInt(data[i].amount);
							break;
						case("2") :
							totalCredit += parseInt(data[i].amount);
							break;
						case("3") :
							totalCheque += parseInt(data[i].amount);
							break;
						case("4") :
							totalBank += parseInt(data[i].amount);
							break;
						case("5") :
							totalPaypal += parseInt(data[i].amount);
							break;
					}
				}
				$("#transactions-totalCash").text(data[0].currency.symbol + " " + totalCash);
				$("#transactions-cash-percentage").css("width", ((totalCash/(totalCash + totalCredit + totalCheque + totalBank + totalPaypal)*100)) + "%");
				$("#transactions-totalCredit").text(data[0].currency.symbol + " " + totalCredit);
				$("#transactions-credit-percentage").css("width", ((totalCredit/(totalCash + totalCredit + totalCheque + totalBank + totalPaypal)*100)) + "%");
				$("#transactions-totalCheque").text(data[0].currency.symbol + " " + totalCheque);
				$("#transactions-cheque-percentage").css("width", ((totalCheque/(totalCash + totalCredit + totalCheque + totalBank + totalPaypal)*100)) + "%");
				$("#transactions-totalBank").text(data[0].currency.symbol + " " + totalBank);
				$("#transactions-bank-percentage").css("width", ((totalBank/(totalCash + totalCredit + totalCheque + totalBank + totalPaypal)*100)) + "%");;
				$("#transactions-totalPaypal").text(data[0].currency.symbol + " " + totalPaypal);
				$("#transactions-paypal-percentage").css("width", ((totalPaypal/(totalCash + totalCredit + totalCheque + totalBank + totalPaypal)*100)) + "%");
				$("#transactions-date-range").append(" from " + $("#start-date").val() + " until " + $("#end-date").val());
			}
		});
			break;
		case("agents") :
			filter = Handlebars.compile($("#agents-filter-template").html());
			Agent.getAllAgents(function sucess(data) {
				$("#report-filters").empty().append( filter({agents : data}) );
			});
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
			filter = Handlebars.compile($("#booking-history-filter-template").html());
			var data = [{name : "Telephone"}, {name: "Agent"}, {name: "In person"}, {name: "Email"}];
			$("#report-filters").empty().append( filter({sources : data}) );
			$.ajax({
				url: '/api/booking/filter-confirmed',
				data: dates,
				success: function(data) {
					console.log(data);
					report = Handlebars.compile($("#booking-history-report-template").html());
					$("#reports").empty().append( report({entries : data}) );
				}
			});
			break;
		case("utilisation") :
			filter = Handlebars.compile($("#utilisation-filter-template").html());
			Trip.getAllTrips(function sucess(data) {
				$("#report-filters").empty().append( filter({trips : data}) );
			});
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
}
