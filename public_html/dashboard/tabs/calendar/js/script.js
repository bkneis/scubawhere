var filterByBoat = false;
var filterByTrip = false;
var filterByClass = false;
var filterByAccom = false;
var boatFilter;
var tripFilter;
var classFilter;
var accomFilter;
var boatsList;
var tripsList;
var filterSelect;
var display = "trips";

$(function() {

	window.trips = {};
	window.boats = {};
	window.sessions = {};
	window.accommodations = {};

	window.promises  = {};

	boatsList = Handlebars.compile($("#boats-list-template").html());
	tripsList = Handlebars.compile($("#trips-list-template").html());

	filterSelect = Handlebars.compile($("#trip-filter-template").html());
	$("#filter-settings").empty().append(filterSelect());

	// 1. Get trips
	window.promises.loadedTrips = $.Deferred();
	Trip.getAllTrips(function(data) { // async
		window.trips = _.indexBy(data, 'id');
		$("#trips-select").append( tripsList({trips : data}) );
		window.promises.loadedTrips.resolve();
	});

	window.promises.loadedBoats = $.Deferred();
	Boat.getAll(function(data) {
		window.boats = _.indexBy(data, 'id');
		window.promises.loadedBoats.resolve();
	});

	window.promises.loadedClasses = $.Deferred();
	Class.getAll(function(data) {
		window.trainings = _.indexBy(data, 'id');
		window.promises.loadedClasses.resolve();
	});

	window.promises.loadedAccommodations = $.Deferred();
	Accommodation.getAll(function(data) {
		window.accommodations = _.indexBy(data, 'id');
		window.promises.loadedAccommodations.resolve();
	});

	/* Initialize the calendar
	--------------------------*/

	window.promises.loadedTrips.done(function() {
		window.promises.loadedClasses.done(function() {
			window.promises.loadedBoats.done(function() {
				window.promises.loadedAccommodations.done(function() {
					$('#calendar').fullCalendar({
						header: {
							left: 'basicDay basicWeek month',
							center: 'title',
						},
						defaultView : 'basicWeek',
						timezone: false,
						height : 450,
						firstDay: 1, // Set Monday as the first day of the week
						events: function(start, end, timezone, callback) {
							if(display == "trips") getTripEvents(start, end, timezone, callback);
							if(display == "accommodations") getAccomEvents(start, end, timezone, callback);
							if(display == "classes") getClassEvents(start, end, timezone, callback);
						},
						eventRender: function(event, element) {
							// Intercept the event rendering to inject the non-html-escaped version of the title
							// Needed for trip names with special characters in it (like ó, à, etc.)
							element.find('.fc-title').html(event.title);
						},
						editable: false,
						droppable: false, // This allows things to be dropped onto the calendar
						eventClick: function(eventObject) {
							if(display == "trips") showModalWindow(eventObject);
							if(display == "accommodations") showModalWindowA(eventObject);
							if(display == "classes") showModalWindowC(eventObject);
							//console.log(display);
							//console.log(eventObject);
						},
					});
				});
			});
		});
	});

	$("#filter-types").on('click', '.filter-type', function(event){
		event.preventDefault();
		$("#filter-"+display).removeClass("btn-primary");
		display = $(this).attr("display");
		
		if(display == "trips") {
			filterSelect = Handlebars.compile($("#trip-filter-template").html());
			$("#filter-settings").empty().append(filterSelect());
		}
		else if(display == "classes") {
			$('#filter').empty();
			filterSelect = Handlebars.compile($("#class-list-template").html());
			$("#filter-settings").empty().append(filterSelect({classes : window.trainings}));
		}
		else {
			$('#filter').empty();
			filterSelect = Handlebars.compile($("#accom-list-template").html());
			$("#filter-settings").empty().append(filterSelect({accoms : window.accommodations}));
		}
		filterByBoat = false;
		filterByTrip = false;
		filterByAccom = false;
		filterByClass = false;
		$('#calendar').fullCalendar( 'refetchEvents' );
		$("#filter-"+display).addClass("btn-primary");
	});

	/*$('#filter-options').on('change', function(event) {
		event.preventDefault();
		if($("#filter-options").val() == 'boat') {
			$("div#filter-settings option[value=boat]").attr('disabled', true);
			$("#filter-options").val('all');
			$("#filter").append( boatsList({boats : window.boats}) );
		}
		else if($("#filter-options").val() == 'trip') {
			$("div#filter-settings option[value=trip]").attr('disabled', true);
			$("#filter-options").val('all');
			$("#filter").append( tripsList({trips : window.trips}) );
		}
	});*/

	$("#filter").on('change', '.filter', function(event){
		event.preventDefault();
	    	//console.log(this.options[this.selectedIndex].value);
	    	console.log(filter);
	    	if(this.id == "boats") {
	    		var filter = $("#boats option:selected").val();
	    		if(filter == "all") filterByBoat = false;
	    		else filterByBoat = true;
	    		//filterByBoat = true;
	    		boatFilter = this.options[this.selectedIndex].value;
	    		//console.log(boatFilter);
	    	}
	    	else if(this.id == "trips") {
	    		var filter = $("#trips option:selected").val();
	    		if(filter == "all") filterByTrip = false;
	    		else filterByTrip = true;
	    		filterByTrip = true;
	    		tripFilter = this.options[this.selectedIndex].value;
	    		console.log("trip filter =  ",tripFilter);
	    	}
	    	$('#calendar').fullCalendar( 'refetchEvents' );
	    });

	$("#filter-settings").on('change', '.filter', function(event) {
		event.preventDefault();
		if(this.id == "accoms") {
			var filter = $("#accoms option:selected").val();
			console.log(filter);
	    	if(filter == "all") filterByAccom = false;
	    	else filterByAccom = true;
	    	//filterByBoat = true;
	    	accomFilter = this.options[this.selectedIndex].value;
		}
		else {
			var filter = $("#classes option:selected").val();
	    	if(filter == "all") filterByClass = false;
	    	else filterByClass = true;
	    	//filterByBoat = true;
	    	classFilter = this.options[this.selectedIndex].value;
		}
		$('#calendar').fullCalendar( 'refetchEvents' );
	});

	$("#filters").on('click', '#remove-boats-filter', function(event){
		event.preventDefault();
		filterByBoat = false;
		boatFilter = null;
		$("div#filter-settings option[value=boat]").attr('disabled', false);
		$(event.target).parent().remove();
		$('#calendar').fullCalendar( 'refetchEvents' );
	});

	$("#filters").on('click', '#remove-trips-filter', function(event){
		event.preventDefault();
		filterByTrip = false;
		tripFilter = null;
		$("div#filter-settings option[value=trip]").attr('disabled', false);
		$(event.target).parent().remove();
		$('#calendar').fullCalendar( 'refetchEvents' );
	});

	$("#jump-to-date").on('change', '#jump-date', function(event){
		event.preventDefault();
		var date = $("#jump-date").val();
		var jumpDate = $.fullCalendar.moment(date);
		$("#calendar").fullCalendar( 'gotoDate', jumpDate );
		$("#remove-jump").css('display', 'inline');
	});

	$("#jump-to-date").on('click', '#remove-jump', function(event){
		event.preventDefault();
		var date = new Date();
		var d = date.getDate();
	    	var m = date.getMonth() + 1; // jan starts at 0
	    	var y = date.getFullYear();
	    	$("#jump-date").val('');
	    	var sDate = y+'-'+m+'-'+d;
	    	var jumpDate = $("#calendar").fullCalendar.moment(sDate);
			//var moment = $('#calendar').fullCalendar('getDate');
			console.log(moment);
			$("#calendar").fullCalendar( 'gotoDate', jumpDate );
			$("#remove-jump").css('display', 'none');
		});


	$('input.datepicker').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
		clearBtn : true
	});

});

function createCalendarEntry(eventObject) {

	eventObject.start = eventObject.session.start;
	eventObject.end   = $.fullCalendar.moment(eventObject.start).add(eventObject.trip.duration, 'hours');
	eventObject.id    = randomString();
	eventObject.color = reproColor( eventObject.session.boat_id ).bgcolor;
	eventObject.textColor       = reproColor( eventObject.session.boat_id ).txtcolor;
	if(eventObject.session.deleted_at) {
		eventObject.color = colorOpacity(eventObject.color, 0.2);
		if( eventObject.textColor == '#000000') // black
			eventObject.textColor = colorOpacity(eventObject.textColor, 0.3);
	}

	if(eventObject.ticketsLeft == 0) eventObject.color = "#f00";

	return eventObject;

	// Render the event on the calendar
	// The last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
	// $('#calendar').fullCalendar('renderEvent', eventObject, true);
}

function showModalWindow(eventObject) {
	// Create the modal window from session-template
	window.sw.sessionTemplateD = Handlebars.compile( $("#session-template").html() );

	eventObject.boats = $.extend(true, {}, window.boats);
	// console.log(eventObject.session);
	if(!eventObject.session.boat_id) {
		// Set default
		eventObject.session.boat_id = _.values(eventObject.boats)[0].id;
	}
	eventObject.boats[ eventObject.session.boat_id ].selected = true;

	// console.log(eventObject);

	$('#modalWindows')
	.append( window.sw.sessionTemplateD(eventObject) )        // Create the modal
	.children('#modal-' + eventObject.id)          // Directly find it and use it
	.data('eventObject', eventObject)              // Assign the eventObject to the modal DOM element
	.reveal({                                      // Open modal window | Options:
		animation: 'fadeAndPop',                   // fade, fadeAndPop, none
		animationSpeed: 300,                       // how fast animtions are
		closeOnBackgroundClick: false,             // if you click background will modal close?
		dismissModalClass: 'close-modal',   // the class of a button or element that will close an open modal
		'eventObject': eventObject,                  // Submit by reference to later get it as this.eventObject
		onCloseModal: function() {
			// Aborted action
			// debugger;
			if(this.eventObject.isNew) {
				$('#calendar').fullCalendar('removeEvents', this.eventObject.id);
			}

			// Clean up the randomStrings array
			// window.randomStrings.indexOf( this.eventObject.id );
		},
		onFinishModal: function() {
			$('#modal-' + this.eventObject.id).remove();
		},
	});
}

function showModalWindowA(eventObject) {
	// Create the modal window from session-template
	window.sw.accommodationTemplateD = Handlebars.compile( $("#accommodation-template").html() );

	console.log(eventObject);

	$('#modalWindows')
	.append( window.sw.accommodationTemplateD(eventObject) )        // Create the modal
	.children('#modal-' + eventObject.id)          // Directly find it and use it
	.data('eventObject', eventObject)              // Assign the eventObject to the modal DOM element
	.reveal({                                      // Open modal window | Options:
		animation: 'fadeAndPop',                   // fade, fadeAndPop, none
		animationSpeed: 300,                       // how fast animtions are
		closeOnBackgroundClick: false,             // if you click background will modal close?
		dismissModalClass: 'close-modal',   // the class of a button or element that will close an open modal
		'eventObject': eventObject,                  // Submit by reference to later get it as this.eventObject
		onFinishModal: function() {
			$('#modal-' + this.eventObject.id).remove();
		},
	});
}

function showModalWindowC(eventObject) {
	// Create the modal window from session-template
	window.sw.classTemplateD = Handlebars.compile( $("#class-template").html() );

	console.log(eventObject);

	$('#modalWindows')
	.append( window.sw.classTemplateD(eventObject) )        // Create the modal
	.children('#modal-' + eventObject.id)          // Directly find it and use it
	.data('eventObject', eventObject)              // Assign the eventObject to the modal DOM element
	.reveal({                                      // Open modal window | Options:
		animation: 'fadeAndPop',                   // fade, fadeAndPop, none
		animationSpeed: 300,                       // how fast animtions are
		closeOnBackgroundClick: false,             // if you click background will modal close?
		dismissModalClass: 'close-modal',   // the class of a button or element that will close an open modal
		'eventObject': eventObject,                  // Submit by reference to later get it as this.eventObject
		onFinishModal: function() {
			$('#modal-' + this.eventObject.id).remove();
		},
	});
}

function showModalWindowM(id) {
	// Create the modal window from session-template

	var params = "id=" + id;
	if(display == "trips") {
		window.sw.manifestTemplateD = Handlebars.compile( $("#manifest-template").html() );
		Session.getAllCustomers(params, function sucess(data) {
		//showModalWindowM(data);
		//var customer = Handlebars.compile( $("#customer-rows-template").html() );
		//$("#customers-table").append(customer({customers : data.customers}));
		$('#modalWindows')
			.append( window.sw.manifestTemplateD(data) )        // Create the modal
			.children('#modal-' + data.id)          // Directly find it and use it
			.reveal({                                      // Open modal window | Options:
				animation: 'fadeAndPop',                   // fade, fadeAndPop, none
				animationSpeed: 300,                       // how fast animtions are
				closeOnBackgroundClick: false,             // if you click background will modal close?
				dismissModalClass: 'close-modal',   // the class of a button or element that will close an open modal
				onFinishModal: function() {
					$('#modal-' + data.id).remove();
				}
			});
			$('#customer-data-table').dataTable({
				"paging":   false,
				"ordering": false,
				"info":     false,
				"pageLength" : 10,
				"searching" : false,
				data : data.customers,
				columns : [
				{"data" : "firstname"},
				{"data" : "email"},
				{"data" : "country_id"},
				{"data" : "phone"}
				]
			});
		});
	}
	else {
		window.sw.manifestTemplateDC = Handlebars.compile( $("#class-manifest-template").html() );
		Class.getAllCustomers(params, function sucess(data) {
		//showModalWindowM(data);
		//var customer = Handlebars.compile( $("#customer-rows-template").html() );
		//$("#customers-table").append(customer({customers : data.customers}));
		$('#modalWindows')
			.append( window.sw.manifestTemplateDC(data) )        // Create the modal
			.children('#modal-' + data.id)          // Directly find it and use it
			.reveal({                                      // Open modal window | Options:
				animation: 'fadeAndPop',                   // fade, fadeAndPop, none
				animationSpeed: 300,                       // how fast animtions are
				closeOnBackgroundClick: false,             // if you click background will modal close?
				dismissModalClass: 'close-modal',   // the class of a button or element that will close an open modal
				onFinishModal: function() {
					$('#modal-' + data.id).remove();
				}
			});
			$('#customer-data-table').dataTable({
				"paging":   false,
				"ordering": false,
				"info":     false,
				"pageLength" : 10,
				"searching" : false,
				data : data.customers,
				columns : [
				{"data" : "firstname"},
				{"data" : "email"},
				{"data" : "country_id"},
				{"data" : "phone"}
				]
			});
		});
	}

}

Handlebars.registerHelper('date', function(datetime) {
	return datetime.format('DD-MM-YYYY');
});
Handlebars.registerHelper('hours', function(datetime) {
	return datetime.format('HH');
});
Handlebars.registerHelper('minutes', function(datetime) {
	return datetime.format('mm');
});
Handlebars.registerHelper('readableDuration', function(duration) {
	if(duration >= 24)
		return Math.floor(duration/24) + ' days, ' + (duration%24) + ' hours';
	else
		return duration + ' hours';
});
Handlebars.registerHelper('getRemaining', function(capacity, booking) {
	return capacity - booking;
});
Handlebars.registerHelper('isWeekday', function(day) {
	if(this.start.format('d') == day)
		return new Handlebars.SafeString('checked onchange="this.checked=!this.checked;"');
	else
		return '';
});

function calcUtil(booked, capacity) {
	var util = ((booked / capacity) * 100);
	return Math.round(util).toString();
}

function getTripEvents(start, end, timezone, callback) {

	// Start loading indicator
	$('.fc-center h2').after('<div id="fetch-events-loader" class="loader"></div>');
	//console.log(start.format(), end.format());
	var sessionFilters = {
		'after': start.format(),
		'before': end.format(),
		'with_full': 1
	};
	if(filterByTrip) sessionFilters.trip_id = tripFilter;
	Session.filter(sessionFilters, function success(data) {
		//console.log(data);
		window.sessions = _.indexBy(data, 'id');

		console.log(window.sessions);

		var events = [];

		// Create eventObjects
		_.each(window.sessions, function(value) {
			if(filterByBoat) {
				if(boatFilter == value.boat_id) {
					var booked = value.capacity[0];
					var capacity = value.capacity[1];
					var ticketsLeft = capacity - booked;
					var sameDay = true;
					if(window.trips[value.trip_id].duration > 24) sameDay = false;
					var eventObject = {
						title: window.trips[ value.trip_id ].name + ' ' + calcUtil(booked, capacity) + '%', // use the element's text as the event title
						allDay: false,
						trip: window.trips[ value.trip_id ],
						session: value,
						isNew: false,
						editable: false, // This uses a 'falsy' check on purpose
						durationEditable: false,
						//className: value.timetable_id ? 'timetabled' : '',*/ // This uses a 'falsy' check on purpose
						ticketsLeft : ticketsLeft,
						capacity : capacity,
						sameDay : sameDay
					};

					if(ticketsLeft == 0) eventObject.title = window.trips[ value.trip_id ].name + " FULL";

					eventObject.session.start = $.fullCalendar.moment(value.start);

					events.push( createCalendarEntry(eventObject) );
				}
			}
			else {
				var booked = value.capacity[0];
				var capacity = value.capacity[1];
				var ticketsLeft = capacity - booked;
				var sameDay = true;
				if(window.trips[value.trip_id].duration > 24) sameDay = false;
				var eventObject = {
					title: window.trips[ value.trip_id ].name + ' ' + calcUtil(booked, capacity) + '%', // use the element's text as the event title
					allDay: false,
					trip: window.trips[ value.trip_id ],
					session: value,
					isNew: false,
					editable: false, // This uses a 'falsy' check on purpose
					durationEditable: false,
					//className: value.timetable_id ? 'timetabled' : '',*/ // This uses a 'falsy' check on purpose
					ticketsLeft : ticketsLeft,
					capacity : capacity,
					sameDay : sameDay
				};

				if(ticketsLeft == 0) eventObject.title = window.trips[ value.trip_id ].name + " FULL";

				eventObject.session.start = $.fullCalendar.moment(value.start);

				events.push( createCalendarEntry(eventObject) );

			}
		});

callback(events);

		// Remove loading indictor
		$('#fetch-events-loader').remove();
	},
	function error(xhr){
		$('.loader').remove();
	});

}

function getClassEvents(start, end, timezone, callback) {

	// Start loading indicator
	$('.fc-center h2').after('<div id="fetch-events-loader" class="loader"></div>');
	//console.log(start.format(), end.format());
	var sessionFilters = {
		'after': start.format(),
		'before': end.format(),
		'with_full': 1
	};
	if(filterByClass) sessionFilters.training_id = classFilter;
	var events = [];

	Class.filter(sessionFilters, function success(data) {
		window.trainingSessions = _.indexBy(data, 'id');
		console.log(data);

		// Create eventObjects
		_.each(window.trainingSessions, function(value) {
			var eventObject = {
				title: window.trainings[ value.training_id ].name, // use the element's text as the event title
				allDay: false,
				trip: window.trainings[ value.training_id ],
				session: value,
				isNew: false,
				editable: value.timetable_id ? false : true, // This uses a 'falsy' check on purpose
				durationEditable: false,
				className: value.timetable_id ? 'timetabled' : '', // This uses a 'falsy' check on purpose,
				isTrip : false
			};

			eventObject.session.start = $.fullCalendar.moment(value.start);

			events.push( createCalendarEntry(eventObject) );
		});

		callback(events);

		$('#fetch-events-loader').remove();

	},
	function error(xhr){
		$('.loader').remove();
	});

}

function getAccomEvents(start, end, timezone, callback) {

	$('.fc-center h2').after('<div id="fetch-events-loader" class="loader"></div>');
	var sessionFilters = {
		'after': start.format(),
		'before': end.format(),
		'with_full': 1
	};
	if(filterByAccom) sessionFilters.accommodation_id = accomFilter;
	Accommodation.filter(sessionFilters, function success(data) {

		console.log(data);
		var events = [];

		_.each(data, function(value, key) {
			var start = new moment(key);

			_.each(value, function(util, id) {
				var eventObject = {
		            start: start, // change start to readable text instead of moment
		            end : start,
		            id : randomString(),
		            title: window.accommodations[ id ].name,
		            color : "#229930",
		            booked : util[0],
		            available : (util[1] - util[0])
		        };
		        if(eventObject.available == 0) eventObject.color = "#f00";
		        events.push( eventObject );
		        //$('#calendar').renderEvent(eventObject);
		    });
		});

		callback(events);
		$('#fetch-events-loader').remove();
	},
	function error(xhr){
		$('.loader').remove();
	});
}

function showManifest(id) { // use data tables to isnert data instead of handle bars
	var params = "id=" + id;
	Session.getAllCustomers(params, function sucess(data) {
		showModalWindowM(data);
		var customer = Handlebars.compile( $("#customer-rows-template").html() );
		$("#customers-table").append(customer({customers : data.customers}));
	});
}

function addTripFilter(value) {

	if(value == 'boat') {
		$("div#filter-settings option[value=boat]").attr('disabled', true);
		$("#filter-options").val('all');
		$("#filter").append( boatsList({boats : window.boats}) );
	}
	else if(value == 'trip') {
		$("div#filter-settings option[value=trip]").attr('disabled', true);
		$("#filter-options").val('all');
		$("#filter").append( tripsList({trips : window.trips}) );
	}

}
