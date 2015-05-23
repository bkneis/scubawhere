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

	$("#feedback-div").on('submit', '#feedback-form', function(event){
		event.preventDefault();
		setToken('[name=_token]');
		Company.sendFeedback($('#feedback-form').serialize(), function success(data){
			pageMssg('Thank you, your feedback has been submitted', true);
			$('#feedback-form').trigger('reset');
		},
		function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.errors[0], 'danger');
		});
	});

	displayFBStats();

	if(window.company.initialised != 1) {
		var initWarning = '<div class="alert alert-info" role="alert"><i class="fa fa-heart fa-lg fa-fw"></i> <strong>Thank you for trying out scubawhereRMS!</strong> Please use the setup wizard below to configure your system.</div>';
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
			}

		});

} else {

	var todaysSessionsWidget = $("#todays-sessions-widget").html();

	$("#row1").prepend(todaysSessionsWidget);

	todaySession = Handlebars.compile($('#today-session-template').html());
	Session.getToday(function success(data){
		window.todaySessions = _.indexBy(data, 'id');
		// console.log(data);
		$('#sessions-list').append( todaySession( {sessions : data} ) );
		//getAllLocations(data);
		for(var i =0; i < data.length; i++) {
			getCustomers(data[i].id);
		}
	},
	function error(xhr){
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.errors[0], 'danger');
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
			},
		"dom": 'T<"clear">lfrtip',
        "tableTools": {
            "sSwfPath": "/common/vendor/datatables-tabletools/swf/copy_csv_xls_pdf.swf"
        }
		});
	});

}

function displayFBStats() {

	if(window.facebook.status == "connected") {
		socialMedia = Handlebars.compile($('#social-media-template').html());
		FB.api(
        "/292265320876159/insights",
        {
          'period' : 'week'
        },
        function (response) {
          if (response && !response.error) {
            console.log(response);
            window.facebook.stats = [
              {title : response.data[1].title, data : response.data[1].values[2].value},
              {title : response.data[54].title, data : response.data[54].values[2].value},
              {title : response.data[29].title, data : response.data[29].values[2].value}
            ];
            $('#social-media-stats').empty().append(socialMedia({facebook : window.facebook.stats}));
          }
        }
    );
	} else {
		$('#social-media-stats').html('<p><strong>Please log into your facebook via settings to view you social media statistics</strong></p>');
	}
	
}

