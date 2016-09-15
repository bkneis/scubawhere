
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1 && (!window.tourStart))
{
	window.location.href = '#dashboard';
}

var report_type;
var report;
var filter;
window.transactions;
window.agentBookings;
window.bookings;
window.utilisations;
window.sources;

var colors = [
	"#85144b", // maroon
	"#FF4136", // red
	"#FF851B", // orange
	"#3D9970", // olive
	"#FFDC00", // yellow
	"#2ECC40", // green
	"#01FF70", // lime
	"#39CCCC", // teal
	"#7FDBFF", // aqua
	"#001f3f", // navy
	"#0074D9", // blue
	"#B10DC9", // purple
	"#F012BE", // fuchsia
];

var colorID = 0;
var democolorID = 0;

Handlebars.registerHelper('getUtil', function(capacity, assigned){
	if(capacity === assigned) return 0;
	return Math.round((assigned/capacity) * 100);
});

Handlebars.registerHelper('getDate', function(date){
	return date.split(' ')[0];
});

Handlebars.registerHelper('getTransAmount', function(date){
	//
});

Handlebars.registerHelper('getRemaining', function(capacity, assigned){
	return capacity - assigned;
});

Handlebars.registerHelper('getStatID', function(country){
	return country.replace(/\s/g, '');
});

Handlebars.registerHelper('getCountry', function(id){
	return window.countries[id].name;
});

Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});

Handlebars.registerHelper('getCommissionAmount', function() {
	var price = this.real_decimal_price || this.decimal_price;

	return (decRound(parseFloat(price) * parseFloat(this.agent.commission) / 100, 2)).toFixed(2);
});

Handlebars.registerHelper('getNetAmount', function() {
	var price = this.real_decimal_price || this.decimal_price;

	return (parseFloat(this.decimal_price) - decRound(parseFloat(price) * parseFloat(this.agent.commission) / 100, 2)).toFixed(2);
});

Handlebars.registerHelper('sourceName', function() {
	switch(this.source) {
		case 'telephone' : return 'Telephone';
		case 'email'     : return 'Email';
		case 'facetoface': return 'In Person';
		default: return new Handlebars.SafeString('Agent - ' + this.agent.name);
	}
})

$(function() {

	window.sources = [
		{name: "Telephone", source : "telephone"},
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

	$('#start-date, #end-date').on('change', function() {
		getReport(report_type, createDataTable);
	});

	$("#report-type-btns").on('click', ':button', function(event){
		event.preventDefault();
		report_type = $(this).attr("data-report");
		getReport(report_type, createDataTable);
		$(':button').removeClass("btn-primary");
		$(this).addClass("btn-primary");
		colorID = 0;
		democolorID = 0;
	});

	getReport(report_type, createDataTable);

});

function getFileName() {
	return report_type + ' report for ' + $('#start-date').val() + ' - ' + $('#end-date').val(); 	
}

function createDataTable() {

	// Check if table contains any data
	if($('.reports-table tbody tr').first().children('td').length === 1) return false;

	$('.reports-table').dataTable({
        "pageLength": 10,
		"dom": 'Bfrtlp',
		"buttons": [
			{
				extend : 'excel',
	   			title  : getFileName() 	
			},
			{
				extend : 'pdf',
				title  : getFileName()
			},
			{
				extend : 'print',
				title  : getFileName()
			}
		]
	});
}

function getDates() {
	return {
		todayDate:          moment().format('YYYY-MM-DD'),
		firstDayOfTheMonth: moment().startOf('month').format('YYYY-MM-DD'),
	}
}

function getReport(reportType, callback) {
	var dates = {
		after : $("#start-date").val(),
		before : $("#end-date").val()
	};
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
					var newData = _.sortBy(data.concat(data2), 'received_at');
					for(var i = 0; i < data2.length; i++) {
						data2[i].refund = true;
					}
					window.transactions = newData;

					var totalCash   = 0,
					    totalCredit = 0,
					    totalCheque = 0,
					    totalBank   = 0,
					    totalOnline = 0,
					    totalPaypal = 0;
					for(var i = 0; i < newData.length; i++) {
						switch(parseInt(newData[i].paymentgateway_id)) {
							case(1) :
								if(newData[i].refund) totalCash -= parseInt(newData[i].amount);
								else                  totalCash += parseInt(newData[i].amount);
								break;
							case(2) :
								if(newData[i].refund) totalCredit -= parseInt(newData[i].amount);
								else                  totalCredit += parseInt(newData[i].amount);
								break;
							case(3) :
								if(newData[i].refund) totalCheque -= parseInt(newData[i].amount);
								else                  totalCheque += parseInt(newData[i].amount);
								break;
							case(4) :
								if(newData[i].refund) totalBank -= parseInt(newData[i].amount);
								else                  totalBank += parseInt(newData[i].amount);
								break;
							case(5) :
								if(newData[i].refund) totalOnline -= parseInt(newData[i].amount);
								else                  totalOnline += parseInt(newData[i].amount);
								break;
							case(6) :
								if(newData[i].refund) totalPaypal -= parseInt(newData[i].amount);
								else                  totalPaypal += parseInt(newData[i].amount);
								break;
						}
					}

					// Only respect positive totals
					var total = Math.max(totalCash, 0)
					          + Math.max(totalCredit, 0)
					          + Math.max(totalCheque, 0)
					          + Math.max(totalBank, 0)
					          + Math.max(totalOnline, 0)
					          + Math.max(totalPaypal, 0);
					console.log(newData);
					window.totalTransactionsRevenue = total;
					var aggregateData = {
						transactions: newData,
						totalRevenue: total
					};
					$("#reports").html( report({entries : aggregateData}) );
					// If the no positive totals are in this daterange, set total to 1 (division by zero is not possible) as it doesn't matter anyway as all totals are < 0
					if(total === 0) total = 1;

					$("#transactions-totalCash"  ).text(window.company.currency.symbol + " " + totalCash);
					$("#transactions-totalCredit").text(window.company.currency.symbol + " " + totalCredit);
					$("#transactions-totalCheque").text(window.company.currency.symbol + " " + totalCheque);
					$("#transactions-totalBank"  ).text(window.company.currency.symbol + " " + totalBank);
					$("#transactions-totalOnline").text(window.company.currency.symbol + " " + totalOnline);
					$("#transactions-totalPaypal").text(window.company.currency.symbol + " " + totalPaypal);

					$("#transactions-cash-percentage"  ).css("width", ((Math.max(totalCash  , 0)/total) * 100) + "%");
					$("#transactions-credit-percentage").css("width", ((Math.max(totalCredit, 0)/total) * 100) + "%");
					$("#transactions-cheque-percentage").css("width", ((Math.max(totalCheque, 0)/total) * 100) + "%");
					$("#transactions-bank-percentage"  ).css("width", ((Math.max(totalBank  , 0)/total) * 100) + "%");
					$("#transactions-online-percentage").css("width", ((Math.max(totalOnline, 0)/total) * 100) + "%");
					$("#transactions-paypal-percentage").css("width", ((Math.max(totalPaypal, 0)/total) * 100) + "%");
					$("#transactions-date-range").append(" from " + $("#start-date").val() + " until " + $("#end-date").val());
					if(newData.length != 0 && typeof callback === 'function') callback();
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
				// console.log(data);
				window.agentBookings = data;
				report = Handlebars.compile($("#agents-report-template").html());
				$("#reports").empty().append( report({entries : data}) );
		 		if(data.bookings.length != 0 && typeof callback === 'function') callback();
			});
			break;

		case("booking-history") :
			$("#report-title").empty().append("Booking History Report");
			filter = Handlebars.compile($("#booking-history-filter-template").html());

			$("#report-filters").empty().append( filter({sources : window.sources}) );

			Report.getBookingHistory(dates, function success(data) {
				// console.log(data);
				window.bookings = data;
				report = Handlebars.compile($("#booking-history-report-template").html());
				$("#reports").empty().append( report({entries : data}) );
				if(data.bookings.length != 0 && typeof callback === 'function') callback();
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
				$("#utilisation-date-range").append(" from " + $("#start-date").val() + " until " + $("#end-date").val());
				if(data.utilisation != null && typeof callback === 'function') callback();
			});

			break;

		case("class-utilisation") :
			$("#report-title").empty().append("Class Utilisation Report");
			filter = Handlebars.compile($("#class-utilisation-filter-template").html());

			Class.getAll(function sucess(data) {
				$("#report-filters").empty().append( filter({classes : data}) );
			});

			Report.getClassUtilisation(dates, function sucess(data) {
				console.log(data);
				window.classUtlisations = data;
				report = Handlebars.compile($("#class-utilisation-report-template").html());
				$("#reports").empty().append( report({entries : data}) );
				//$("#class-utilisation-total-capacity").text(data.utilisation_total.unassigned);
				//$("#class-utilisation-average").css("width", (100 - ((data.utilisation_total.unassigned/data.utilisation_total.capacity)*100)) + "%");
				$("#class-utilisation-date-range").append(" from " + $("#start-date").val() + " until " + $("#end-date").val());
				if(data.utilisation != null && typeof callback === 'function') callback();
			});

			break;

		case("revenue") :
			$("#report-title").empty().append("Revenue Analysis Report");
			filter = Handlebars.compile($("#revenue-filter-template").html());

			var types = ["Packages", "Addons", "Fees", "Courses", "Tickets", "Accommodations", "Summary"];
			$("#report-filters").empty().append( filter({types : types}) );

			Report.getTicketsPackages(dates, function sucess(data) {

				var stats = {};
				stats.streams = [];
				colorID = 0;
				_.each(data.tickets, function(ticket) {
					ticket.statColor = assignColor("revenue");
					ticket.type = "Tickets";
					stats.streams.push(ticket);
				});
				_.each(data.packages, function(package) {
					package.statColor = assignColor("revenue");
					package.type = "Packages";
					stats.streams.push(package);
				});
				_.each(data.courses, function(course) {
					course.statColor = assignColor("revenue");
					course.type = "Courses";
					stats.streams.push(course);
				});
				_.each(data.addons, function(addon) {
					addon.statColor = assignColor("revenue");
					addon.type = "Addons";
					stats.streams.push(addon);
				});
				_.each(data.fees, function(fee) {
					fee.statColor = assignColor("revenue");
					fee.type = "Fees";
					stats.streams.push(fee);
				});
				_.each(data.accommodations, function(acom) {
					acom.statColor = assignColor("revenue");
					acom.type = "Accommodations";
					stats.streams.push(acom);
				});
				stats.acomTotal    = data.accommodations_total.revenue;
				stats.ticketTotal  = data.tickets_total.revenue;
				stats.packageTotal = data.packages_total.revenue;
				stats.courseTotal  = data.courses_total.revenue;
				stats.addonTotal   = data.addons_total.revenue;
				stats.feeTotal     = data.fees_total.revenue;
				stats.total        = data.accommodations_total.revenue + data.tickets_total.revenue + data.packages_total.revenue +
				data.courses_total.revenue + data.addons_total.revenue + data.fees_total.revenue;
				stats.acomAmount = data.accommodations_total.quantity;
				stats.ticketAmount = data.tickets_total.quantity;
				stats.packageAmount = data.packages_total.quantity;
				stats.courseAmount = data.courses_total.quantity;
				stats.addonAmount = data.addons_total.quantity;
				stats.feeAmount = data.fees_total.quantity;
				// console.log(stats);
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

				renderDoughnutChart(pieStats, "revenue");
				if(stats.total != 0 && typeof callback === 'function') callback();
			});
			break;

		case("demographics") :
			$("#report-title").empty().append("Demographics Report");
			/*filter = Handlebars.compile($("#demographics-filter-template").html());

			Trip.getAllTrips(function sucess(data) {
				$("#report-filters").empty().append( filter({trips : data}) );
			}); */

			Report.getDemographics(dates, function sucess(data) {
				console.log(data);
				report = Handlebars.compile($("#demographics-report-template").html());
				$("#reports").empty().append( report({countries : data.country_revenue}) );

				var pieStats = [];
				_.each(data.country_revenue, function(value, key) {
					var stat = {
						value : value,
						color : assignColor("demographics"),
						//highlight : "#FF0",
						label : key,
                		labelColor : 'white',
                		labelFontSize : '16'

					};
					pieStats.push(stat);
					var statID = key.replace(/\s/g, '');
					$("#" + statID + "-colour").css('background-color', stat.color);
					console.log(stat.color);
				});
				renderDoughnutChart(pieStats, "demographics");
				if(data.country_revenue.length != 0 && typeof callback === 'function') callback();
			});

			break;


	}

}

function renderDoughnutChart(data, type) {
	var ctx;
	if(type == "revenue") ctx = $("#revenue-chart").get(0).getContext("2d");
	else ctx = $("#demographics-chart").get(0).getContext("2d");

	new Chart(ctx).Doughnut(data, {
		animateRotate: true,
		animateScale: false,
		animationEasing: 'easeOutQuart', // http://jqueryui.com/resources/demos/effect/easing.html
		tooltipTemplate: "<%= label %>: " + window.company.currency.symbol + " <%= value %>",
		showTooltips: true
	});
}

function filterReport(reportType, value)
{
	var params = {
		after : $("#start-date").val(),
		before : $("#end-date").val()
	};
	switch(reportType)
	{
		case('transactions') :
			if(value == 0) getReport("transactions", createDataTable);
			else
			{
				var results = [];
				var totalRevenue = 0;
				_.each(window.transactions, function(transaction) {
					if(parseInt(transaction.paymentgateway_id) == value) 
					{
						results.push(transaction);
						totalRevenue += parseFloat(transaction.amount);
					}
				});

				var data = {
					transactions : results,
					total        : window.totalTransactionsRevenue,
					totalRevenue : totalRevenue.toFixed(2)
				};

				console.log(data);
				report = Handlebars.compile($("#transactions-report-template").html());
				$("#reports").empty().append( report({entries : data}) );
				$("#transactions-summary").css('display', 'none');
				createDataTable();
			}

			break;

		case('agents') :
			if(value == 0) getReport("agents", createDataTable);
			else
			{
				/*
				var results = [];
				_.each(window.agentBookings.bookings, function(booking) {
					if(parseInt(booking.agent_id) == value) results.push(booking);
				});

				// console.log(results);
				var results2 = {bookings : results};
				report = Handlebars.compile($("#agents-report-template").html());
				$("#reports").empty().append( report({entries : results2}) );
				*/

				params.agent_ids = [value];

				Report.getAgentBookings(params, function success(data) {
					// console.log(data);
					report = Handlebars.compile($("#agents-report-template").html());
					$("#reports").empty().append( report({entries : data}) );
					createDataTable();
				});
			}

			break;

		case('booking-history') :
			if(value == 0) getReport("booking-history", createDataTable);
			else
			{
				var results = [];
				var totalRevenue = 0;
				var price;
				_.each(window.bookings.bookings, function(booking) {
					if(booking.source == value) 
					{
						results.push(booking);
						price = booking.decimal_price || booking.real_decimal_price;
						totalRevenue += parseFloat(price);
					}
					else if(booking.source == null & value == "agent") 
					{
						results.push(booking);
						price = booking.real_decimal_price || booking.decimal_price;
						price = (parseFloat(booking.decimal_price) - decRound(parseFloat(price) * parseFloat(booking.agent.commission) / 100, 2)).toFixed(2);
						totalRevenue += parseFloat(price);
					}
				});

				var results2 = {bookings : results};
				results2.totals = { revenue : totalRevenue.toFixed(2) };
				report = Handlebars.compile($("#booking-history-report-template").html());
				$("#reports").empty().append( report({entries : results2}) );
				createDataTable();
			}

			break;

		case('utilisation') :
			if(value == 0) getReport("utilisation", createDataTable);
			else
			{
				var results = [];
				var totalAssigned = 0;
				var totoalCapacity = 0;
				_.each(window.utlisations.utilisation, function(trip) {
					if(trip.name == value)
					{
						results.push(trip);
						totalAssigned += parseInt(trip.assigned);
						totoalCapacity += parseInt(trip.capacity);
					}
				});

				var results2 = {utilisation : results};
				results2.utilisation_total = {
					capacity : totoalCapacity,
					assigned : totalAssigned
				};
				report = Handlebars.compile($("#utilisation-report-template").html());
				$("#reports").empty().append( report({entries : results2}) );
				$("#utilisation-date-range").append(" from " + $("#start-date").val() + " until " + $("#end-date").val());
				//$("#utilisation-summary").css('display', 'none');
				createDataTable();
			}

			break;

		case('class-utilisation') :
			if(value == 0) getReport("class-utilisation", createDataTable);
			else
			{
				var results = [];
				var totalAssigned = 0;
				var totoalCapacity = 0;
				_.each(window.classUtlisations.utilisation, function(training) {
					if(training.name == value)
					{
						results.push(training);
						totalAssigned += parseInt(training.assigned);
						totoalCapacity += parseInt(training.capacity);
					}
				});

				var results2 = {utilisation : results};
				results2.utilisation_total = {
					capacity : totoalCapacity,
					assigned : totalAssigned
				};
				report = Handlebars.compile($("#class-utilisation-report-template").html());
				$("#reports").empty().append( report({entries : results2}) );
				$("#class-utilisation-date-range").append(" from " + $("#start-date").val() + " until " + $("#end-date").val());
				//$("#class-utilisation-summary").css('display', 'none');
				createDataTable();
			}

			break;

		case('revenue') :
			if(value == 0) getReport("revenue", createDataTable);
			else
			{
				if(value == "Summary")
				{
					var summary = [
					{
						name : "Tickets",
						quantity : window.revenueAnalysis.ticketAmount,
						statColor : "#0074D9",
						revenue : window.revenueAnalysis.ticketTotal
					},
					{
						name : "Packages",
						quantity : window.revenueAnalysis.packageAmount,
						statColor : "#39CCCC",
						revenue : window.revenueAnalysis.packageTotal
					},
					{
						name : "Courses",
						quantity : window.revenueAnalysis.courseAmount,
						statColor : "#3D9970",
						revenue : window.revenueAnalysis.courseTotal
					},
					{
						name : "Addons",
						quantity : window.revenueAnalysis.addonAmount,
						statColor : "#FFDC00",
						revenue : window.revenueAnalysis.addonTotal
					},
					{
						name : "Fees",
						quantity : window.revenueAnalysis.feeAmount,
						statColor : "#FF851B",
						revenue : window.revenueAnalysis.feeTotal
					},
					{
						name : "Accommodations",
						quantity : window.revenueAnalysis.acomAmount,
						statColor : "#2ECC40",
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
					// console.log(pieStats);
					report = Handlebars.compile($("#revenue-report-template").html());
					var results2 = {streams : summary};
					$("#reports").empty().append( report({entries : results2}) );

					renderDoughnutChart(pieStats, "revenue");
					createDataTable();
				}
				else
				{
					var results = [];
					_.each(window.revenueAnalysis.streams, function(revenue) {
						//console.log(revenue);
						if(revenue.type == value) results.push(revenue);
					});

					// console.log(results);
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

					renderDoughnutChart(pieStats, "revenue");
					createDataTable();
				}
			}
			break;
	}
}

function assignColor(type) {
	// Colors from http://clrs.cc

	var id;
	if(type == "demographics") {
		if(democolorID == colors.length) democolorID = 0;
		id = democolorID;
		democolorID++;
	}
	else {
		if(colorID == colors.length) colorID = 0;
		id = colorID;
		colorID++;
	}

	return colors[id];

}
