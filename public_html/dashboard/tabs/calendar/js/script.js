var filterByBoat = false;
var filterByTrip = false;
var boatFilter;
var tripFilter;
var boatsList;
var tripsList;
var display = "trips";
$(function() {

  window.trips = {};
	window.boats = {};
	window.sessions = {};
	window.accommodations = {};

	boatsList = Handlebars.compile($("#boats-list-template").html());
	tripsList = Handlebars.compile($("#trips-list-template").html());

	// 1. Get trips
	Trip.getAllTrips(function(data) { // async
		window.trips = _.indexBy(data, 'id');
		$("#trips-select").append( tripsList({trips : data}) );
	});

	Boat.getAll(function(data) {
		window.boats = _.indexBy(data, 'id');
	});

	Accommodation.getAll(function(data) {
		window.accommodations = _.indexBy(data, 'id');
	});

	/* Initialize the calendar
	--------------------------*/
	$('#calendar').fullCalendar({
		header: {
			left: '',
			center: 'title',
		},
		defaultView : 'basicWeek',
		timezone: false,
		height : 450,
		firstDay: 1, // Set Monday as the first day of the week
		events: function(start, end, timezone, callback) {
			if(display == "trips") getTripEvents(start, end, timezone, callback);
			if(display == "accommodations") getAccomEvents(start, end, timezone, callback);
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
			//console.log(display);
			//console.log(eventObject);
		},
	});

	/*$('.collapsible').collapsible({
        defaultOpen: 'filters'
    });*/

    $('#filter-options').on('change', function(event) {
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
    });

    $("#filter").on('change', '.filter', function(event){
    	event.preventDefault();
    	//console.log(this.options[this.selectedIndex].value);
    	console.log(filter);
    	if(this.id == "boats") {
    		if(filter == "all") filterByBoat = false;
    		else filterByBoat = true;
    		filterByBoat = true;
    		boatFilter = this.options[this.selectedIndex].value;
    		//console.log(boatFilter);
    	}
    	else if(this.id == "trips") {
    		var filter = $("#trips:selected").val();
    		if(filter == "all") filterByTrip = false;
    		else filterByTrip = true;
    		filterByTrip = true;
    		tripFilter = this.options[this.selectedIndex].value;
    		console.log("trip filter =  ",tripFilter);
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

	$("#jump-to-date").on('click', '#jump-to', function(event){
		event.preventDefault();
		var date = $("#jump-date").val();
		var month = $("#jump-month").val();
		var year = $("#jump-year").val();
		var jumpDate = $.fullCalendar.moment(year+'-'+month+'-'+date);
		$("#calendar").fullCalendar( 'gotoDate', jumpDate );
	});

	$("#jump-to-date").on('click', '#remove-jump', function(event){
		event.preventDefault();
		var date = new Date();
    	var d = date.getDate();
    	var m = date.getMonth() + 1; // jan starts at 0
    	var y = date.getFullYear();
    	$("#jump-date").val('');
    	$("#jump-month").val('');
    	$("#jump-year").val('');
		var jumpDate = $.fullCalendar.moment(y+'-'+m+'-'+d);
		$("#calendar").fullCalendar( 'gotoDate', jumpDate );
	});

	$("#myonoffswitch").click(function() {
		if($(this).is(':checked')) display = "trips";
		else display = "accommodations";
		$('#calendar').fullCalendar( 'refetchEvents' );
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
Handlebars.registerHelper('isWeekday', function(day) {
	if(this.start.format('d') == day)
		return new Handlebars.SafeString('checked onchange="this.checked=!this.checked;"');
	else
		return '';
});

var randomStrings = [];
function randomString() {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 15;
	var result = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		result += chars.substring(rnum,rnum+1);
	}

	if(_.indexOf(randomStrings, result) >= 0)
	{
		// If the random string is not unique (unlikely, but possible) the function recursively calls itself again
		return randomString();
	}
	else
	{
		// When the random string has been approved as unique, it is added to the list of generated strings and then returned
		randomStrings.push(result);
		return result;
	}
}

function calcUtil(booked, capacity) {
	var util = ((booked / capacity) * 100);
	return util.toString();
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

function getAccomEvents(start, end, timezone, callback) {

	$('.fc-center h2').after('<div id="fetch-events-loader" class="loader"></div>');
	var sessionFilters = {
		'after': start.format(),
		'before': end.format(),
		'with_full': 1
	};
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
