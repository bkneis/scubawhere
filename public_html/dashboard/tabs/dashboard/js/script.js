window.todaySessions;
window.todayBookings;
window.tourStart;
var todaySession;
var customerDetails;
window.currentStep;

Handlebars.registerHelper('getTime', function(obj){
	return obj.substring(obj.length-3, 10);
});

Handlebars.registerHelper('tourStarted', function(){
	if(window.tourStart) return true;
	else return false;
});

Handlebars.registerHelper('getEnd', function(obj, duration){
	console.log(obj);
	var date = moment(obj);
	date.add(duration, "hours");
	console.log(date);

	return date.format("HH:mm");
});

Handlebars.registerHelper('getPer', function(capacity){
	var booked = capacity[0];
	var max = capacity[1];
	return parseInt((booked / max) * 100) + '%';
});

$(function () {

	if(window.company.initialised != 1) {
		var initWarning = '<div class="alert alert-danger" role="alert"><strong>RMS is not configured!</strong> Please use the setup wizard below to configure your system</div>';
		$("#wrapper").prepend(initWarning);
		var setupWizard = $("#setup-wizard").html();
		$("#row1").prepend(setupWizard);
		if(window.tourStart) {
			$("#start-wizard").text("Continue wizard");
		}
		$("#start-wizard").on('click', function(event) {
			if(window.tourStart) {
				window.location.href = window.currentStep.tab;
			}
			else {
				window.currentStep = "#dashboard";
				window.location.href = '#accommodations';
					$("#guts").prepend($("#tour-nav-wizard").html());
					window.tourStart = true;
					window.currentStep = {
						tab : "#accommodations",
						position : 1
					};
					$(".tour-progress").on("click", function(event) {
						if(window.currentStep.position >= $(this).attr('data-position')) {
							window.location.href = $(this).attr('data-target');
						} else {
							pageMssg("Please complete the unfinished steps");
						}
					});
				//$(this).text("Continue tour");
				/*var tourDash = introJs();
				tourDash.setOptions({
					steps: [
					{ 
						intro: "Welcome to scubawhereRMS! So we can get you all set up with our system, this wizard will take you through our system and ask that you fill in some information about your dive operation."
					},
					{
						intro: "So let's get started. Click 'Done' to start the configuration."
					}
					],
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false,
					skipLabel : "End"
				});
				tourDash.start().oncomplete(function() {
					window.location.href = '#accommodations';
					$("#guts").prepend($("#tour-nav-wizard").html());
					window.tourStart = true;
					window.currentStep = {
						tab : "#accommodations",
						position : 1
					};
					$(".tour-progress").on("click", function(event) {
						if(window.currentStep.position >= $(this).attr('data-position')) {
							window.location.href = $(this).attr('data-target');
						} else {
							pageMssg("Please complete the unfinished steps");
						}
					});
				});*/
			}

		});

} else {

	var todaysSessionsWidget = $("#todays-sessions-widget").html();
	
	$("#row1").prepend(todaysSessionsWidget);
	
	todaySession = Handlebars.compile($('#today-session-template').html());
	Session.getToday(function success(data){
		window.todaySessions = _.indexBy(data, 'id');
		console.log(data);
		$('#sessions-list').append( todaySession( {sessions : data} ) );
		//getAllLocations(data);
		for(var i =0; i < data.length; i++) {
			getCustomers(data[i].id);
		}
	},
	function error(xhr){
		console.log('could not retrieve sessions');
	});

	$('#sessions-list').on('click', '.accordion-header', function() {
		$(this).toggleClass('expanded');
		$('.accordion-' + this.getAttribute('data-id')).toggle();
	});

}

});

function getCustomers(id) {

	customerDetails = Handlebars.compile($('#customer-details-template').html());
	var params = "id=" + id;
	Session.getAllCustomers(params, function sucess(data){
		console.log(data.customers);
		$('#customer-table-'+id).append( customerDetails( {customers : data.customers} ) );
		$('#customers-'+id).DataTable({
			"paging":   false,
			"ordering": false,
			"info":     false,
			"pageLength" : 10,
			"language": {
				"emptyTable": "There are no customers booked for this trip"
			}
		});
	});

}

