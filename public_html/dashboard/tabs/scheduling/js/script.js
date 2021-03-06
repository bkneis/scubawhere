
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1 && (!window.tourStart))
{
	window.location.href = '#dashboard';
}

if(JSON.parse(localStorage.getItem('finishedTour')))
{
	console.log('test', JSON.parse(localStorage.getItem('finishedTour')));
	$('#completed-tour-modal').modal('show');
	localStorage.setItem('finishedTour', 'false');
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

var timetableWeek;

$(function() {

	// Render a list of trips
	var tripsTemplate = $("#trip-list").html();
	tripsTemplate     = Handlebars.compile(tripsTemplate);

	var classesTemplate = $("#class-list").html();
	classesTemplate     = Handlebars.compile(classesTemplate);

	timetableWeek = $('#timetable-week-template').html();
	timetableWeek = Handlebars.compile(timetableWeek);

	Handlebars.registerHelper('timetableWeek', function(week) {
		return new Handlebars.SafeString( timetableWeek( {'week': week} ) );
	});

	window.trips     = {};
	window.boats     = {};
	window.sessions  = {};
	window.trainings = {};

	window.promises.loadedTrips = $.Deferred();
	Trip.getAllWithTrashed(function(data) { // async
		window.trips = _.indexBy(data, 'id');
		$('#trip-class-list').append(tripsTemplate({trips: extractActive(data)}));
		window.promises.loadedTrips.resolve();
	});

	window.promises.loadedClasses = $.Deferred();
	Class.getAllWithTrashed(function(data) {
		window.trainings = _.indexBy(data, 'id');
		window.promises.loadedClasses.resolve();
	});

	window.promises.loadedBoats = $.Deferred();
	Boat.getAllWithTrashed(function(data) {
		window.boats = _.indexBy(data, 'id');
		window.promises.loadedBoats.resolve();
	});

	getToken();

	$.when(
		window.promises.loadedTrips,
		window.promises.loadedClasses,
		window.promises.loadedBoats
	).done(function() {
		initDraggables();
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
				isNew   : true,
				durationEditable: false,
				startEditable: true
			};
			if($(this).attr('data-type') == "trip") {
				$.extend(eventObject, {
					trip    : window.trips[ $(this).attr('data-id') ],
					session : {
						trip_id: $(this).attr('data-id'),
					},
					eventType : "trip",
					isTrip : true
				});
			}
			else {
				$.extend(eventObject, {
					trip    : window.trainings[ $(this).attr('data-id') ],
					session : {
						training_id: $(this).attr('data-id'),
					},
					eventType : "class",
					isTrip : false
				});
			}

			// Store the eventObject in the DOM element so we can get it back later
			$(this).data('eventObject', eventObject);

			// Make the event draggable using jQuery UI
			$(this).draggable({
				zIndex: 999,
				revert: true,      // Causes the event to go back to its original position after the drag
				revertDuration: 0,
				helper: "clone",
				containment: "document",
				appendTo: 'body',
				scroll: true,
				scrollSensitivity: 50, // Pixels from the edge of the screen that start scrolling, default: 20
				scrollSpeed: 10, // Number of pixels that the page scrolls when in scroll-area, default: 20
			});
		});
	}

	/* Initialize the calendar
	--------------------------*/
	$('#calendar').fullCalendar({
		header: {
            left: 'basicDay basicWeek month',
			center: 'title',
		},
		defaultView : 'basicWeek',
		timezone: false,
		firstDay: 1, // Set Monday as the first day of the week
		eventConstraint : {
			dow : [1, 2, 3, 4, 5, 6, 7],
			businessHours : {
				start : '00:00',
				end   : '24:00'
			}
		},
		events: function(start, end, timezone, callback) {

			// Start loading indicator
			$('.fc-center h2').after('<div id="fetch-events-loader" class="loader"></div>');

			var events = [];
			window.promises.sessionFilterLoaded = $.Deferred();
			window.promises.classFilterLoaded   = $.Deferred();

			$.when(window.promises.loadedClasses, window.promises.loadedTrips).done(function() {
				Class.filter({
					'after': start.format(),
					'before': end.format(),
					'with_full': 1
				}, function success(data) {
					window.trainingSessions = _.indexBy(data, 'id');

					// Create eventObjects
					_.each(window.trainingSessions, function(value) {
						var eventObject = {
							title: window.trainings[ value.training_id ].name, // use the element's text as the event title
							allDay: false,
							trip: window.trainings[ value.training_id ], // actually is training, used for less repetive of handlebars
							session: value,
							isNew: false,
							editable: value.timetable_id ? false : true, // This uses a 'falsy' check on purpose
							durationEditable: false,
							className: value.schedule_id ? 'timetabled' : '', // This uses a 'falsy' check on purpose,
							isTrip : false
						};

						eventObject.session.start = $.fullCalendar.moment(value.start);

						events.push( createCalendarEntry(eventObject) );
					});

					window.promises.classFilterLoaded.resolve();
				});

				Session.filter({
					'after': start.format(),
					'before': end.format(),
					'with_full': 1
				}, function success(data) {
					window.sessions = _.indexBy(data, 'id');

					// Create eventObjects
					_.each(window.sessions, function(value) {
						var eventObject = {
							title: window.trips[ value.trip_id ].name, // use the element's text as the event title
							allDay: false,
							trip: window.trips[ value.trip_id ],
							session: value,
							isNew: false,
							editable: value.timetable_id ? false : true, // This uses a 'falsy' check on purpose
							durationEditable: false,
							className: value.timetable_id ? 'timetabled' : '', // This uses a 'falsy' check on purpose
							isTrip : true
						};

						eventObject.session.start = $.fullCalendar.moment(value.start);

						events.push( createCalendarEntry(eventObject) );
					});

					window.promises.sessionFilterLoaded.resolve();
				});
			});

			// Wait for the requests to load and then return events
			$.when(
				window.promises.sessionFilterLoaded,
				window.promises.classFilterLoaded
			).done(function() {
				// Remove loading indictor
				$('#fetch-events-loader').remove();

				callback(events);
			});
		},
		eventRender: function(event, element) {
			// Intercept the event rendering to inject the non-html-escaped version of the title
			// Needed for trip names with special characters in it (like ó, à, etc.)
			element.find('.fc-title').html(event.title);
		},
		eventAfterRender : function(event, element) {
			$(element).css('height', '25px');
			$(element).css('text-align', 'center');
			if ($('#calendar').fullCalendar('getView').name === 'month') {
				var weekContainer = $(element).closest('.fc-week').first();
				var totalHeight = weekContainer.children('.fc-content-skeleton').children('table').first().height();
				if (totalHeight > 100) {
					weekContainer.height(totalHeight);
				}
			}
		},
		editable: true,
		droppable: true, // This allows things to be dropped onto the calendar
		drop: function(date) { // This function is called when something is dropped

			// Check if the dropped-on date is in the past
			/*if( moment().startOf('day').diff(date) > 0 ) {
				pageMssg('Sessions cannot be created in the past.', 'info');
				return false;
			}*/

			// Retrieve the dropped element's stored Event Object
			var originalEventObject = $(this).data('eventObject');

			// We need to copy it, so that multiple events don't have a reference to the same object
			var eventObject = $.extend(true, {}, originalEventObject);
			// delete eventObject.session.boat_id;

			eventObject.session.start = date.add(9, 'hours'); // Sets default start time to 09:00

			eventObject = createCalendarEntry(eventObject);

			$('#calendar').fullCalendar('renderEvent', eventObject, true);

			showModalWindow(eventObject);
		},
		eventDrop: function(eventObject, delta, revertFunc) {
			if(!eventObject.start.hasTime()) {
				// Combine dropped-on date and session's start time
				eventObject.start.time(eventObject.session.start.format('HH:mm:ss'));
			}
			eventObject.session.start = eventObject.start;

			eventObject.session._token = window.token;

			// Format the time in a PHP readable format
			eventObject.session.start = eventObject.session.start.format('YYYY-MM-DD HH:mm:ss');

			// console.log(eventObject.session);

			if(eventObject.isTrip) {
				Session.updateSession(eventObject.session, function success(data){
					// Sync worked, now save and update the calendar item

					// Remake the moment-object
					eventObject.session.start = $.fullCalendar.moment(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss');

					updateCalendarEntry(eventObject);
					// console.log(data.status + '|' + data.id);

					// Close modal window
					$('#modalWindows .close-reveal-modal').click();

					pageMssg(data.status, true);
				},
				function error(xhr) {
					revertFunc();

					eventObject.session.start = $.fullCalendar.moment(eventObject.start.format('YYYY-MM-DD HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss');

					updateCalendarEntry(eventObject);

					var data = JSON.parse(xhr.responseText);
					pageMssg(data.errors[0], 'warning');
				});
			}
			else {
				Class.updateSession(eventObject.session, function success(data){
					// Sync worked, now save and update the calendar item

					// Remake the moment-object
					eventObject.session.start = $.fullCalendar.moment(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss');

					updateCalendarEntry(eventObject);
					// console.log(data.status + '|' + data.id);

					// Close modal window
					$('#modalWindows .close-reveal-modal').click();

					pageMssg(data.status, true);
				},
				function error(xhr) {
					revertFunc();

					eventObject.session.start = $.fullCalendar.moment(eventObject.start.format('YYYY-MM-DD HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss');

					updateCalendarEntry(eventObject);

					var data = JSON.parse(xhr.responseText);
					pageMssg(data.errors[0], 'warning');
				});
			}

		},
		eventClick: function(eventObject) {
			showModalWindow(eventObject);
		},
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

		eventObject.newStart = newStart;

		// console.log(eventObject.session.start.format('HH:mm on DD-MM-YYYY'));

		// Update displayed end datetime
		var newEnd = $.fullCalendar.moment(newStart).add(eventObject.trip.duration, 'hours');
		eventObject.newEnd = newEnd;
		$(event.target)
		.closest('.reveal-modal')
		.find('.enddatetime')
		.text( newEnd.format('HH:mm on DD-MM-YYYY') );
		delete newStart;
		delete newEnd;
	});

	// Finally, the ACTIVATE button
	$('#modalWindows').on('click', '.submit-session', function(event) {

		// Disable button and display loader
		$(event.target).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		var modal = $(event.target).closest('.reveal-modal');
		var eventObject = modal.data('eventObject');

		// Get time from input boxes
		var starthours   = modal.find('.starthours').val();
		var startminutes = modal.find('.startminutes').val();
		eventObject.session.start.hours(starthours).minutes(startminutes);

		// console.log(eventObject.session.start.format('YYYY-MM-DD HH:mm:ss'));

		eventObject.session._token = window.token;

		eventObject.session.boat_id = modal.find('[name=boat_id]').val();

		// Format the time in a PHP readable format
		eventObject.session.start = eventObject.session.start.format('YYYY-MM-DD HH:mm:ss');

		if(eventObject.isTrip) {
			Session.createSession(eventObject.session, function success(data) {

				// Whenever the session is saved, it is not new anymore
				eventObject.isNew = false;

				// Communicate success to user
				$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
				$('#save-loader').remove();

				// Remake the moment-object
				eventObject.session.start = $.fullCalendar.moment(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss');
				eventObject.session.id = data.id;

				// console.log(eventObject.session);
				$('#calendar').fullCalendar('removeEvents', eventObject.id);
				$('#calendar').fullCalendar('refetchEvents');

				// Close modal window
				$('#modalWindows .close-reveal-modal').click();

				pageMssg(data.status, true);
			},
			function error(xhr) {
				var data = JSON.parse(xhr.responseText);
				console.log(data);

				pageMssg(data.errors[0]);

				// Remake the moment-object
				eventObject.session.start = $.fullCalendar.moment(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss');

				// Communicate error to user
				$(event.target).prop('disabled', false);
				$('#save-loader').remove();

				// Trigger boatroomWarning
				$('#modalWindows .boatSelect').change();
			});
		}
		else {

			Class.createSession(eventObject.session, function success(data) {
				// Whenever the session is saved, it is not new anymore
				eventObject.isNew = false;

				// Communicate success to user
				$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
				$('#save-loader').remove();

				// Remake the moment-object
				eventObject.session.start = $.fullCalendar.moment(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss');
				eventObject.session.id = data.id;

				// console.log(eventObject.session);
				$('#calendar').fullCalendar('removeEvents', eventObject.id);
				$('#calendar').fullCalendar('refetchEvents');

				// Close modal window
				$('#modalWindows .close-reveal-modal').click();

				pageMssg(data.status, true);
			},
			function error(xhr) {
				var data = JSON.parse(xhr.responseText);
				console.log(data);

				pageMssg(data.errors[0]);

				// Remake the moment-object
				eventObject.session.start = $.fullCalendar.moment(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss');

				// Communicate error to user
				$(event.target).prop('disabled', false);
				$('#save-loader').remove();

				// Trigger boatroomWarning
				$('#modalWindows .boatSelect').change();
			});
		}
	});

	// The UPDATE button
	$('#modalWindows').on('click', '.update-session', function success(event) {

		var modal = $(event.target).closest('.reveal-modal');
		var eventObject = modal.data('eventObject');

		// Get time from input boxes
		var starthours   = modal.find('.starthours').val();
		var startminutes = modal.find('.startminutes').val();

		// Check if every necessary info is supplied
		if( eventObject.session.timetable_id && ( starthours != eventObject.start.hours() || startminutes != eventObject.start.minutes() ) && $('[name=handle_timetable]:checked').length === 0) {

			$('.attention-placeholder').removeClass('border-blink');

			// Direct chaining didn't work. Thus a timeout hack...
			setTimeout(function() {
				$('.attention-placeholder').addClass('border-blink');
			}, 10);

			return false;
		}
		else {
			// Add mandatory parameter to request payload
			eventObject.session.handle_timetable = $('[name=handle_timetable]:checked').val();
		}

		// Disable button and display loader
		$(event.target).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		// Write new time into session object
		eventObject.session.start.hours(starthours).minutes(startminutes);

		eventObject.session._token = window.token;

		eventObject.session.boat_id = modal.find('[name=boat_id]').val();

		// Format the time in a PHP readable format
		eventObject.session.start = eventObject.session.start.format('YYYY-MM-DD HH:mm:ss');

		if(eventObject.isTrip) {
			Session.updateSession(eventObject.session, function success(data) {

			// Communicate success to user
			$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
			$('#save-loader').remove();

			// Remove extra payload parameter from eventObject so it doesn't automatically transfer over to the next request
			delete eventObject.session.handle_timetable;

			// Remake the moment-object
			eventObject.session.start = $.fullCalendar.moment(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss');

			// updateCalendarEntry(eventObject);
			$('#calendar').fullCalendar('refetchEvents');

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data.status, true);
		},
		function error(xhr) {
				// Remove extra payload parameter from eventObject so it doesn't automatically transfer over to the next request
				delete eventObject.session.handle_timetable;

				// Remake the moment-object
				eventObject.session.start = $.fullCalendar.moment(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss');

				var data = JSON.parse(xhr.responseText);
				console.log(data);

				_.each(data.errors, function(error) {
					pageMssg(error);
				});

				// Communicate error to user
				$(event.target).prop('disabled', false);
				$('#save-loader').remove();

				// Trigger boatroomWarning
				$('#modalWindows .boatSelect').change();
			});
}
else {
	Class.updateSession(eventObject.session, function success(data) {

			// Communicate success to user
			$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
			$('#save-loader').remove();

			// Remove extra payload parameter from eventObject so it doesn't automatically transfer over to the next request
			delete eventObject.session.handle_timetable;

			// Remake the moment-object
			eventObject.session.start = $.fullCalendar.moment(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss');

			// updateCalendarEntry(eventObject);
			$('#calendar').fullCalendar('refetchEvents');

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data.status, true);
		},
		function error(xhr) {
				// Remove extra payload parameter from eventObject so it doesn't automatically transfer over to the next request
				delete eventObject.session.handle_timetable;

				// Remake the moment-object
				eventObject.session.start = $.fullCalendar.moment(eventObject.session.start, 'YYYY-MM-DD HH:mm:ss');

				var data = JSON.parse(xhr.responseText);
				console.log(data);

				_.each(data.errors, function(error) {
					pageMssg(error);
				});

				// Communicate error to user
				$(event.target).prop('disabled', false);
				$('#save-loader').remove();

				// Trigger boatroomWarning
				$('#modalWindows .boatSelect').change();
			});
}


});

	// The RESTORE button
	$('#modalWindows').on('click', '.restore-session', function(event) {

		var modal = $(event.target).closest('.reveal-modal');
		var eventObject = modal.data('eventObject');

		// Disable button and display loader
		$(event.target).prop('disabled', true).after('<div id="save-loader" class="loader" style="float: left;"></div>');

		eventObject.session._token = window.token;

		if(eventObject.isTrip) {
			Session.restoreSession({
				'id'              : eventObject.session.id,
				'_token'          : eventObject.session._token
			}, function success(data) {

			// Communicate success to user
			$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
			$('#save-loader').remove();

			$('#calendar').fullCalendar('refetchEvents');

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data.status, true);
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.errors[0]);
			$(event.target).prop('disabled', false);
			$('#save-loader').remove();
		});
		}
		else {
			Class.restoreSession({
				'id'              : eventObject.session.id,
				'_token'          : eventObject.session._token
			},
			function success(data) {
				// Communicate success to user
				$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
				$('#save-loader').remove();

				$('#calendar').fullCalendar('refetchEvents');

				// Close modal window
				$('#modalWindows .close-reveal-modal').click();

				pageMssg(data.status, true);
			},
			function error(xhr) {
				var data = JSON.parse(xhr.responseText);
				pageMssg(data.errors[0]);
				$(event.target).prop('disabled', false);
				$('#save-loader').remove();
			});
		}

	});

	// The DELETE button
	$('#modalWindows').on('click', '.delete-session', function(event) {

		var modal = $(event.target).closest('.reveal-modal');
		var eventObject = modal.data('eventObject');

		// Check if every necessary info is supplied
		if( eventObject.session.timetable_id && $('[name=handle_timetable]:checked').length === 0) {

			$('.attention-placeholder').removeClass('border-blink');

			// Direct chaining didn't work. Thus a timeout hack...
			setTimeout(function() {
				$('.attention-placeholder').addClass('border-blink');
			}, 10);

			return false;
		}
		else {
			// Add mandatory parameter to request payload
			eventObject.session.handle_timetable = $('[name=handle_timetable]:checked').val();
		}

		// Disable button and display loader
		$(event.target).prop('disabled', true).after('<div id="save-loader" class="loader" style="float: left;"></div>');

		eventObject.session._token = window.token;

		// console.log(eventObject.session);

		if(eventObject.isTrip) {
			Session.deleteSession({
				'id'              : eventObject.session.id,
				'_token'          : eventObject.session._token,
				'handle_timetable': eventObject.session.handle_timetable
			}, function success(data) {

				// Communitcate success to user
				$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
				$('#save-loader').remove();

				if(eventObject.session.handle_timetable === 'following')
					$('#calendar').fullCalendar('refetchEvents');
				else
					$('#calendar').fullCalendar('removeEvents', eventObject.id);

				// Unset eventObject
				delete eventObject;

				// Close modal window
				$('#modalWindows .close-reveal-modal').click();

				pageMssg(data.status, true);
			}, function error(xhr) {
				if(xhr.status == 409 && !eventObject.session.deleted_at) {
					var message = 'ATTENTION:\n\nThis session has already been booked. Do you want to deactivate it instead, so it can not be booked anymore?';
					var question = confirm(message);
					if( question ) {
						// Deactivate
						Session.deactivateSession({
							'id': eventObject.session.id,
							'_token': eventObject.session._token,
							'handle_timetable': 'only_this'
						}, function success(data) {

							// Communitcate success to user
							$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
							$('#save-loader').remove();

							eventObject.session.deleted_at = true;

							updateCalendarEntry(eventObject);

							pageMssg(data.status, true);

							// Close modal window
							$('#modalWindows .close-reveal-modal').click();

							// TODO Hack!
							// window.location.reload();
						});
					}
					else {
						// do nothing
					}
				}
				else {
					var data = JSON.parse(xhr.responseText);
					pageMssg(data.errors[0]);
					$(event.target).prop('disabled', false);
					$('#save-loader').remove();
				}
			});
}
else {
	Class.deleteSession({
		'id'              : eventObject.session.id,
		'_token'          : eventObject.session._token,
		'handle_timetable': eventObject.session.handle_timetable
	}, function success(data) {

				// Communitcate success to user
				$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
				$('#save-loader').remove();

				if(eventObject.session.handle_timetable === 'following')
					$('#calendar').fullCalendar('refetchEvents');
				else
					$('#calendar').fullCalendar('removeEvents', eventObject.id);

				// Unset eventObject
				delete eventObject;

				// Close modal window
				$('#modalWindows .close-reveal-modal').click();

				pageMssg(data.status, true);
			}, function error(xhr) {
				if(xhr.status == 409 && !eventObject.session.deleted_at) {
					var message = 'ATTENTION:\n\nThis session has already been booked. Do you want to deactivate it instead, so it can not be booked anymore?';
					var question = confirm(message);
					if( question ) {
						// Deactivate
						Session.deactivateSession({
							'id': eventObject.session.id,
							'_token': eventObject.session._token,
							'handle_timetable': 'only_this'
						}, function success(data) {

							// Communitcate success to user
							$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
							$('#save-loader').remove();

							eventObject.session.deleted_at = true;

							updateCalendarEntry(eventObject);

							pageMssg(data.status, true);

							// Close modal window
							$('#modalWindows .close-reveal-modal').click();

							// TODO Hack!
							// window.location.reload();
						});
					}
					else {
						// do nothing
					}
				}
				else {
					var data = JSON.parse(xhr.responseText);
					pageMssg(data.errors[0]);
					$(event.target).prop('disabled', false);
					$('#save-loader').remove();
				}
			});
}

});

	// The CREATE TIMETABLE button
	$('#modalWindows').on('click', '.create-timetable-button', function(event) {
		event.preventDefault();
		var $form = $(event.target).closest('form');

		if($(this).attr("data-type") == "trips") {
			Timetable.createTimetable($form.serialize(), function success(data) {
					pageMssg(data.status, true);

				// Remove original session
				var eventObject = $form.closest('.reveal-modal').data('eventObject');
				$('#calendar').fullCalendar('removeEvents', eventObject.id);

				// Close modal window
				$('#modalWindows .close-reveal-modal').click();

				$('#calendar').fullCalendar('refetchEvents');
			},
			function error(xhr) {
				console.log(xhr);
				var data = JSON.parse(xhr.responseText);
				pageMssg(data.errors[0], 'danger');
			});
		}
		else {
			Timetable.createClassTimetable($form.serialize(),function success(data) {
					pageMssg(data.status, true);

				// Remove original session
				var eventObject = $form.closest('.reveal-modal').data('eventObject');
				$('#calendar').fullCalendar('removeEvents', eventObject.id);

				// Close modal window
				$('#modalWindows .close-reveal-modal').click();

				$('#calendar').fullCalendar('refetchEvents');
			},
			function error(xhr) {
				console.log(xhr);
			});
		}

	});

	$('#modalWindows').on('change', '.boatSelect, .starthours, .startminutes', function() {
		var $select = $('.boatSelect');
		var $button = $('.submit-session, .update-session');
		$select.siblings('.boatroomWarning').remove();
		$button.prop('disabled', false);

		var eventObject = $select.closest('.reveal-modal').data('eventObject');
		var start = eventObject.newStart || eventObject.start;
		var end   = eventObject.newEnd   || eventObject.end;
		if(start.format('YYYY-MM-DD') !== end.format('YYYY-MM-DD'))
		{
			var boat_id = $select.val();
			var boatroomCount = window.boats[boat_id].boatrooms.length;
			if(boatroomCount === 0) {
				// Display warning
				$select.after(' <span class="boatroomWarning text-danger"><i class="fa fa-exclamation-triangle fa-lg"></i><br>Without boatrooms, this boat cannot be selected for an overnight trip.</span>');
				$button.prop('disabled', true);
			}
		}
	});

	$("#filter-types").on('click', '.filter-type', function() {
		$(".filter-type").removeClass("btn-primary");
		display = $(this).attr("display");
		console.log(display);
		$("#filter-"+display).addClass("btn-primary");
		if(display == "classes") $('#trip-class-list').html(classesTemplate({classes: extractActive(window.trainings)}));
		else $('#trip-class-list').html(tripsTemplate({trips: extractActive(window.trips)}));
		initDraggables();
	});

	$("#jump-to-date").on('change', '#jump-date', function(event) {
		event.preventDefault();
		var date = $("#jump-date").val();
		var jumpDate = $.fullCalendar.moment(date);
		$("#calendar").fullCalendar('gotoDate', jumpDate);
		$("#remove-jump").css('display', 'inline');
	});

	$('input.datepicker').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up: 'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
		clearBtn: true
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
	return eventObject;

	// Render the event on the calendar
	// The last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
	// $('#calendar').fullCalendar('renderEvent', eventObject, true);
}

function updateCalendarEntry(eventObject, redraw) {

	eventObject.start = eventObject.session.start;
	eventObject.end   = $.fullCalendar.moment(eventObject.start).add(eventObject.trip.duration, 'hours');

	eventObject.color = reproColor( eventObject.session.boat_id ).bgcolor;
	eventObject.textColor       = reproColor( eventObject.session.boat_id ).txtcolor;
	if(eventObject.session.deleted_at) {
		eventObject.color = colorOpacity(eventObject.color, 0.2);
		if( eventObject.textColor == '#000000') // black
			eventObject.textColor = colorOpacity(eventObject.textColor, 0.3);
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
	if(!window.sw.sessionTemplate) window.sw.sessionTemplate = Handlebars.compile( $("#session-template").html() );

	eventObject.boats = $.extend(true, {}, window.boats);
	// console.log(eventObject.session);
	if(eventObject.session.boat_id) {
		eventObject.boats[ eventObject.session.boat_id ].selected = true;
	}

	// Check if session lies in the past
	if(typeof eventObject.isPast === 'undefined')
		/*if( (eventObject.isNew && moment().startOf('day').diff(eventObject.start) > 0) || (!eventObject.isNew && moment().diff(eventObject.start) > 0) )
			eventObject.isPast = true;
		else
			eventObject.isPast = false;*/

		eventObject.isPast = false;

		$('#modalWindows')
	.append( window.sw.sessionTemplate(eventObject) )        // Create the modal
	.children('#modal-' + eventObject.id)          // Directly find it and use it
	.data('eventObject', eventObject)              // Assign the eventObject to the modal DOM element
	.reveal({                                      // Open modal window | Options:
		animation: 'fadeAndPop',                   // fade, fadeAndPop, none
		animationSpeed: 300,                       // how fast animtions are
		closeOnBackgroundClick: true,              // if you click background will modal close?
		dismissModalClass: 'close-modal',          // the class of a button or element that will close an open modal
		'eventObject': eventObject,                // Submit by reference to later get it as this.eventObject
		onFinishModal: function() {
			if(this.eventObject.isNew) {
				$('#calendar').fullCalendar('removeEvents', this.eventObject.id);
			}

			delete this.eventObject.newStart;
			delete this.eventObject.newEnd;

			$('#modal-' + this.eventObject.id).remove();
		},
	});

	// Trigger boatroomWarning
	$('#modalWindows .boatSelect').change();

	// Set timetable form token
	getToken(function(token) {
		$('#modalWindows [name="_token"]').val(token);
	});
}

function toggleWeek(self) {
	var $self    = $(self);
	var $tr      = $self.closest('tr');
	var disabled = !$self.is(':checked');
	// console.log(disabled);

	// First, set the clicked week accordingly
	$tr.find('.day_selector').prop('disabled', disabled);

	if(disabled) {
		// When the week has been disabled, remove all following weeks from the timetable
		$tr.nextAll().remove();
	}
	else {
		// When the week has been enabled, add the following week (disabled by default)
		$tr.after( timetableWeek(  {'week': $self.data('week') * 1 + 1} ) );
	}
}

function toggleTimetableForm() {
	$('.create-timetable').toggle();
}

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

function extractActive(data) {
	return _.filter(data, function (obj) { return obj.deleted_at === null; });
}
