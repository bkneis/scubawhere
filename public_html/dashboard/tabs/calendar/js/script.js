
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1)
{
	window.location.href = '#dashboard';
}

var calendarOptions = {
    filterByBoat: false,
    filterByTrip: false,
    filterByClass: false,
    filterByAccom: false,
    boatFilter: null,
    tripFilter: null,
    classFilter: null,
    accomFilter: null,
    boatsListTemplate: null,
    tripsListTemplate: null,
    filterSelectTemplate: null,
    calendarDisplay: "trips"
};

$(function() {

    window.trips = {};
    window.boats = {};
    window.sessions = {};
    window.accommodations = {};

    calendarOptions.boatsListTemplate = Handlebars.compile($("#boats-list-template").html());
    calendarOptions.tripsListTemplate = Handlebars.compile($("#trips-list-template").html());

    calendarOptions.filterSelectTemplate = Handlebars.compile($("#trip-filter-template").html());
    $("#filter-settings").empty().append(calendarOptions.filterSelectTemplate());

    // 1. Get trips
    window.promises.loadedTrips = $.Deferred();
    Trip.getAllTrips(function(data) { // async
        window.trips = _.indexBy(data, 'id');
        $("#trips-select").append(calendarOptions.tripsListTemplate({
            trips: data
        }));
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

    window.promises.loadedCountries = $.Deferred();
    $.get("/api/country/all", function success(data) {
        window.countries = _.indexBy(data, 'id');
        window.promises.loadedCountries.resolve();
    });

    window.promises.loadedCourses = $.Deferred();
    Course.getAllWithTrashed(function(data) {
        window.courses = _.indexBy(data, 'id');
        window.promises.loadedCourses.resolve();
    });

    window.promises.loadedTickets = $.Deferred();
    Ticket.getAllWithTrashed(function(data) {
        window.tickets = _.indexBy(data, 'id');
        window.promises.loadedTickets.resolve();
    });

    /* Initialize the calendar
    --------------------------*/
    $.when(
        window.promises.loadedTrips,
        window.promises.loadedClasses,
        window.promises.loadedBoats,
        window.promises.loadedAccommodations
    ).done(function() {
        $('#calendar').fullCalendar({
            header: {
                left: 'basicDay basicWeek month',
                center: 'title',
            },
            timezone: false,
            firstDay: 1, // Set Monday as the first day of the week
            events: function(start, end, timezone, callback) {
                if (calendarOptions.calendarDisplay == "all") {
                    /*getTripEvents(start, end, timezone, callback);
                    getClassEvents(start, end, timezone, callback);
                    getAccomEvents(start, end, timezone, callback);*/
                    $("#filter-settings").empty();
                    getAllEvents(start, end, timezone, callback);
                    console.log('all');
                }
                if (calendarOptions.calendarDisplay == "trips")
                    getTripEvents(start, end, timezone, callback);
                if (calendarOptions.calendarDisplay == "accommodations")
                    getAccomEvents(start, end, timezone, callback);
                if (calendarOptions.calendarDisplay == "classes")
                    getClassEvents(start, end, timezone, callback);
            },
            eventRender: function(event, element) {
                // Intercept the event rendering to inject the non-html-escaped version of the title
                // Needed for trip names with special characters in it (like ó, à, etc.)
                element.find('.fc-title').html(event.title);
            },
            editable: false,
            droppable: false, // This allows things to be dropped onto the calendar
            eventClick: function(eventObject) {

                if (eventObject.type == "trip")
                    showModalWindow(eventObject);
                if (eventObject.type == "class")
                    showModalWindowCourse(eventObject);
                if (eventObject.type == "accom")
                    showModalWindowAccommodation(eventObject);
                /*if(calendarOptions.calendarDisplay == "trips")
                    showModalWindow(eventObject);
                if(calendarOptions.calendarDisplay == "accommodations")
                    showModalWindowAccommodation(eventObject);
                if(calendarOptions.calendarDisplay == "classes")
                    showModalWindowCourse(eventObject);*/
                //console.log(calendarOptions.calendarDisplay);
                //console.log(eventObject);
            },
        });
    });

    $("#filter-types").on('click', '.filter-type', function(event) {
        event.preventDefault();
        $("#filter-" + calendarOptions.calendarDisplay).removeClass("btn-primary");
        calendarOptions.calendarDisplay = $(this).attr("display");

        if (calendarOptions.calendarDisplay == "trips") {
            calendarOptions.filterSelectTemplate = Handlebars.compile($("#trip-filter-template").html());
            $("#filter-settings").empty().append(calendarOptions.filterSelectTemplate());
        } else if (calendarOptions.calendarDisplay == "classes") {
            $('#filter').empty();
            calendarOptions.filterSelectTemplate = Handlebars.compile($("#class-list-template").html());
            $("#filter-settings").empty().append(calendarOptions.filterSelectTemplate({
                classes: window.trainings
            }));
        } else {
            $('#filter').empty();
            calendarOptions.filterSelectTemplate = Handlebars.compile($("#accom-list-template").html());
            $("#filter-settings").empty().append(calendarOptions.filterSelectTemplate({
                accoms: window.accommodations
            }));
        }
        calendarOptions.filterByBoat = false;
        calendarOptions.filterByTrip = false;
        calendarOptions.filterByAccom = false;
        calendarOptions.filterByClass = false;
        $('#calendar').fullCalendar('refetchEvents');
        $("#filter-" + calendarOptions.calendarDisplay).addClass("btn-primary");
    });

    /*$('#filter-options').on('change', function(event) {
        event.preventDefault();
        if($("#filter-options").val() == 'boat') {
            $("div#filter-settings option[value=boat]").attr('disabled', true);
            $("#filter-options").val('all');
            $("#filter").append( calendarOptions.boatsListTemplate({boats : window.boats}) );
        }
        else if($("#filter-options").val() == 'trip') {
            $("div#filter-settings option[value=trip]").attr('disabled', true);
            $("#filter-options").val('all');
            $("#filter").append( calendarOptions.tripsListTemplate({trips : window.trips}) );
        }
    });*/

    $("#filter").on('change', '.filter', function(event) {
        event.preventDefault();
        //console.log(this.options[this.selectedIndex].value);
        console.log(filter);
        if (this.id == "boats") {
            var filter = $("#boats option:selected").val();
            if (filter == "all") calendarOptions.filterByBoat = false;
            else calendarOptions.filterByBoat = true;
            //calendarOptions.filterByBoat = true;
            calendarOptions.boatFilter = this.options[this.selectedIndex].value;
            //console.log(calendarOptions.boatFilter);
        } else if (this.id == "trips") {
            var filter = $("#trips option:selected").val();
            if (filter == "all") calendarOptions.filterByTrip = false;
            else calendarOptions.filterByTrip = true;
            calendarOptions.filterByTrip = true;
            calendarOptions.tripFilter = this.options[this.selectedIndex].value;
            console.log("trip filter =  ", calendarOptions.tripFilter);
        }
        $('#calendar').fullCalendar('refetchEvents');
    });

    $("#filter-settings").on('change', '.filter', function(event) {
        event.preventDefault();
        if (this.id == "accoms") {
            var filter = $("#accoms option:selected").val();
            console.log(filter);
            if (filter == "all") calendarOptions.filterByAccom = false;
            else calendarOptions.filterByAccom = true;
            //calendarOptions.filterByBoat = true;
            calendarOptions.accomFilter = this.options[this.selectedIndex].value;
        } else {
            var filter = $("#classes option:selected").val();
            if (filter == "all") calendarOptions.filterByClass = false;
            else calendarOptions.filterByClass = true;
            //calendarOptions.filterByBoat = true;
            calendarOptions.classFilter = this.options[this.selectedIndex].value;
        }
        $('#calendar').fullCalendar('refetchEvents');
    });

    $("#filters").on('click', '#remove-boats-filter', function(event) {
        event.preventDefault();
        calendarOptions.filterByBoat = false;
        calendarOptions.boatFilter = null;
        $("div#filter-settings option[value=boat]").attr('disabled', false);
        $(event.target).parent().remove();
        $('#calendar').fullCalendar('refetchEvents');
    });

    $("#filters").on('click', '#remove-trips-filter', function(event) {
        event.preventDefault();
        calendarOptions.filterByTrip = false;
        calendarOptions.tripFilter = null;
        $("div#filter-settings option[value=trip]").attr('disabled', false);
        $(event.target).parent().remove();
        $('#calendar').fullCalendar('refetchEvents');
    });

    $("#jump-to-date").on('change', '#jump-date', function(event) {
        event.preventDefault();
        var date = $("#jump-date").val();
        var jumpDate = $.fullCalendar.moment(date);
        $("#calendar").fullCalendar('gotoDate', jumpDate);
        $("#remove-jump").css('display', 'inline');
    });

    $("#jump-to-date").on('click', '#remove-jump', function(event) {
        event.preventDefault();
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth() + 1; // jan starts at 0
        var y = date.getFullYear();
        $("#jump-date").val('');
        var sDate = y + '-' + m + '-' + d;
        var jumpDate = $("#calendar").fullCalendar.moment(sDate);
        //var moment = $('#calendar').fullCalendar('getDate');
        console.log(moment);
        $("#calendar").fullCalendar('gotoDate', jumpDate);
        $("#remove-jump").css('display', 'none');
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
    eventObject.end = $.fullCalendar.moment(eventObject.start).add(eventObject.trip.duration, 'hours');
    eventObject.id = randomString();
    eventObject.color = reproColor(eventObject.session.boat_id).bgcolor;
    eventObject.textColor = reproColor(eventObject.session.boat_id).txtcolor;
    if (eventObject.session.deleted_at) {
        eventObject.color = colorOpacity(eventObject.color, 0.2);
        if (eventObject.textColor == '#000000') // black
            eventObject.textColor = colorOpacity(eventObject.textColor, 0.3);
    }

    if (eventObject.ticketsLeft == 0) eventObject.color = "#f00";

    return eventObject;

    // Render the event on the calendar
    // The last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
    // $('#calendar').fullCalendar('renderEvent', eventObject, true);
}

function showModalWindow(eventObject) {
    // Create the modal window from session-template
    window.sw.sessionTemplateD = Handlebars.compile($("#session-template").html());

    // console.log(eventObject.session);
    if (eventObject.trip.boat_required && _.size(window.boats) > 0) {
        eventObject.boats = $.extend(true, {}, window.boats);

        if (!eventObject.session.boat_id) {
            // Set default
            eventObject.session.boat_id = _.values(eventObject.boats)[0].id;
        }

        eventObject.boats[eventObject.session.boat_id].selected = true;
    }

    // console.log(eventObject);

    $('#modalWindows')
        .append(window.sw.sessionTemplateD(eventObject)) // Create the modal
        .children('#modal-' + eventObject.id) // Directly find it and use it
        .data('eventObject', eventObject) // Assign the eventObject to the modal DOM element
        .reveal({ // Open modal window | Options:
            animation: 'fadeAndPop', // fade, fadeAndPop, none
            animationSpeed: 300, // how fast animtions are
            closeOnBackgroundClick: true, // if you click background will modal close?
            dismissModalClass: 'close-modal', // the class of a button or element that will close an open modal
            'eventObject': eventObject, // Submit by reference to later get it as this.eventObject
            onCloseModal: function() {
                // Aborted action
                // debugger;
                if (this.eventObject.isNew) {
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

function showModalWindowAccommodation(eventObject) {
    // Create the modal window from accommodation-template
    window.sw.accommodationTemplateD = Handlebars.compile($("#accommodation-template").html());

    console.log(eventObject);

    $('#modalWindows')
        .append(window.sw.accommodationTemplateD(eventObject)) // Create the modal
        .children('#modal-' + eventObject.id) // Directly find it and use it
        .data('eventObject', eventObject) // Assign the eventObject to the modal DOM element
        .reveal({ // Open modal window | Options:
            animation: 'fadeAndPop', // fade, fadeAndPop, none
            animationSpeed: 300, // how fast animtions are
            closeOnBackgroundClick: true, // if you click background will modal close?
            dismissModalClass: 'close-modal', // the class of a button or element that will close an open modal
            'eventObject': eventObject, // Submit by reference to later get it as this.eventObject
            onFinishModal: function() {
                $('#modal-' + this.eventObject.id).remove();
            },
        });
}

function showModalWindowCourse(eventObject) {
    // Create the modal window from class-template
    window.sw.classTemplateD = Handlebars.compile($("#class-template").html());

    console.log(eventObject);

    $('#modalWindows')
        .append(window.sw.classTemplateD(eventObject)) // Create the modal
        .children('#modal-' + eventObject.id) // Directly find it and use it
        .data('eventObject', eventObject) // Assign the eventObject to the modal DOM element
        .reveal({ // Open modal window | Options:
            animation: 'fadeAndPop', // fade, fadeAndPop, none
            animationSpeed: 300, // how fast animtions are
            closeOnBackgroundClick: true, // if you click background will modal close?
            dismissModalClass: 'close-modal', // the class of a button or element that will close an open modal
            'eventObject': eventObject, // Submit by reference to later get it as this.eventObject
            onFinishModal: function() {
                $('#modal-' + this.eventObject.id).remove();
            },
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
                "paging": false,
                "ordering": false,
                "info": false,
                "pageLength": 10,
                "searching": true,
                columns: [{
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
                    render: 'shoe'
                }, {
                    data: null,
                    render: 'chest'
                }, {
                    data: null,
                    render: 'height'
                }, {
                    data: null,
                    render: 'lastDive'
                }, {
                    data: null,
                    render: 'ticket'
                }, {
                    data: null,
                    render: 'reference'
                }, {
                    data: null,
                    render: 'notes'
                }],
                "dom": 'T<"clear">lfrtip',
                "tableTools": {
                    "sSwfPath": "/common/vendor/datatables-tabletools/swf/copy_csv_xls_pdf.swf"
                }
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
                    render: 'name'
                }, {
                    data: null,
                    render: 'country'
                }, {
                    data: null,
                    render: 'phone'
                }, {
                    data: null,
                    render: 'shoe'
                }, {
                    data: null,
                    render: 'chest'
                }, {
                    data: null,
                    render: 'height'
                }, {
                    data: null,
                    render: 'lastDive'
                }, {
                    data: null,
                    render: 'course'
                }, {
                    data: null,
                    render: 'reference'
                }, {
                    data: null,
                    render: 'notes'
                }],
                "dom": 'T<"clear">lfrtip',
                "tableTools": {
                    "sSwfPath": "/common/vendor/datatables-tabletools/swf/copy_csv_xls_pdf.swf"
                }
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
    if (duration >= 24)
        return Math.floor(duration / 24) + ' days, ' + (duration % 24) + ' hours';
    else
        return duration + ' hours';
});
Handlebars.registerHelper('getRemaining', function(capacity, booking) {
    return capacity - booking;
});
Handlebars.registerHelper('isWeekday', function(day) {
    if (this.start.format('d') == day)
        return new Handlebars.SafeString('checked onchange="this.checked=!this.checked;"');
    else
        return '';
});

function calcUtil(booked, capacity) {
    if (!capacity) return 0;

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
    if (calendarOptions.filterByTrip) sessionFilters.trip_id = calendarOptions.tripFilter;
    Session.filter(sessionFilters, function success(data) {
            //console.log(data);
            window.sessions = _.indexBy(data, 'id');

            console.log(window.sessions);

            var events = [];

            // Create eventObjects
            _.each(window.sessions, function(value) {
                if (calendarOptions.filterByBoat) {
                    if (calendarOptions.boatFilter == value.boat_id) {
                        var booked = value.capacity[0];
                        var capacity = value.capacity[1];
                        var ticketsLeft = capacity ? (capacity - booked) : '∞ (shore-based)';
                        var sameDay = true;
                        if (window.trips[value.trip_id].duration > 24) sameDay = false;
                        var eventObject = {
                            title: window.trips[value.trip_id].name + ' ' + calcUtil(booked, capacity) + '%', // use the element's text as the event title
                            allDay: false,
                            trip: window.trips[value.trip_id],
                            session: value,
                            isNew: false,
                            editable: false, // This uses a 'falsy' check on purpose
                            durationEditable: false,
                            //className: value.timetable_id ? 'timetabled' : '',*/ // This uses a 'falsy' check on purpose
                            ticketsLeft: ticketsLeft,
                            capacityString: capacity ? (ticketsLeft + ' out of ' + capacity) : ticketsLeft,
                            capacity: capacity,
                            sameDay: sameDay
                        };

                        if (ticketsLeft == 0) eventObject.title = window.trips[value.trip_id].name + " FULL";

                        eventObject.session.start = $.fullCalendar.moment(value.start);
                        eventObject.type = "trip";
                        events.push(createCalendarEntry(eventObject));
                    }
                } else {
                    var booked = value.capacity[0];
                    var capacity = value.capacity[1];
                    var ticketsLeft = capacity ? (capacity - booked) : '∞ (shore-based)';
                    var sameDay = true;
                    if (window.trips[value.trip_id].duration > 24) sameDay = false;
                    var eventObject = {
                        title: window.trips[value.trip_id].name + ' ' + calcUtil(booked, capacity) + '%', // use the element's text as the event title
                        allDay: false,
                        trip: window.trips[value.trip_id],
                        session: value,
                        isNew: false,
                        editable: false, // This uses a 'falsy' check on purpose
                        durationEditable: false,
                        //className: value.timetable_id ? 'timetabled' : '',*/ // This uses a 'falsy' check on purpose
                        ticketsLeft: ticketsLeft,
                        capacityString: capacity ? (ticketsLeft + ' out of ' + capacity) : ticketsLeft,
                        capacity: capacity,
                        sameDay: sameDay
                    };

                    if (ticketsLeft == 0) eventObject.title = window.trips[value.trip_id].name + " FULL";

                    eventObject.session.start = $.fullCalendar.moment(value.start);
                    eventObject.type = "trip";
                    events.push(createCalendarEntry(eventObject));

                }
            });

            callback(events);

            // Remove loading indictor
            $('#fetch-events-loader').remove();
        },
        function error(xhr) {
            $('.loader').remove();
        });

}

function getAllEvents(start, end, timezone, callback) {

    // Start loading indicator
    $('.fc-center h2').after('<div id="fetch-events-loader" class="loader"></div>');
    //console.log(start.format(), end.format());
    var sessionFilters = {
        'after': start.format(),
        'before': end.format(),
        'with_full': 1
    };

    var events = [];
    window.promises.sessionFilterLoaded = $.Deferred();
    window.promises.classFilterLoaded = $.Deferred();
    window.promises.accommodationFilterLoaded = $.Deferred();

    Session.filter(sessionFilters, function success(data) {
            //console.log(data);
            window.sessions = _.indexBy(data, 'id');

            console.log(window.sessions);

            // Create eventObjects
            _.each(window.sessions, function(value) {
                if (calendarOptions.filterByBoat) {
                    if (calendarOptions.boatFilter == value.boat_id) {
                        var booked = value.capacity[0];
                        var capacity = value.capacity[1];
                        var ticketsLeft = capacity ? (capacity - booked) : '∞ (shore-based)';
                        var sameDay = true;
                        if (window.trips[value.trip_id].duration > 24) sameDay = false;
                        var eventObject = {
                            title: window.trips[value.trip_id].name + ' ' + calcUtil(booked, capacity) + '%', // use the element's text as the event title
                            allDay: false,
                            trip: window.trips[value.trip_id],
                            session: value,
                            isNew: false,
                            editable: false, // This uses a 'falsy' check on purpose
                            durationEditable: false,
                            //className: value.timetable_id ? 'timetabled' : '',*/ // This uses a 'falsy' check on purpose
                            ticketsLeft: ticketsLeft,
                            capacityString: capacity ? (ticketsLeft + ' out of ' + capacity) : ticketsLeft,
                            capacity: capacity,
                            sameDay: sameDay
                        };

                        if (ticketsLeft == 0) eventObject.title = window.trips[value.trip_id].name + " FULL";

                        eventObject.session.start = $.fullCalendar.moment(value.start);
                        eventObject.type = "trip";
                        events.push(createCalendarEntry(eventObject));
                    }
                } else {
                    var booked = value.capacity[0];
                    var capacity = value.capacity[1];
                    var ticketsLeft = capacity ? (capacity - booked) : '∞ (shore-based)';
                    var sameDay = true;
                    if (window.trips[value.trip_id].duration > 24) sameDay = false;
                    var eventObject = {
                        title: window.trips[value.trip_id].name + ' ' + calcUtil(booked, capacity) + '%', // use the element's text as the event title
                        allDay: false,
                        trip: window.trips[value.trip_id],
                        session: value,
                        isNew: false,
                        editable: false, // This uses a 'falsy' check on purpose
                        durationEditable: false,
                        //className: value.timetable_id ? 'timetabled' : '',*/ // This uses a 'falsy' check on purpose
                        ticketsLeft: ticketsLeft,
                        capacityString: capacity ? (ticketsLeft + ' out of ' + capacity) : ticketsLeft,
                        capacity: capacity,
                        sameDay: sameDay
                    };

                    if (ticketsLeft == 0) eventObject.title = window.trips[value.trip_id].name + " FULL";

                    eventObject.session.start = $.fullCalendar.moment(value.start);
                    eventObject.type = "trip";
                    events.push(createCalendarEntry(eventObject));
                }
            });

            window.promises.sessionFilterLoaded.resolve();
        },
        function error(xhr) {
            $('.loader').remove();
        });

    Class.filter(sessionFilters, function success(data) {
            window.trainingSessions = _.indexBy(data, 'id');
            console.log(data);

            // Create eventObjects
            _.each(window.trainingSessions, function(value) {
                var eventObject = {
                    title: window.trainings[value.training_id].name, // use the element's text as the event title
                    allDay: false,
                    trip: window.trainings[value.training_id],
                    session: value,
                    isNew: false,
                    editable: value.timetable_id ? false : true, // This uses a 'falsy' check on purpose
                    durationEditable: false,
                    className: value.timetable_id ? 'timetabled' : '', // This uses a 'falsy' check on purpose,
                    isTrip: false
                };

                eventObject.session.start = $.fullCalendar.moment(value.start);
                eventObject.type = "class";
                events.push(createCalendarEntry(eventObject));
            });

            window.promises.classFilterLoaded.resolve();
        },
        function error(xhr) {
            $('.loader').remove();
        });

    Accommodation.filter(sessionFilters, function success(data) {

            console.log(data);

            _.each(data, function(value, key) {
                var start = new moment(key);

                _.each(value, function(util, id) {
                    var eventObject = {
                        start: start, // change start to readable text instead of moment
                        end: start,
                        id: randomString(),
                        title: window.accommodations[id].name,
                        color: "#229930",
                        booked: util[0],
                        available: (util[1] - util[0])
                    };
                    if (eventObject.available == 0) eventObject.color = "#f00";
                    eventObject.type = "accom";
                    events.push(eventObject);
                    //$('#calendar').renderEvent(eventObject);
                });
            });

            window.promises.accommodationFilterLoaded.resolve();
        },
        function error(xhr) {
            $('.loader').remove();
        });



    // Wait for the requests to load and then return events
    $.when(
        window.promises.sessionFilterLoaded,
        window.promises.classFilterLoaded,
        window.promises.accommodationFilterLoaded
    ).done(function() {
        // Remove loading indictor
        $('#fetch-events-loader').remove();

        callback(events);
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
    if (calendarOptions.filterByClass) sessionFilters.training_id = calendarOptions.classFilter;
    var events = [];

    Class.filter(sessionFilters, function success(data) {
            window.trainingSessions = _.indexBy(data, 'id');
            console.log(data);

            // Create eventObjects
            _.each(window.trainingSessions, function(value) {
                var eventObject = {
                    title: window.trainings[value.training_id].name, // use the element's text as the event title
                    allDay: false,
                    trip: window.trainings[value.training_id],
                    session: value,
                    isNew: false,
                    editable: value.timetable_id ? false : true, // This uses a 'falsy' check on purpose
                    durationEditable: false,
                    className: value.timetable_id ? 'timetabled' : '', // This uses a 'falsy' check on purpose,
                    isTrip: false
                };

                eventObject.session.start = $.fullCalendar.moment(value.start);
                eventObject.type = "class";
                events.push(createCalendarEntry(eventObject));
            });

            callback(events);

            $('#fetch-events-loader').remove();

        },
        function error(xhr) {
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
    if (calendarOptions.filterByAccom) sessionFilters.accommodation_id = calendarOptions.accomFilter;
    Accommodation.filter(sessionFilters, function success(data) {

            console.log(data);
            var events = [];

            _.each(data, function(value, key) {
                var start = new moment(key);

                _.each(value, function(util, id) {
                    var eventObject = {
                        start: start, // change start to readable text instead of moment
                        end: start,
                        id: randomString(),
                        title: window.accommodations[id].name,
                        color: "#229930",
                        booked: util[0],
                        available: (util[1] - util[0])
                    };
                    if (eventObject.available == 0) eventObject.color = "#f00";
                    eventObject.type = "accom";
                    events.push(eventObject);
                    //$('#calendar').renderEvent(eventObject);
                });
            });

            callback(events);
            $('#fetch-events-loader').remove();
        },
        function error(xhr) {
            $('.loader').remove();
        });
}


function addTripFilter(value) {

    if (value == 'boat') {
        $("div#filter-settings option[value=boat]").attr('disabled', true);
        $("#filter-options").val('all');
        $("#filter").append(calendarOptions.boatsListTemplate({
            boats: window.boats
        }));
    } else if (value == 'trip') {
        $("div#filter-settings option[value=trip]").attr('disabled', true);
        $("#filter-options").val('all');
        $("#filter").append(calendarOptions.tripsListTemplate({
            trips: window.trips
        }));
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
    this._notes = customer.pivot.notes;

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
}

function editBooking(booking_id, self) {
    // Set loading indicator
    $(self).after('<span id="save-loader" class="loader"></span>');

    // Load booking data and redirect to add-booking tab
    Booking.get(booking_id, function success(object) {
        window.booking = object;
        window.clickedEdit = true;

        window.location.hash = 'add-booking';
    });
}
