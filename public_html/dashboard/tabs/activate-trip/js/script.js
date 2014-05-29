$(function() {

	// Render a list of trips
	var tripsTemplate = $("#trip-list").html();
	tripsTemplate     = Handlebars.compile(tripsTemplate);

	window.trips;
	window.boats;
	window.token;
	window.sessions;

	// 1. Get trips
	Trip.getAllTrips(function(data) { // async
		window.trips = _.indexBy(data, 'id');
		$('#trips ul').append(tripsTemplate({trips: data}));
		initDraggables();

		// 2. Get sessions
		Sessions.getAllSessions(function(data) {
			window.sessions = _.indexBy(data, 'id');
			_.each(window.sessions, function(value) {
				var eventObject = {
					title: window.trips[ value.trip_id ].name, // use the element's text as the event title
					allDay: false,
					trip: window.trips[ value.trip_id ],
					session: value,
					isNew: false,
				};
				// console.log("----------------------------------------");
				// console.log("From Server:        " + eventObject.session.startObj);
				// Parse server's UTC time and convert to local
				// TODO change local() to "setTimezone" (set the user's profile timezone) (don't trust the browser)
				eventObject.session.startObj = $.fullCalendar.moment.utc(value.start).local();
				// console.log("Converted to local: " + eventObject.session.startObj.format('YYYY-MM-DD HH:mm:ss'));

				createCalendarEntry(eventObject);
			});
		});
	});

	Boat.getAllBoats(function(data) {
		window.boats = _.indexBy(data.boats, 'id');
	});

	$.get("/token", null, function(data) {
		window.token = data;
	});


	/* Initialize the external events
	---------------------------------*/
	function initDraggables() {
		$('.trip-event').each(function() {

			// Create an eventObject (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
			// It doesn't need to have a start or end as that is assigned onDrop
			var eventObject = {
				title   : $.trim( $(this).text() ), // use the element's text as the event title
				allDay  : false,
				id      : randomString(),
				trip    : window.trips[ $(this).attr('data-id') ],
				session : {
					trip_id: $(this).attr('data-id'),
				},
				isNew   : true,
			};

			// Store the eventObject in the DOM element so we can get it back later
			$(this).data('eventObject', eventObject);

			// Make the event draggable using jQuery UI
			$(this).draggable({
				zIndex: 999,
				revert: true,      // Causes the event to go back to its original position after the drag
				revertDuration: 0,
				helper: "clone",
				containment: "document",
			});
		});
	}

	/* Initialize the calendar
	--------------------------*/
	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay',
		},
		timezone: 'local', // TODO Change to user's profile timezone (don't trust the browser)
		events: [],
		editable: false,
		droppable: true, // This allows things to be dropped onto the calendar
		drop: function(date) { // This function is called when something is dropped

			// Retrieve the dropped element's stored Event Object
			var originalEventObject = $(this).data('eventObject');

			// We need to copy it, so that multiple events don't have a reference to the same object
			var eventObject = $.extend({}, originalEventObject);

			/**
			 * The date reported by the calendar, when a trip is dropped, is
			 * in no-zone, no-time UTC (e.g. 2014-05-29 00:00:00). The problem
			 * is, that when the user's local timezone is negative (e.g. -4:30
			 * for Newfoundland), the conversion into local would reduce the
			 * date by one day (in this example to 2014-05-28). This is why we
			 * instead just fetch the date from the supplied object and create
			 * a new date object from it with a default set time.
			 */
			date = date.format('YYYY-MM-DD');
			eventObject.session.startObj = $.fullCalendar.moment(date + ' 09:00:00');
			// console.log("Set to 9 hours:     " + eventObject.session.startObj.format('YYYY-MM-DD HH:mm:ss'));

			createCalendarEntry(eventObject);

			showModalWindow(eventObject);
		},
		eventClick: function(eventObject) {
			showModalWindow(eventObject);
		},
	});

	/* HACK	*/
	setTimeout(function() {
		$('#calendar').fullCalendar( 'today' );
	}, 100);

	$('#modalWindows').on('change', '.boatSelect', function(event) {
		eventObject = $(event.target).closest('.reveal-modal').data('eventObject');

		// Unset old value
		delete eventObject.boats[ eventObject.session.boat_id ].selected;

		// Assign selected value
		eventObject.session.boat_id = event.target.value;
	});

	$('#modalWindows').on('change', '.starthours, .startminutes', function(event) {
		// Validation and correction
		if(event.target.value < 0) event.target.value = 0;
		if(event.target.value.length == 1) event.target.value = "0" + event.target.value;

		if( $(event.target).is('.starthours') ) {
			if(event.target.value >= 24) event.target.value = 23;

			starthours = event.target.value;

			startminutes = $(event.target).siblings('input').first().val();
			if(startminutes.length == 1)
				startminutes = $(event.target).siblings('input').first().val("0" + startminutes);
		}
		else {
			if(event.target.value >= 60) event.target.value = 59;

			startminutes = event.target.value;

			starthours   = $(event.target).siblings('input').first().val();
			if(starthours.length == 1)
				starthours = $(event.target).siblings('input').first().val("0" + starthours);
		}

		starthours = parseInt(starthours, 10);
		startminutes = parseInt(startminutes, 10);

		eventObject = $(event.target).closest('.reveal-modal').data('eventObject');

		// Create new independend moment object
		eventObject.session.newStart = $.fullCalendar.moment( eventObject.session.startObj.format('YYYY-MM-DD HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss' ).hours( starthours ).minutes( startminutes );

		// console.log(eventObject.session.startObj.format('HH:mm on DD-MM-YYYY'));

		// Update displayed end datetime
		tempEnd = $.fullCalendar.moment(eventObject.session.newStart).add('hours', eventObject.trip.duration);
		$(event.target)
			.closest('.reveal-modal')
			.find('.enddatetime')
			.text( tempEnd.format('HH:mm on DD-MM-YYYY') );
		delete tempEnd;
	});

	// Finally, the ACTIVATE button
	$('#modalWindows').on('click', '.submit-session', function(event) {
		modal = $(event.target).closest('.reveal-modal');
		eventObject = modal.data('eventObject');

		// Whenever the session is saved, it is not new anymore
		eventObject.isNew = false;

		// Get time from input boxes
		starthours   = modal.find('.starthours').val();
		startminutes = modal.find('.startminutes').val();
		eventObject.session.startObj.hours(starthours).minutes(startminutes);

		console.log(eventObject.session.startObj.format('YYYY-MM-DD HH:mm:ss'));

		eventObject.session._token = window.token;

		// Clean up
		if(eventObject.session.newStart) {
			delete eventObject.session.newStart;
		}

		// Format the time in a PHP readable format
		eventObject.session.start = eventObject.session.startObj.utc().format('YYYY-MM-DD HH:mm:ss');
		eventObject.session.startObj.local();

		Sessions.createSession(_.omit(eventObject.session, 'startObj'), function(data){
			// Sync worked, now save and update the calendar item

			// Remake the moment-object (parse as UTC, convert to local to work with)
			// TODO Change local() to "setTimezone" (set to user's profile timezone) (don't trust the browser)
			// eventObject.session.startObj = $.fullCalendar.moment.utc(eventObject.session.startObj, 'YYYY-MM-DD HH:mm:ss').local();
			eventObject.session.id = data.id;

			updateCalendarEntry(eventObject);

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data['status'], true);
		});
	});

	// The UPDATE button
	$('#modalWindows').on('click', '.update-session', function(event) {

	});
});

function createCalendarEntry(eventObject) {

	eventObject.start = eventObject.session.startObj;
	eventObject.end   = $.fullCalendar.moment(eventObject.start).add('hours', eventObject.trip.duration);
	eventObject.id    = randomString();
	eventObject.backgroundColor = reproColor( eventObject.session.boat_id ).bgcolor;
	eventObject.textColor       = reproColor( eventObject.session.boat_id ).txtcolor;

	// Render the event on the calendar
	// The last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
	$('#calendar').fullCalendar('renderEvent', eventObject, true);
}

function updateCalendarEntry(eventObject) {

	eventObject.start = eventObject.session.startObj;
	eventObject.end   = $.fullCalendar.moment(eventObject.start).add('hours', eventObject.trip.duration);

	eventObject.backgroundColor = reproColor( eventObject.session.boat_id ).bgcolor;
	eventObject.textColor       = reproColor( eventObject.session.boat_id ).txtcolor;

	// $('#calendar').fullCalendar('updateEvent', eventObject);

	// Because f***ing updateEvent doesn't work, we need to remove and render it again
	$('#calendar').fullCalendar('removeEvents', eventObject.id);
	$('#calendar').fullCalendar('renderEvent', eventObject, true);
}

function showModalWindow(eventObject) {
	// Create the modal window from session-template
	var sessionTemplate = $("#session-template").html();
	sessionTemplate     = Handlebars.compile(sessionTemplate);

	eventObject.boats = window.boats;
	console.log(eventObject.session);
	if(!eventObject.session.boat_id) {
		// Set default
		eventObject.session.boat_id = _.values(eventObject.boats)[0].id;
	}

	eventObject.boats[ eventObject.session.boat_id ].selected = true;

	$('#modalWindows')
	.append( sessionTemplate(eventObject) )        // Create the modal
	.children('#modal-' + eventObject.id)          // Directly find it and use it
	.data('eventObject', eventObject)              // Assign the eventObject to the modal DOM element
	.reveal({                                      // Open modal window | Options:
		animation: 'fadeAndPop',                   // fade, fadeAndPop, none
		animationSpeed: 300,                       // how fast animtions are
		closeOnBackgroundClick: false,             // if you click background will modal close?
		dismissModalClass: 'close-modal',   // the class of a button or element that will close an open modal
		eventObject: eventObject,                  // Submit by reference to later get it as this.eventObject
		onCloseModal: function() {
			// Aborted action
			if(this.eventObject.isNew) {
				$('#calendar').fullCalendar('removeEvents', this.eventObject.id);
			}

			// Unset old boat value
			delete this.eventObject.boats[ this.eventObject.session.boat_id ].selected;

			// Clean up the randomStrings array
			// window.randomStrings.indexOf( this.eventObject.id );
		},
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

function checkOverlap(event) {

	var start = new Date(event.start);
	var end = new Date(event.end);

	var overlap = $('#calendar').fullCalendar('clientEvents', function(ev) {
		if( ev == event)
			return false;
		var estart = new Date(ev.start);
		var eend = new Date(ev.end);

		return (Math.round(estart)/1000 < Math.round(end)/1000 && Math.round(eend) > Math.round(start));
	});

	if (overlap.length){
		alert("Overlap");
	}
}

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
