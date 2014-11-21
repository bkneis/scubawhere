$(function() {

	window.trips;
	window.boats;
	window.token;
	window.sessions;
	
	// 1. Get trips
	Trip.getAllTrips(function(data) { // async
		window.trips = _.indexBy(data, 'id');
	});

	Boat.getAll(function(data) {
		window.boats = _.indexBy(data, 'id');
	});

	$.get("/token", null, function(data) {
		window.token = data;
	});

	/* Initialize the calendar
	--------------------------*/
	$('#calendar').fullCalendar({
		header: {
			left: '',
			center: 'title',
		},
		timezone: false,
		firstDay: 1, // Set Monday as the first day of the week
		events: function(start, end, timezone, callback) {
			getTripEvents(start, end, timezone, callback);
		},
		eventRender: function(event, element) {
			// Intercept the event rendering to inject the non-html-escaped version of the title
			// Needed for trip names with special characters in it (like ó, à, etc.)
			element.find('.fc-title').html(event.title);
		},
		editable: false,
		droppable: false, // This allows things to be dropped onto the calendar
		eventClick: function(eventObject) {
			showModalWindow(eventObject);
			//console.log(eventObject);
		},
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

	if(eventObject.ticketsLeft == 0) eventObject.color = "#4B0082";

	return eventObject;

	// Render the event on the calendar
	// The last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
	// $('#calendar').fullCalendar('renderEvent', eventObject, true);
}

function showModalWindow(eventObject) {
	// Create the modal window from session-template
	if(!window.sw.sessionTemplateD) window.sw.sessionTemplateD = Handlebars.compile( $("#session-template").html() );

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

	// Set timetable form _token
	if(window._token)
		$('#modalWindows [name="_token"]').val(_token);

	$.ajax({
		url: "/token",
		type: "GET",
		dataType: "html",
		success: function(_token) {
			$('#modalWindows [name="_token"]').val(_token);
			window._token = _token;
		}
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
	return util.toString() + '%';
}

function getTripEvents(start, end, timezone, callback) {

	// Start loading indicator
	$('.fc-center h2').after('<div id="fetch-events-loader" class="loader"></div>');
	//console.log(start.format(), end.format());
	Session.filter({
		'after': start.format(),
		'before': end.format(),
		'with_full': 1
	}, function success(data) {
		//console.log(data);
		sessions = _.indexBy(data, 'id');

		console.log(sessions);

		events = [];

		// Create eventObjects
		_.each(sessions, function(value) {
			var booked = data[value.trip_id].capacity[0];
			var capacity = data[value.trip_id].capacity[1];
			var ticketsLeft = capacity - booked;
			var sameDay = true;
			if(window.trips[value.trip_id].duration > 24) sameDay = false;
			var eventObject = {
				title: window.trips[ value.trip_id ].name + ' ' + calcUtil(booked, capacity), // use the element's text as the event title
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

			if(ticketsLeft == 0) eventObject.title = window.trips[ value.trip_id ].name + " FULL"

			eventObject.session.start = $.fullCalendar.moment(value.start);

			events.push( createCalendarEntry(eventObject) );
		});

		callback(events);

		// Remove loading indictor
		$('#fetch-events-loader').remove();
	},
	function error(xhr){
		$('.loader').remove();
	});

}
