
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1 && (!window.tourStart))
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
    Trip.getAllWithTrashed(function(data) { // async
        window.trips = _.indexBy(data, 'id');
        $("#trips-select").append(calendarOptions.tripsListTemplate({
            trips: data
        }));
        window.promises.loadedTrips.resolve();
    });

    window.promises.loadedBoats = $.Deferred();
    Boat.getAllWithTrashed(function(data) {
        window.boats = _.indexBy(data, 'id');
        window.promises.loadedBoats.resolve();
    });

    window.promises.loadedClasses = $.Deferred();
    Class.getAllWithTrashed(function(data) {
        window.trainings = _.indexBy(data, 'id');
        window.promises.loadedClasses.resolve();
    });

    window.promises.loadedAccommodations = $.Deferred();
    Accommodation.getAllWithTrashed(function(data) {
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
			defaultView : 'basicWeek',
            eventConstraint : {
                dow : [1, 2, 3, 4, 5, 6, 7],
                businessHours : {
                    start : '00:00',
                    end   : '24:00'
                }
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
			eventAfterRender : function(event, element) {
				$(element).css('height', '25px');
				$(element).css('text-align', 'center');
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
            }
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

function getFileName(data, type) {
    if(type === 'trip') {
        return data.trip.name + ' Trip Manifest - ' + data.start;
    } else if(type === 'class') {
        return data.training.name + ' Class Manifest - ' + data.start;
    } else {
        return data.accommodation.name + ' Accommodation Manifest - ' + data.start;
    }
}

function showModalWindowManifest(id, type, date) {
    // Create the modal window from manifest-template
    var params = {
        id: id
    };
    if (type == 'trip') {
        window.sw.manifestTemplateD = Handlebars.compile($("#manifest-template").html());
        Session.getAllCustomers(params, function success(data) {
            console.log('data', data);
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
                },{
                    data: null,
                    render: 'addons'
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
                },
                {
                    data: null,
                    render: 'cylinder_size'
                }],
                "dom": '<"col-md-6 dt-buttons"B><"col-md-6"f>rt<"col-md-6"l><"col-md-6"p>',
				"buttons": [
					{
						extend : 'excel',
						title  : getFileName(data, 'trip')
					},
					{
						extend : 'pdf',
						title  : getFileName(data, 'trip'),
                        orientation: 'landscape',
                        customize : function(doc) {
                            var colCount = new Array();
                            $(tbl).find('tbody tr:first-child td').each(function () {
                                if ($(this).attr('colspan')) {
                                    for (var i=1;i<=$(this).attr('colspan');$i++) {
                                        colCount.push('*');
                                    }
                                } else {
                                    colCount.push('*');
                                }
                            });
                            doc.content[1].table.widths = colCount;
                        }
					},
					{
						extend : 'print',
						title  : getFileName(data, 'trip')
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
    } else if (type === 'class') {
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
                },
                {
                    data: null,
                    render: 'cylinder_size'
                }],
                "dom": '<"col-md-6 dt-buttons"B><"col-md-6"f>rt<"col-md-6"l><"col-md-6"p>',
                "buttons": [
                    {
                        extend : 'excel',
                        title  : getFileName(data, 'class')
                    },
                    {
                        extend : 'pdf',
                        title  : getFileName(data, 'class'),
                        orientation: 'landscape',
                        customize : function(doc) {
                            var colCount = new Array();
                            $(tbl).find('tbody tr:first-child td').each(function () {
                                if ($(this).attr('colspan')) {
                                    for (var i=1;i<=$(this).attr('colspan');$i++) {
                                        colCount.push('*');
                                    }
                                } else {
                                    colCount.push('*');
                                }
                            });
                            doc.content[1].table.widths = colCount;
                        }
                    },
                    {
                        extend : 'print',
                        title  : getFileName(data, 'class')
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
                }

                table.draw();
            });
        });
    }
    else if (type === 'accommodation') {
        window.sw.manifestTemplateA = Handlebars.compile($("#accommodation-manifest-template").html());
        params.date = date;
        Accommodation.getManifest(params, function success(res) {
            console.log(res);
            var date = jFriendly(res.data.date);
            console.log(date);
            //showModalWindowManifest(data);
            //var customer = Handlebars.compile( $("#customer-rows-template").html() );
            //$("#customers-table").append(customer({customers : data.customers}));
            $('#modalWindows')
                .append(window.sw.manifestTemplateA(res.data)) // Create the modal
                .children('#modal-' + res.data.accommodation[0].id + '-' + date) // Directly find it and use it
                .reveal({ // Open modal window | Options:
                    animation: 'fadeAndPop', // fade, fadeAndPop, none
                    animationSpeed: 300, // how fast animtions are
                    closeOnBackgroundClick: true, // if you click background will modal close?
                    dismissModalClass: 'close-modal', // the class of a button or element that will close an open modal
                    onFinishModal: function() {
                        $('#modal-' + res.data.accommodation[0].id + '-' + date).remove();
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
                    render: 'notes'
                }],
                "dom": 'Bfrtlp',
                "buttons": [
                    {
                        extend : 'excel',
                        title  : res.data.accommodation[0].name + ' - Accommodation manifest ( ' + res.data.date + ' )'
                    },
                    {
                        extend : 'pdf',
                        title  : res.data.accommodation[0].name + ' - Accommodation manifest ( ' + res.data.date + ' )',
                        orientation : 'landscape'
                    },
                    {
                        extend : 'print',
                        title  : res.data.accommodation[0].name + ' - Accommodation manifest ( ' + res.data.date + ' )'
                    }
                ]
            });

            $.when(
                window.promises.loadedCountries,
                window.promises.loadedCourses,
                window.promises.loadedTickets
            ).done(function() {
                for (var i = 0; i < res.data.accommodation[0].bookings.length; i++) {
                    table.row.add(new customerDataA(res.data.accommodation[0].bookings[i]));
                }

                table.draw();
            });
        });
    }
}

Handlebars.registerHelper('date', function(datetime) {
    return datetime.format('DD-MM-YYYY');
});

Handlebars.registerHelper('convertDate', function(ts) {
    return new Date(ts).toDateString();
})
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
// @todo move this to global function
Handlebars.registerHelper('jFriendly', function(str) {
    return jFriendly(str);
});

function jFriendly(str) {
    var string = str.replace(/:/g, "-");
    return string.replace(/\s/g, "");
}

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
                        accommodation_id : id,
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
                        accommodation_id : id,
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
    console.log('cust', customer);
    this._name = customer.firstname + ' ' + customer.lastname;
    this._phone = customer.phone;
    if(customer.country_id !== null) {
        this._country = window.countries[customer.country_id].abbreviation;
    }
    else {
        this._country = '';
    }
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

    this._cylinder_size = customer.cylinder_size || '-';

	var paid = 0;

	_.each(customer.pivot.payments, function(obj) {
		paid += parseFloat(obj.amount);
	});
	_.each(customer.pivot.refunds, function(obj) {
		paid -= parseFloat(obj.amount);
	});

	this.amount_paid = paid.toFixed(2);

    var addons = [];

    for(var i = 0; i < customer.pivot.addons.length; i++) {
        if (customer.pivot.addons[i].pivot.quantity > 1) {
            addons.push(customer.pivot.addons[i].name + ' x ' + customer.pivot.addons[i].pivot.quantity.toString());
        } else {
            addons.push(customer.pivot.addons[i].name);
        }
    }

    if(addons.length > 0) {
        this._addons = addons.join(', ');
    }
    else {
        this._addons = '-';
    }

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

    this.addons = function() {
        return this._addons;
    }

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

    this.cylinder_size = function() {
        return this._cylinder_size;
    }

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

function calcPayments(payments) {
    var paid = 0;
    for(var i in payments) {
        paid += parseFloat(payments[i].amount);
    }
    return paid;
}

function customerDataA(data) {
    this._name = data.lead_customer.firstname + ' ' + data.lead_customer.lastname;
    this._phone = data.lead_customer.phone;
    if(data.lead_customer.country_id !== null) {
        this._country = window.countries[data.lead_customer.country_id].abbreviation;
    } else {
        this._country = '-';
    }

    this._reference = data.reference;
    this._booking_id = data.id;
    this._notes = data.comment || "-";
    this._price = data.real_decimal_price ? data.real_decimal_price : data.decimal_price;
    this._amount_paid = calcPayments(data.payments);

    this.name = function() {
        return this._name;
    };

    this.phone = function() {
        return this._phone + '&nbsp;'; // The forced space at the end makes the phone number be recognised as a string in Excel (instead of a number, when exporting via DataTable's CSV/Excel export)
    };

    this.country = function() {
        return this._country;
    };

    this.reference = function() {
        return '<a href="javascript:void(0);" onclick="editBooking(' + this._booking_id + ', this);">' + this._reference + '</a>';
    };

    this.notes = function() {
        return this._notes;
    };

    this.status = function() {
        return window.company.currency.symbol + ' ' + this._amount_paid + ' / ' + window.company.currency.symbol + ' ' + this._price;
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
