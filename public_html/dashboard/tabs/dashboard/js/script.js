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

	_.each(pivot.refunds, function(obj) {
		payments -= parseFloat(obj.amount);
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

Handlebars.registerHelper('convertPrice', function(price) {
	return new Handlebars.SafeString(window.company.currency.symbol + ' ' + (parseFloat(price) / 100).toFixed(2));
})

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

		var gotCourses   = $.Deferred();
		var gotTickets   = $.Deferred();
		var gotSessions  = $.Deferred();
		var gotCountries = $.Deferred();

		$.get("/api/country/all", function success(data) {
			window.countries = _.indexBy(data, 'id');
			gotCountries.resolve();
		});

		$.when(gotCountries).done(function() {
			Course.getAllWithTrashed(function success(data) {
				window.courses = _.indexBy(data, 'id');
				gotCourses.resolve();
			});
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

function showModalWindowManifest(id, type) {
	// Create the modal window from manifest-template
	var params = {
		id: id
	};
	if (type == 'trip') {
		window.sw.manifestTemplateD = Handlebars.compile($("#manifest-template").html());
		Session.getAllCustomers(params, function success(data) {
			//showModalWindowManifest(data);
			//var customer = Handlebars.compile( $("#customer-rows-template").html() );
			//$("#customers-table").append(customer({customers : data.customers}));
			$('#modalWindows')
				.append(window.sw.manifestTemplateD(data)) // Create the modal
				.children('#modal-' + data.id) // Directly find it and use it
				.reveal({ // Open modal window | Options:
					animation: 'fadeAndPop', // fade, fadeAndPop, none
					animationSpeed: 300, // how fast animtions are
					closeOnBackgroundClick: true, // if you click background will modal close?
					dismissModalClass: 'close-modal', // the class of a button or element that will close an open modal
					onFinishModal: function() {
						$('#modal-' + data.id).remove();
					}
				});

			var table = $('#customer-data-table').DataTable({
				"pageLength": 10,
				columns: [{
					data: null,
					render: 'reference'
				}, {
					data: null,
					render: 'status'
				}, {
					data: null,
					render: 'name'
				}, {
					data: null,
					render: 'country'
				}, {
					data: null,
					render: 'phone'
				}, {
					data: null,
					render: 'ticket'
				}, {
					data: null,
					render: 'lastDive'
				}, {
					data: null,
					render: 'notes'
				}, {
					data: null,
					render: 'shoe'
				}, {
					data: null,
					render: 'chest'
				}, {
					data: null,
					render: 'height'
				}],
				"dom": '<"col-md-6 dt-buttons"B><"col-md-6"f>rt<"col-md-6"l><"col-md-6"p>',
				"buttons": [
					{
						extend : 'excel',
						title  : getFileName('trip', data)
					},
					{
						extend : 'pdf',
						title  : getFileName('trip', data),
						orientation: 'landscape'
					},
					{
						extend : 'print',
						title  : getFileName('trip', data)
					}
				]
			});

			$.when(
				window.promises.loadedCountries,
				window.promises.loadedCourses,
				window.promises.loadedTickets
			).done(function() {
				for (var i = 0; i < data.customers.length; i++) {
					table.row.add(new customerData(data.customers[i]));
				};

				table.draw();
			});
		});
	} else {
		window.sw.manifestTemplateDC = Handlebars.compile($("#class-manifest-template").html());
		Class.getAllCustomers(params, function success(data) {
			//showModalWindowManifest(data);
			//var customer = Handlebars.compile( $("#customer-rows-template").html() );
			//$("#customers-table").append(customer({customers : data.customers}));
			$('#modalWindows')
				.append(window.sw.manifestTemplateDC(data)) // Create the modal
				.children('#modal-' + data.id) // Directly find it and use it
				.reveal({ // Open modal window | Options:
					animation: 'fadeAndPop', // fade, fadeAndPop, none
					animationSpeed: 300, // how fast animtions are
					closeOnBackgroundClick: true, // if you click background will modal close?
					dismissModalClass: 'close-modal', // the class of a button or element that will close an open modal
					onFinishModal: function() {
						$('#modal-' + data.id).remove();
					}
				});

			var table = $('#customer-data-table').DataTable({
				"paging": false,
				"ordering": false,
				"info": false,
				"pageLength": 10,
				"searching": false,
				columns: [{
					data: null,
					render: 'reference'
				}, {
					data: null,
					render: 'status'
				}, {
					data: null,
					render: 'name'
				}, {
					data: null,
					render: 'country'
				}, {
					data: null,
					render: 'phone'
				}, {
					data: null,
					render: 'course'
				}, {
					data: null,
					render: 'lastDive'
				}, {
					data: null,
					render: 'notes'
				}, {
					data: null,
					render: 'shoe'
				}, {
					data: null,
					render: 'chest'
				}, {
					data: null,
					render: 'height'
				}],
				"dom": 'Bfrtlp',
				"buttons": [
					{
						extend : 'excel',
						title  : getFileName('training', data)
					},
					{
						extend : 'pdf',
						title  : getFileName('training', data)
					},
					{
						extend : 'print',
						title  : getFileName('training', data)
					}
				]
			});

			$.when(
				window.promises.loadedCountries,
				window.promises.loadedCourses,
				window.promises.loadedTickets
			).done(function() {
				for (var i = 0; i < data.customers.length; i++) {
					table.row.add(new customerData(data.customers[i]));
				};

				table.draw();
			});
		});
	}
}

function customerData(customer) {
	this._name = customer.firstname + ' ' + customer.lastname;
	this._phone = customer.phone;
	this._country = window.countries[customer.country_id].abbreviation;
	if (customer.pivot.ticket_id != null) {
		this._ticket = window.tickets[customer.pivot.ticket_id].name;
	}
	if (customer.pivot.course_id != null) {
		this._course = window.courses[customer.pivot.course_id].name;
	}

	this._chest = customer.chest_size || "-";
	this._shoe = customer.shoe_size || "-";
	this._height = customer.height || "-";
	this._lastDive = customer.last_dive || "-";
	this._reference = customer.pivot.reference;
	this._booking_id = customer.pivot.booking_id;
	this._notes = customer.pivot.notes || "-";
	this._status = customer.pivot.status;
	this._price = customer.pivot.decimal_price;

	var paid = 0;

	_.each(customer.pivot.payments, function(obj) {
		paid += parseFloat(obj.amount);
	});
	_.each(customer.pivot.refunds, function(obj) {
		paid -= parseFloat(obj.amount);
	});

	this.amount_paid = paid.toFixed(2);

	this.name = function() {
		return this._name;
	};

	this.phone = function() {
		return this._phone + '&nbsp;'; // The forced space at the end makes the phone number be recognised as a string in Excel (instead of a number, when exporting via DataTable's CSV/Excel export)
	};

	this.country = function() {
		return this._country;
	};

	this.ticket = function() {
		return this._ticket;
	};

	this.course = function() {
		return this._course;
	};

	this.chest = function() {
		return this._chest;
	};

	this.shoe = function() {
		return this._shoe;
	};

	this.height = function() {
		return this._height;
	};

	this.lastDive = function() {
		return this._lastDive;
	};

	this.reference = function() {
		return '<a href="javascript:void(0);" onclick="editBooking(' + this._booking_id + ', this);">' + this._reference + '</a>';
	};

	this.notes = function() {
		return this._notes;
	};

	this.status = function() {
		return window.company.currency.symbol + ' ' + this.amount_paid + ' / ' + window.company.currency.symbol + ' ' + this._price;
	};
}

function getFileName(type, data) {
	var name;
	if(type === 'trip') {
		name = data.trip.name;
	} else {
		name = data.training.name;
	}
	return name + ' Trip Manifest - ' + data.start;
}
