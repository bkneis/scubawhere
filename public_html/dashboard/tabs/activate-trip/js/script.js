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
			_.each(window.sessions, function(value, key, list) {
				var eventObject = {
					title   : window.trips[ value.trip_id ].name, // use the element's text as the event title
					allDay  : false,
					id      : randomString(),
					trip    : window.trips[ value.trip_id ],
					session : value,
					isNew   : false,
				};
				eventObject.session.start = moment(value.start);

				createCalenderEntry(eventObject);
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
		events: [],
		editable: false,
		droppable: true, // This allows things to be dropped onto the calendar
		drop: function(date) { // This function is called when something is dropped

			// Retrieve the dropped element's stored Event Object
			var originalEventObject = $(this).data('eventObject');

			// We need to copy it, so that multiple events don't have a reference to the same object
			var eventObject = $.extend({}, originalEventObject);

			// Assign it the date that it was dropped on
			eventObject.session.start = moment(date); // 12am midnight

			createCalenderEntry(eventObject);

			showModalWindow(eventObject);
		},
		eventClick: function(eventObject) {
			console.log(eventObject);
			showModalWindow(eventObject);
		},
	});

	/* HACK	*/
	setTimeout(function() {
		$('#calendar').fullCalendar( 'today' );
	}, 100);

	$('#modalWindows').on('change', '.boatSelect', function(event) {
		eventObject = $(event.target).closest('.reveal-modal').data('eventObject');
		eventObject.session.boat_id = event.target.value;
	});
	$('#modalWindows').on('change', '.starthours', function(event) {
		// Validation and correction
		event.target.value = parseInt(event.target.value, 10);
		if(event.target.value.length == 1) event.target.value = "0" + event.target.value;
		if(event.target.value > 24) event.target.value = 24;
		if(event.target.value < 0)  event.target.value = 0;

		eventObject = $(event.target).closest('.reveal-modal').data('eventObject');
		eventObject.session.start = eventObject.session.start.hours( event.target.value );

		// Update end datetime
		updateCalendarEntry(eventObject);
		$(event.target).closest('.reveal-modal').find('.enddatetime').text( eventObject.end.format('HH:mm on DD-MM-YYYY') );

	});
	$('#modalWindows').on('change', '.startminutes', function(event) {
		// Validation and correction
		event.target.value = parseInt(event.target.value, 10);
		if(event.target.value.length == 1) event.target.value = "0" + event.target.value;
		if(event.target.value > 60) event.target.value = 60;
		if(event.target.value < 0)  event.target.value = 0;

		eventObject = $(event.target).closest('.reveal-modal').data('eventObject');
		eventObject.session.start = eventObject.session.start.minutes( event.target.value );

		// Update end datetime
		updateCalendarEntry(eventObject);
		$(event.target).closest('.reveal-modal').find('.enddatetime').text( eventObject.end.format('HH:mm on DD-MM-YYYY') );
	});

	// Finally, the ACTIVATE button
	$('#modalWindows').on('click', '.submit-session', function(event) {
		eventObject = $(event.target).closest('.reveal-modal').data('eventObject');

		// Whenever the session is saved, it is not new anymore
		eventObject.isNew = false;

		// 1. Send the AJAX to the server
		eventObject.session._token = window.token;
		eventObject.session.start = eventObject.session.start.format('YYYY-MM-DD HH:mm:ss');
		console.log(eventObject.session);
		Sessions.createSession(eventObject.session, function(data){
			// Sync worked, now save and update the calender item
			updateCalendarEntry(eventObject);
			// Then, in absence of a good programmatic solution, just remove the modal
			$('.reveal-modal-bg, .reveal-modal').remove();
			alert("Sync successfull. Have a look in the database ;-)");
		});
	});

	// The UPDATE button
	$('#modalWindows').on('click', '.submit-session', function(event) {
		/*eventObject = $(event.target).closest('.reveal-modal').data('eventObject');

		// Whenever the session is saved, it is not new anymore
		eventObject.isNew = false;

		// 1. Send the AJAX to the server
		eventObject.session._token = window.token;
		eventObject.session.start = eventObject.session.start.format('YYYY-MM-DD HH:mm:ss');
		console.log(eventObject.session);
		Sessions.createSession(eventObject.session, function(data){
			// Sync worked, now save and update the calender item
			updateCalendarEntry(eventObject);
			// Then, in absence of a good programmatic solution, just remove the modal
			$('.reveal-modal-bg, .reveal-modal').remove();
			alert("Sync successfull. Have a look in the database ;-)");
		});
		*/
	});
});

function createCalenderEntry(eventObject) {

	eventObject.start = eventObject.session.start;
	eventObject.end = moment(eventObject.start).add('hours', eventObject.trip.duration); // just for initial display. will be overwriten next

	// checkOverlap(eventObject); // Check thi later, after a boat has been assigned

	// Render the event on the calendar
	// The last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
	$('#calendar').fullCalendar('renderEvent', eventObject, true);
}

function updateCalendarEntry(eventObject) {

	eventObject.start = eventObject.session.start = moment(eventObject.session.start);
	eventObject.end = moment(eventObject.start).add('hours', eventObject.trip.duration);
	// eventObject.eventBackgroundColor = ;

	// Can't update, so we recreate
	$('#calendar').fullCalendar('removeEvents', eventObject.id);
	$('#calendar').fullCalendar('renderEvent', eventObject, true);
}

function showModalWindow(eventObject) {
	// Create the modal window from session-template
	var sessionTemplate = $("#session-template").html();
	sessionTemplate     = Handlebars.compile(sessionTemplate);

	eventObject.boats = window.boats;
	if(eventObject.session.boat_id)
		eventObject.boats[ eventObject.session.boat_id ].selected = true;
	else
		// Set default
		eventObject.session.boat_id = eventObject.boats[ _.values(eventObject.boats)[0].id ].id;

	$('#modalWindows')
	.append( sessionTemplate(eventObject) )
	.children('#modal-' + eventObject.id)
	.data('eventObject', eventObject)
	.reveal({
		animation: 'fadeAndPop',                   // fade, fadeAndPop, none
		animationSpeed: 300,                       // how fast animtions are
		closeOnBackgroundClick: false,             // if you click background will modal close?
		dismissModalClass: 'close-reveal-modal',   // the class of a button or element that will close an open modal
		eventObject: eventObject,                  // submit by reference
		onCloseModal: function() {
			// Aborted action
			if(this.eventObject.isNew) {
				$('#calendar').fullCalendar('removeEvents', this.eventObject.id);
			}

			// Clean up the randomStrings array
			// window.randomStrings.indexOf( this.eventObject.id );
		},
		onFinishModal: function() {
			if(this.eventObject.isNew) {
				$('#modal-' + this.eventObject.id).remove();
			}
		},
	});
}

Handlebars.registerHelper('date', function(datetime) {
	return moment(datetime).format('DD-MM-YYYY');
});
Handlebars.registerHelper('hours', function(datetime) {
	return moment(datetime).format('HH');
});
Handlebars.registerHelper('minutes', function(datetime) {
	return moment(datetime).format('mm');
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
