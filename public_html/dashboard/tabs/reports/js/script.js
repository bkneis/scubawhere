var report_type;
var report;
var filter;
var summaries;

Handlebars.registerHelper('getUtil', function(capacity, unassigned){
	return Math.round((1 - unassigned/capacity) * 100);
});

Handlebars.registerHelper('getDate', function(date){
	return date.split(' ')[0];
});

Handlebars.registerHelper('getTransAmount', function(date){
	//
});

Handlebars.registerHelper('getRemaining', function(capacity, unassigned){
	return capacity - unassigned;
});

Handlebars.registerHelper('getCountry', function(id){
	return window.countries[id].name;
});

Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});

Handlebars.registerHelper('getCommissionAmount', function() {
	var price = this.real_decimal_price ? this.real_decimal_price : this.decimal_price;

	return (price * this.agent.commission / 100).toFixed(2);
});

$(function() {

	$.ajax({
		url : "/api/country/all",
		success : function(data) {
			window.countries = _.indexBy(data, "id");
		}
	});

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

	$('#start-date').val(getDates().lastWeek);
	$('#end-date').val(getDates().todayDate);

	var dataTable = $('.reports-table').DataTable({
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

function getReport(reportType) {
	var dates = {
		after : $("#start-date").val(),
		before : $("#end-date").val()
	}
	$("#report-filters").empty();
	switch(reportType) {
		case("transactions") :
			$("#report-title").empty().append("Transactions Report");
			filter = Handlebars.compile($("#transactions-filter-template").html());
			report = Handlebars.compile($("#transactions-report-template").html());
			//summaries = Handlebars.compile($("#transactions-totals-template").html());

			Report.getPayments(dates, function success(data) {
				Report.getRefunds(dates, function success(data2) {
					var newData = data.concat(data2);
					for(var i = 0; i < data2.length; i++) {
						data2[i].refund = true;
					}
					console.log(newData);
					$("#reports").empty().append( report({entries : newData}) );
					var totalCash = 0, totalCredit = 0, totalCheque = 0, totalBank = 0, totalPaypal = 0;
					for(var i=0; i < newData.length; i++) {
						switch(parseInt(newData[i].paymentgateway_id)) {
							case(1) :
								if(newData[i].refund) totalCash -= parseInt(newData[i].amount);
								else totalCash += parseInt(newData[i].amount);
								break;
							case(2) :
								if(newData[i].refund) totalCredit -= parseInt(newData[i].amount);
								else totalCredit += parseInt(newData[i].amount);
								break;
							case(3) :
								if(newData[i].refund) totalCheque -= parseInt(newData[i].amount);
								else totalCheque += parseInt(newData[i].amount);
								break;
							case(4) :
								if(newData[i].refund) totalBank -= parseInt(newData[i].amount);
								else totalBank += parseInt(newData[i].amount);
								break;
							case(5) :
								if(newData[i].refund) totalPaypal -= parseInt(newData[i].amount);
								else totalPaypal += parseInt(newData[i].amount);
								break;
						}
					}
					var total = totalCash + totalCredit + totalCheque + totalBank + totalPaypal;
					$("#transactions-totalCash").text(window.company.currency.symbol + " " + totalCash);
					$("#transactions-cash-percentage").css("width", ((totalCash/total)*100) + "%");
					$("#transactions-totalCredit").text(window.company.currency.symbol + " " + totalCredit);
					$("#transactions-credit-percentage").css("width", ((totalCredit/total)*100) + "%");
					$("#transactions-totalCheque").text(window.company.currency.symbol + " " + totalCheque);
					$("#transactions-cheque-percentage").css("width", ((totalCheque/total)*100) + "%");
					$("#transactions-totalBank").text(window.company.currency.symbol + " " + totalBank);
					$("#transactions-bank-percentage").css("width", ((totalBank/total)*100) + "%");
					$("#transactions-totalPaypal").text(window.company.currency.symbol + " " + totalPaypal);
					$("#transactions-paypal-percentage").css("width", ((totalPaypal/total)*100) + "%");
					$("#transactions-date-range").append(" from " + $("#start-date").val() + " until " + $("#end-date").val());
				});
			});
			break;

		case("agents") :
			$("#report-title").empty().append("Agent Transactions Report");
			filter = Handlebars.compile($("#agents-filter-template").html());

			Agent.getAllAgents(function sucess(data) {
				$("#report-filters").empty().append( filter({agents : data}) );
			});

			Report.getAgentBookings(dates, function success(data) {
				console.log(data);
				report = Handlebars.compile($("#agents-report-template").html());
				$("#reports").empty().append( report({entries : data}) );
				/*var agentsTotal = 0;
					for(var i=0; i < data.bookings.length; i++)
					{
						agentsTotal += parseInt(data.bookings[i].decimal_price);
					}
					$("#agents-total").text(agentsTotal);
					$("#agents-percentage-total").css("width", agentsTotal + "%");*/
			});
			break;

		case("booking-history") :
			$("#report-title").empty().append("Booking History Report");
			filter = Handlebars.compile($("#booking-history-filter-template").html());

			var data = [{name : "Telephone"}, {name: "Agent"}, {name: "In person"}, {name: "Email"}];
			$("#report-filters").empty().append( filter({sources : data}) );

			Report.getBookingHistory(dates, function success(data) {
				console.log(data);
				report = Handlebars.compile($("#booking-history-report-template").html());
				$("#reports").empty().append( report({entries : data}) );
			});
			break;

		case("utilisation") :
			$("#report-title").empty().append("Trip Utilisation Report");
			filter = Handlebars.compile($("#utilisation-filter-template").html());

			Trip.getAllTrips(function sucess(data) {
				$("#report-filters").empty().append( filter({trips : data}) );
			});

			Report.getTripUtilisation(dates, function sucess(data) {
				console.log(data);
				report = Handlebars.compile($("#utilisation-report-template").html());
				$("#reports").empty().append( report({entries : data}) );
				$("#utilisation-total-capacity").text(data.utilisation_total.unassigned);
				$("#utilisation-average").css("width", (100 - ((data.utilisation_total.unassigned/data.utilisation_total.capacity)*100)) + "%");
				$("#utilisation-date-range").append(" from " + $("#start-date").val() + " until " + $("#end-date").val());
			});

			break;
	}
}
