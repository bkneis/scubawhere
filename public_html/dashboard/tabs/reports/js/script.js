var report_type;
var report;
var filter;
var summaries;
window.transactions;
window.agentBookings;
window.bookings;
window.utilisations;
window.sources;

var colorID = 0;

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

	window.sources = [
		{name : "Telephone", source : "telephone"},
		{name: "Agent", source : "agent"},
		{name: "In person", source : "facetoface"},
		{name: "Email", source : "email"}
	];

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

	$('#start-date').val(getDates().firstDayOfTheMonth);
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
	return {
		todayDate:          moment().format('YYYY-MM-DD'),
		firstDayOfTheMonth: moment().startOf('month').format('YYYY-MM-DD'),
	}
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

			Report.getPaymentGateways(function success(data) {
				$("#report-filters").empty().append( filter({gateways : data}) );
			});


			Report.getPayments(dates, function success(data) {
				Report.getRefunds(dates, function success(data2) {
					var newData = data.concat(data2);
					for(var i = 0; i < data2.length; i++) {
						data2[i].refund = true;
					}
					console.log(newData);
					window.transactions = newData;
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
				window.agentBookings = data;
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

			$("#report-filters").empty().append( filter({sources : window.sources}) );

			Report.getBookingHistory(dates, function success(data) {
				console.log(data);
				window.bookings = data;
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
				window.utlisations = data;
				report = Handlebars.compile($("#utilisation-report-template").html());
				$("#reports").empty().append( report({entries : data}) );
				$("#utilisation-total-capacity").text(data.utilisation_total.unassigned);
				$("#utilisation-average").css("width", (100 - ((data.utilisation_total.unassigned/data.utilisation_total.capacity)*100)) + "%");
				$("#utilisation-date-range").append(" from " + $("#start-date").val() + " until " + $("#end-date").val());
			});

			break;

		case("revenue") :
			$("#report-title").empty().append("Revenue Analysis Report");
			filter = Handlebars.compile($("#revenue-filter-template").html());

			var types = ["Packages", "Addons", "Courses", "Tickets", "Accommodations", "Summary"];
			$("#report-filters").empty().append( filter({types : types}) );

			Report.getTicketsPackages(dates, function sucess(data) {
				console.log(data);

				var stats = {};
				stats.streams = [];
				colorID = 0;
				_.each(data.tickets, function(ticket) {
					ticket.statColor = assignColor();
					colorID++;
					ticket.type = "Tickets";
					stats.streams.push(ticket);
				});
				_.each(data.packages, function(package) {
					package.statColor = assignColor();
					colorID++;
					package.type = "Packages";
					stats.streams.push(package);
				});
				_.each(data.courses, function(course) {
					course.statColor = assignColor();
					colorID++;
					course.type = "Courses";
					stats.streams.push(course);
				});
				_.each(data.addons, function(addon) {
					addon.statColor = assignColor();
					colorID++;
					addon.type = "Addons";
					stats.streams.push(addon);
				});
				_.each(data.accommodations, function(acom) {
					acom.statColor = assignColor();
					colorID++;
					acom.type = "Accommodations";
					stats.streams.push(acom);
				});
				stats.acomTotal = data.accommodations_total.revenue;
				stats.ticketTotal = data.tickets_total.revenue;
				stats.packageTotal = data.packages_total.revenue;
				stats.courseTotal = data.courses_total.revenue;
				stats.addonTotal = data.addons_total.revenue;
				stats.total = data.accommodations_total.revenue + data.tickets_total.revenue + data.packages_total.revenue +
				data.courses_total.revenue + data.addons_total.revenue;
				console.log(stats);
				window.revenueAnalysis = stats;
				report = Handlebars.compile($("#revenue-report-template").html());
				$("#reports").empty().append( report({entries : stats}) );

				var pieStats = [];

				_.each(stats.streams, function(stream) {
					var stat = {
						value : stream.revenue,
						color : stream.statColor,
						//highlight : "#FF0",
						label : stream.name,
                		labelColor : 'white',
                		labelFontSize : '16'

					};
					pieStats.push(stat);
				});

				var ctx = $("#myChart").get(0).getContext("2d");
				var myPieChart = new Chart(ctx).Pie(pieStats, {
					animateScale: true,
					multiTooltipTemplate: "<%= datasetLabel %> - <%= value %>",
					showTooltips: true
				});
			});
			break;
	}
}

function filterReport(reportType, value)
{
	switch(reportType)
	{
		case('transactions') :
			if(value == 0) getReport("transactions");
			else
			{
				var results = [];
				_.each(window.transactions, function(transaction) {
					if(parseInt(transaction.paymentgateway_id) == value) results.push(transaction);
				});

				console.log(results);
				report = Handlebars.compile($("#transactions-report-template").html());
				$("#reports").empty().append( report({entries : results}) );
				$("#transactions-summary").css('display', 'none');
			}

			break;

		case('agents') :
			if(value == 0) getReport("agents");
			else
			{
				var results = [];
				_.each(window.agentBookings.bookings, function(booking) {
					if(parseInt(booking.agent_id) == value) results.push(booking);
				});

				console.log(results);
				var results2 = {bookings : results};
				report = Handlebars.compile($("#agents-report-template").html());
				$("#reports").empty().append( report({entries : results2}) );
			}

			break;

		case('booking-history') :
			if(value == 0) getReport("booking-history");
			else
			{
				var results = [];
				_.each(window.bookings.bookings, function(booking) {
					console.log(booking.source);
					if(booking.source == value) results.push(booking);
					else if(booking.source == null & value == "agent") results.push(booking);
				});

				console.log(results);
				var results2 = {bookings : results};
				report = Handlebars.compile($("#booking-history-report-template").html());
				$("#reports").empty().append( report({entries : results2}) );
			}

			break;

		case('utilisation') :
			if(value == 0) getReport("utilisation");
			else
			{
				var results = [];
				_.each(window.utlisations.utilisation, function(trip) {
					console.log(trip);
					if(trip.name == value) results.push(trip);
				});

				console.log(results);
				var results2 = {utilisation : results};
				report = Handlebars.compile($("#utilisation-report-template").html());
				$("#reports").empty().append( report({entries : results2}) );
				$("#utilisation-summary").css('display', 'none');
			}

			break;

		case('revenue') :
			if(value == 0) getReport("revenue");
			else
			{
				if(value == "Summary")
				{
					var summary = [
					{
						name : "Tickets",
						//quantity : ,
						statColor : "#800000",
						revenue : window.revenueAnalysis.ticketTotal
					},
					{
						name : "Packages",
						//quantity : ,
						statColor : "#FF0000",
						revenue : window.revenueAnalysis.packageTotal
					},
					{
						name : "Courses",
						//quantity : ,
						statColor : "#808000",
						revenue : window.revenueAnalysis.courseTotal
					},
					{
						name : "Addons",
						//quantity : ,
						statColor : "#FFFF00",
						revenue : window.revenueAnalysis.addonTotal
					},
					{
						name : "Accommodations",
						//quantity : ,
						statColor : "#008000",
						revenue : window.revenueAnalysis.acomTotal
					}
					];

					var pieStats = [];

					_.each(summary, function(stream) {
						var stat = {
							value : stream.revenue,
							color : stream.statColor,
							//highlight : "#FF0",
							label : stream.name
						};
						pieStats.push(stat);
					});
					console.log(pieStats);
					report = Handlebars.compile($("#revenue-report-template").html());
					var results2 = {streams : summary};
					$("#reports").empty().append( report({entries : results2}) );
					var ctx = $("#myChart").get(0).getContext("2d");
					var myPieChart = new Chart(ctx).Pie(pieStats, {
						animateScale: true
					});
				}
				else
				{
					var results = [];
					_.each(window.revenueAnalysis.streams, function(revenue) {
						//console.log(revenue);
						if(revenue.type == value) results.push(revenue);
					});

					console.log(results);
					var results2 = {streams : results};
					report = Handlebars.compile($("#revenue-report-template").html());
					$("#reports").empty().append( report({entries : results2}) );

					var pieStats = [];

					_.each(results, function(stream) {
						var stat = {
							value : stream.revenue,
							color : stream.statColor,
							//highlight : "#FF0",
							label : stream.name
						};
						pieStats.push(stat);
					});

					var ctx = $("#myChart").get(0).getContext("2d");
					var myPieChart = new Chart(ctx).Pie(pieStats, {
						animateScale: true
					});
				}
			}
			break;
	}
}

function assignColor() {
	var colors = [
		"#800000", // maroon
		"#FF0000", // red
		"#808000", // olive
		"#FFFF00", // yellow
		"#008000", // green
		"#00FF00", // lime
		"#008080", // teal
		"#00FFFF", // aqua
		"#000080", // navy
		"#0000FF", // blue
		"#800080", // purple
		"#FF00FF" //fuschia
	];
	if(colorID == colors.length) colorID = 0;
	return colors[colorID];
}
