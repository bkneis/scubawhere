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
					durationEditable: false,
					startEditable: value.capacity[0] == 0, // the session has not been booked yet, so it's ok to move it
				};

				// console.log("----------------------------------------");
				// console.log("From Server:        " + eventObject.session.start);
				// Parse server's UTC time and convert to local
				// TODO change local() to "setTimezone" (set the user's profile timezone) (don't trust the browser)
				eventObject.session.start = $.fullCalendar.moment.utc(value.start).local();
				// console.log("Converted to local: " + eventObject.session.start.format('YYYY-MM-DD HH:mm:ss'));

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
				durationEditable: false,
				startEditable: true,
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
		editable: true,
		droppable: true, // This allows things to be dropped onto the calendar
		drop: function(date) { // This function is called when something is dropped

			// Retrieve the dropped element's stored Event Object
			var originalEventObject = $(this).data('eventObject');

			// We need to copy it, so that multiple events don't have a reference to the same object
			var eventObject = $.extend(true, {}, originalEventObject);
			// delete eventObject.session.boat_id;

			/**
			 * UTC problem
			 * ===========
			 * The date reported by the calendar, when a trip is dropped, is
			 * in no-zone, no-time UTC (e.g. 2014-05-29 00:00:00). The problem
			 * is, that when the user's local timezone is negative (e.g. -4:30
			 * for Newfoundland), the conversion into local would reduce the
			 * date by one day (in this example to 2014-05-28). This is why we
			 * instead just fetch the date from the supplied object and create
			 * a new date object from it with a default set time.
			 */
			var date = date.format('YYYY-MM-DD');
			eventObject.session.start = $.fullCalendar.moment(date + ' 09:00:00');
			// console.log("Set to 9 hours: " + eventObject.session.start.format('YYYY-MM-DD HH:mm:ss'));

			createCalendarEntry(eventObject);

			showModalWindow(eventObject);
		},
		eventDrop: function(eventObject, revertFunc) {
			if(!eventObject.start.hasTime()) {
				// Combine dropped-on date and session's start time
				// See UTC problem above
				var date = date.format('YYYY-MM-DD');
				eventObject.start = $.fullCalendar.moment(date + ' ' + eventObject.session.start.format('HH:mm:ss'));
			}
			eventObject.session.start = eventObject.start;

			eventObject.session._token = window.token;

			// Format the time in a PHP readable format
			eventObject.session.start = eventObject.session.start.utc().format('YYYY-MM-DD HH:mm:ss');

			// console.log(eventObject.session);

			Sessions.updateSession(eventObject.session, function success(data){
				// Sync worked, now save and update the calendar item

				// Remake the moment-object (parse as UTC, convert to local to work with)
				// TODO Change local() to "setTimezone" (set to user's profile timezone) (don't trust the browser)
				eventObject.session.start = $.fullCalendar.moment.utc(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss').local();

				updateCalendarEntry(eventObject);
				// console.log(data.status + '|' + data.id);

				// Close modal window
				$('#modalWindows .close-reveal-modal').click();

				pageMssg(data.status, true);
			}, function error(xhr) {
				revertFunc();

				eventObject.session.start = $.fullCalendar.moment(eventObject.start.format('YYYY-MM-DD HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss');

				updateCalendarEntry(eventObject);

				pageMssg(xhr.responseText);
			});
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
		var eventObject = $(event.target).closest('.reveal-modal').data('eventObject');

		// Assign selected value
		eventObject.session.newBoat_id = event.target.value;
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

		var starthours = parseInt(starthours, 10);
		var startminutes = parseInt(startminutes, 10);

		var eventObject = $(event.target).closest('.reveal-modal').data('eventObject');

		// Create new independend moment object
		var newStart = $.fullCalendar.moment( eventObject.session.start.format('YYYY-MM-DD HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss' ).hours( starthours ).minutes( startminutes );

		// console.log(eventObject.session.start.format('HH:mm on DD-MM-YYYY'));

		// Update displayed end datetime
		var tempEnd = $.fullCalendar.moment(newStart).add('hours', eventObject.trip.duration);
		$(event.target)
			.closest('.reveal-modal')
			.find('.enddatetime')
			.text( tempEnd.format('HH:mm on DD-MM-YYYY') );
		delete tempEnd;
	});

	// Finally, the ACTIVATE button
	$('#modalWindows').on('click', '.submit-session', function(event) {
		var modal = $(event.target).closest('.reveal-modal');
		var eventObject = modal.data('eventObject');

		// Whenever the session is saved, it is not new anymore
		eventObject.isNew = false;

		// Get time from input boxes
		var starthours   = modal.find('.starthours').val();
		var startminutes = modal.find('.startminutes').val();
		eventObject.session.start.hours(starthours).minutes(startminutes);

		// console.log(eventObject.session.start.format('YYYY-MM-DD HH:mm:ss'));

		eventObject.session._token = window.token;

		// debugger;
		eventObject.session.boat_id = eventObject.session.newBoat_id || eventObject.session.boat_id;
		// debugger;

		// Format the time in a PHP readable format
		eventObject.session.start = eventObject.session.start.utc().format('YYYY-MM-DD HH:mm:ss');
		// eventObject.session.start.local();

		// console.log(eventObject.isNew);

		Sessions.createSession(eventObject.session, function(data){
			// Sync worked, now save and update the calendar item

			// Remake the moment-object (parse as UTC, convert to local to work with)
			// TODO Change local() to "setTimezone" (set to user's profile timezone) (don't trust the browser)
			eventObject.session.start = $.fullCalendar.moment.utc(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss').local();
			eventObject.session.id = data.id;

			// console.log(eventObject.session);
			updateCalendarEntry(eventObject, true);

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data.status, true);
		});
	});

	// The UPDATE button
	$('#modalWindows').on('click', '.update-session', function(event) {
		var modal = $(event.target).closest('.reveal-modal');
		var eventObject = modal.data('eventObject');

		// Get time from input boxes
		var starthours   = modal.find('.starthours').val();
		var startminutes = modal.find('.startminutes').val();
		eventObject.session.start.hours(starthours).minutes(startminutes);

		eventObject.session._token = window.token;

		eventObject.session.boat_id = eventObject.session.newBoat_id || eventObject.session.boat_id;

		// Format the time in a PHP readable format
		eventObject.session.start = eventObject.session.start.utc().format('YYYY-MM-DD HH:mm:ss');

		// console.log(eventObject.session);

		Sessions.updateSession(eventObject.session, function(data){
			// Sync worked, now save and update the calendar item

			// Remake the moment-object (parse as UTC, convert to local to work with)
			// TODO Change local() to "setTimezone" (set to user's profile timezone) (don't trust the browser)
			eventObject.session.start = $.fullCalendar.moment.utc(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss').local();

			updateCalendarEntry(eventObject);
			// console.log(data.status + '|' + data.id);

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data.status, true);
		});

	});

	// The DELETE button
	$('#modalWindows').on('click', '.delete-session', function(event) {
		var modal = $(event.target).closest('.reveal-modal');
		var eventObject = modal.data('eventObject');

		eventObject.session._token = window.token;

		// console.log(eventObject.session);

		Sessions.deleteSession({
			'id': eventObject.session.id,
			'_token': eventObject.session._token
		}, function success(data) {

			$('#calendar').fullCalendar('removeEvents', eventObject.id);

			// Unset eventObject
			delete eventObject;

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data.status, true);
		}, function error(xhr) {
			if(xhr.status == 409) {
				message = 'ATTENTION:\n\nThis session has already been booked. Do you want to deactivate it instead, so it can not be booked anymore?';
				question = confirm(message);
				if( question ) {
					// Deactivate
					Sessions.deactivateSession({
						'id': eventObject.session.id,
						'_token': eventObject.session._token
					}, function success(data) {

						eventObject.session.deleted_at = true;

						updateCalendarEntry(eventObject);

						pageMssg(data.status, true);

						// TODO Hack!
						window.location.reload();
					});
				}
				else {
					// do nothing
				}
			}
			else {
				pageMssg(data.errors[0]);
			}
		});
	});
});

function createCalendarEntry(eventObject) {

	eventObject.start = eventObject.session.start;
	eventObject.end   = $.fullCalendar.moment(eventObject.start).add('hours', eventObject.trip.duration);
	eventObject.id    = randomString();
	eventObject.backgroundColor = reproColor( eventObject.session.boat_id ).bgcolor;
	eventObject.textColor       = reproColor( eventObject.session.boat_id ).txtcolor;
	if(eventObject.session.deleted_at) {
		eventObject.backgroundColor = colorOpacity(eventObject.backgroundColor, 0.1);
		eventObject.textColor = colorOpacity(eventObject.textColor, 0.1);
	}

	// Render the event on the calendar
	// The last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
	$('#calendar').fullCalendar('renderEvent', eventObject, true);
}

function updateCalendarEntry(eventObject, redraw) {

	eventObject.start = eventObject.session.start;
	eventObject.end   = $.fullCalendar.moment(eventObject.start).add('hours', eventObject.trip.duration);

	eventObject.backgroundColor = reproColor( eventObject.session.boat_id ).bgcolor;
	eventObject.textColor       = reproColor( eventObject.session.boat_id ).txtcolor;
	if(eventObject.session.deleted_at) {
		eventObject.backgroundColor = colorOpacity(eventObject.backgroundColor, 0.1);
		eventObject.textColor = colorOpacity(eventObject.textColor, 0.1);
	}
	// $('#calendar').fullCalendar('updateEvent', eventObject);

	// debugger;

	// Because f***ing updateEvent doesn't work, we need to remove and render it again
	if(redraw === true) {
		$('#calendar').fullCalendar('removeEvents', eventObject.id);
		$('#calendar').fullCalendar('renderEvent', eventObject, true);
	}
	else {
		$('#calendar').fullCalendar('rerenderEvents');
	}
}

function showModalWindow(eventObject) {
	// Create the modal window from session-template
	var sessionTemplate = $("#session-template").html();
	sessionTemplate     = Handlebars.compile(sessionTemplate);

	eventObject.boats = $.extend(true, {}, window.boats);
	// console.log(eventObject.session);
	if(!eventObject.session.boat_id) {
		// Set default
		eventObject.session.boat_id = _.values(eventObject.boats)[0].id;
	}
	eventObject.boats[ eventObject.session.boat_id ].selected = true;

	// console.log(eventObject);

	$('#modalWindows')
	.append( sessionTemplate(eventObject) )        // Create the modal
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
