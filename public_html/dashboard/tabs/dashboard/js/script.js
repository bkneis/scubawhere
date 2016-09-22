window.todayBookings;
window.tourStart;
var todaySession;
var customerDetails;
window.currentStep;

Handlebars.registerHelper('tourStarted', function(){
	if(window.tourStart) return true;
	else return false;
});

function friendlyDate(date) {
	// return moment(date).format('DD/MM/YYYY HH:mm');
	return moment(date).format('DD MMM YYYY HH:mm');
}

Handlebars.registerHelper("friendlyDate", function(date) {
	return friendlyDate(date);
});

Handlebars.registerHelper('getTicketName', function(id) {
	return window.tickets[id].name;
});

Handlebars.registerHelper('getCourseName', function(id) {
	return window.courses[id].name;
});

Handlebars.registerHelper('getRemainingBalance', function(pivot) {
	var price = pivot.decimal_price;

	var payments = 0;
	_.each(pivot.payments, function(obj) {
		payments += parseFloat(obj.amount);
	});

	payments = payments.toFixed(2);

	return new Handlebars.SafeString(window.company.currency.symbol + ' ' + payments + ' / ' + window.company.currency.symbol + ' ' + price);
});

Handlebars.registerHelper("tripFinish", function() {
	var startDate = friendlyDate(this.start);

	var duration = 0;
	if(this.trip) duration = this.trip.duration;
	if(this.training) duration = this.training.duration;

	var endDate   = friendlyDate( moment(this.start).add(duration, 'hours') );

	if(startDate.substr(0, 11) === endDate.substr(0, 11))
		// Only return the time, if the date is the same
		return endDate.substr(12);
	else
		// Only return the date and the Month (and time)
		return endDate.substr(0, 6) + ' ' + endDate.substr(12);
});

Handlebars.registerHelper('getPer', function(capacity){
	if(!capacity[1]) return '-';

	return capacity[0] + '/' + capacity[1] + ' - ' + parseInt((capacity[0] / capacity[1]) * 100) + '%';
});

$(function () {

	window.tickets = [];

	if(window.company.initialised !== 1) {

		$('#modal-intro').modal({
			backdrop: 'static',
			keyboard: false
		});
		
		$('#modal-intro').modal('show'); 		
		$('#btn-start-wizard').on('click', function(event) {
			event.preventDefault();
			startWizard();
		});

	} else {
		var nextSessionsWidget = $("#todays-sessions-widget").html();
		$("#row1").prepend(nextSessionsWidget);

		var nextSessionTemplates = Handlebars.compile($('#today-session-template').html());

		var gotCourses = $.Deferred();
		var gotTickets = $.Deferred();
		var gotSessions = $.Deferred();

		Course.getAllWithTrashed(function success(data) {
			window.courses = _.indexBy(data, 'id');
			gotCourses.resolve();
		});

		$.when(gotCourses).done(function() {
			Ticket.getAllWithTrashed(function success(data) {
				window.tickets = _.indexBy(data, 'id');	
				gotTickets.resolve();
			});
		});

		$.when(gotTickets).done(function() {
			Session.filter({after: moment().format('YYYY-MM-DD')}, function success(data) {
				gotSessions.resolve(data);
			},
			function error(xhr){
				var data = JSON.parse(xhr.responseText);
				pageMssg(data.errors[0], 'danger');
			});
		});

		$.when(gotSessions).done(function(nextTrips) {
			Class.filter({after: moment().format('YYYY-MM-DD')}, function success(nextClasses) {
				var nextSessions = nextTrips.concat(nextClasses);
				nextSessions = _.sortBy(nextSessions, 'start');
				$('#sessions-list').append( nextSessionTemplates( {sessions : nextSessions.slice(0,6)} ) );
			});
		});

		/*Course.getAllWithTrashed(function success(data) {
			window.courses = _.indexBy(data, 'id');
			Ticket.getAllWithTrashed(function success(data) {
				window.tickets = _.indexBy(data, 'id');
				Session.filter({after: moment().format('YYYY-MM-DD')}, function success(nextTrips) {
					Class.filter({after: moment().format('YYYY-MM-DD')}, function success(nextClasses) {
						var nextSessions = nextTrips.concat(nextClasses);
						nextSessions = _.sortBy(nextSessions, 'start');
						console.log(nextSessions, 'next');
						$('#sessions-list').append( nextSessionTemplates( {sessions : _.first(nextSessions, 6)} ) );
					});
				},
				function error(xhr){
					var data = JSON.parse(xhr.responseText);
					pageMssg(data.errors[0], 'danger');
				});
			});
		});*/

		$('#sessions-list').on('click', '.accordion-header', function() {
			self = $(this);
			var id   = self.data('id');
			var type = self.data('type');

			if(!self.hasClass('manifest-loaded'))
				getCustomers(id, type);

			self.toggleClass('expanded');
			$('.accordion-' + id).toggle();
		});

		$('#feedback-form').on('submit', function(event) {
			event.preventDefault();
			var params = $(this).serializeObject();
			params._token = window.token;
			var form = $(this);
			Company.sendFeedback(params, function success(data) {
				pageMssg('Thank you for your feedback.', 'success');
				console.log(this);
				form.trigger('reset');	
			},
			function error(xhr) {
				var data = JSON.parse(xhr.responseText);

				if(data.errors.length > 0) pageMssg(data.errors[0]);
				else pageMssg('Sorry, unfortunately we cannot send your feedback right now');
			});
		});

		$('#wrapper').on('click', '.view-booking', function(event) {
			// Load booking data and redirect to add-booking tab
			Booking.getByRef($(this).html(), function success(object) {
				window.booking      = object;
				// window.booking.mode = 'view'; // Should be default behavior
				window.clickedEdit  = true;

				window.location.hash = 'add-booking';
			});
		});
	}
});

function getCustomers(id, type) {

	var customerDetailsTemplate = Handlebars.compile($('#customer-details-template').html());
	var params = "id=" + id;

	var Model;
	switch(type) {
		case 'trip':
			Model = Session;
			break;
		case 'class':
			Model = Class;
			break;
	}

	Model.getAllCustomers(params, function sucess(data) {
		console.log(data);
		//console.log(data.customers);
		$('#sessions-list .accordion-header[data-id=' + id + '][data-type=' + type + ']').addClass('manifest-loaded');
		$('#customer-table-' + id).append( customerDetailsTemplate( {customers : data.customers} ) );
		$('#customers-' + id).DataTable({
			"paging":   false,
			"ordering": false,
			"info":     false,
			"pageLength" : 10,
			"language": {
				"emptyTable": "There are no customers booked for this " + type
			},
		"dom": 'T<"clear">lfrtip'
		});
	});

}

function startWizard()
{
	window.location.href = '#settings';
	$("#guts").prepend($("#tour-nav-wizard").html());
	window.tourStart = true;
	window.currentStep = {
		tab : "#settings",
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

/*function displayFBStats() {

	if(window.facebook.status == "connected") {
		socialMedia = Handlebars.compile($('#social-media-template').html());
		FB.api(
        "/292265320876159/insights",
        {
          'period' : 'week'
        },
        function (response) {
          if (response && !response.error) {
            //console.log(response);
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

}*/

