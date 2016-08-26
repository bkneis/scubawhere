function TourManager() {

	var accommodationTour = new Tour({
		steps: [
			{
				element : '#accommodations-list', // @todo find a way to float this centre screen
				title	: 'Managing Accommodations',
				content : 'Do you own or manage any accommodation? If so, click next. Otherwise, you can just skip this step'
			},
			{
				element : '#acom-name',
				title: 'Adding Accommodation',
				placement: 'left',
				content: 'To get started, enter the room name and description.',
				onShown	  : function(tour) {
					$("#room-name").val("3* Hotel Single Room");
				}
			},
			{
				element	  : '#acom-base',
				title	  : 'Assigning a Price',
				placement : 'left',
				content	  : 'Here you can set a price per night for the accommodation',
				onShown   : function(tour) {
					$("#acom-price").val(50);
				}
			},
			{
				element	  : '#acom-season',
				title	  : 'Assigning Seasonal Price Changes',
				placement : 'left',
				content	  : 'If you have prices that change throughout the year, you can ajust your prices depening on the seasons',
				onShown   : function(tour) {
					$("#acom-season-price").click();
					$("#seasonal-prices-list #acom-price").val(60);
				}
			},
			{
				element   : '#acom-rooms',
				title     : 'Assiging the number of rooms',
				placement : 'left',
				content   : 'Lastly enter the number of rooms you have available. If the room is a dorm room, then treat each dorm room. Lastly, click save to add your accommodation.',
				onShown   : function(tour) {
                	$("#room-amount").val(6);
				}
			},
			{
				element   : '#accommodations-list',
				title     : 'Viewing Your Accommodation',
				placement : 'right',
				content   : 'Once an accommodation is saved, you will see it in your list. Click on an accommodation to view or edit its details.',
				onShown   : function(tour) {
					clearForm();
					$("#no-accommodations").remove();
					$("#accommodation-list").append('<li id="dummy-room"><strong>3* Hotel Single Room</strong> | 6 | 50</li>');
				}
			}
		],
		onEnd : function(tour) {
			$('#dummy-room').remove();
		}
	});

	var agentsTour = new Tour({
		steps: [
			{
				element : '#agent-list-div', // @todo find a way to float this centre screen
				title	: 'Managing Agents',
				content : 'Do you recieve any reservations from 3rd party booking agents? For example, travel agents, hotel booking desk, etc. If so, click next. Otherwise, you can just skip this step'
			},
			{
				element : '#agent-form-container',
				title: 'Adding Agents',
				placement: 'left',
				content: 'Create an agent profile, by entering their details',
				onShown	  : function(tour) {
					$("#agent-name").val("John doe");
					$("#agent-web").val("http://www.onlinescubaholidays.com");
					$("#branch-name").val("Scuba holidays R us");
					$("#branch-address").val("46 grand avenue tenerife");
					$("#branch-phone").val("+44 7866565047");
					$("#branch-email").val("john.doe@scubaholidays.com");
				}
			},
			{
				element	  : '#commission-div',
				title	  : 'Assigning a Commission',
				placement : 'left',
				content	  : 'Enter the percentage of commission the agent recieves for each reservation.',
				onShown   : function(tour) {
					$("#commission-amount").val(20);
				}
			},
			{
				element	  : '#terms-div',
				title	  : 'Defining the business terms',
				placement : 'left',
				content	  : "Define your terms of business to the agent with one of the drop down options. 'Deposit only' means the agent will take the commission as a deposit. 'Full amount' means the agent gets paid the full amount for the reservation, then you will invoice the agent for payment. 'Banned' means that the agent is blocked and they are no longer allowed to make reservations. Lastly, click 'save' to add your agent.",
				onShown   : function(tour) {
					$('#terms').val('deposit');
				}
			},
			{
				element   : '#agent-list-div',
				title     : 'Viewing your agents',
				placement : 'left',
				content   : 'Once an agent is saved, you will see it in your list. Click on an agent to view or edit its details.',
				onShown   : function(tour) {
					clearForm();
					$("#no-agents").remove();
					$("#agent-list").append('<li id="dummy-agent"><strong>John doe</strong> | Scuba holidays r us</li>');
				}
			}
		],
		onEnd : function(tour) {
			$('#dummy-agent').remove();
		}
	});

	var locationsTour = new Tour({
		steps: [
			{
				element   : '#map-container', // @todo find a way to float this centre screen
				title	  : 'Managing Locations',
				placement : 'top',
				content   : 'Here is where you declare your dive locations. These will be used when creating a trip.'
			},
			{
				element : '#add-location',
				title: 'Adding Locations',
				placement: 'bottom',
				content: "To add a location, simply enter in the Latitude and Longitude co-ordinates and click 'Show'.",
				onShown	  : function(tour) {
				}
			},
			{
				element : '#showLocation',
				title: 'Viewing the location',
				placement: 'left',
				content: 'Click ‘Show’ to see exactly where the co-ordinates display on the Map.',
				onShown	  : function(tour) {
				}
			},
			{
				element : '#createLocation',
				title: 'Creating the location',
				placement: 'left',
				content: "When you click 'Create', a pop up box will appear. Enter a name and description for the dive location. Additionally, you can select any relevant tags for the dive location.",
				onShown	  : function(tour) {
				}
			},
			{
				element : '#map-container',
				title: 'Viewing your locations',
				placement: 'top',
				content: 'Here is a map that displays all the available dive locations, made by you and other dive operators. The house icon represents where your dive operation is based.',
				onShown	  : function(tour) {
				}
			},
			{
				element : '#markers-info',
				title: 'Loctation Markers',
				placement: 'left',
				content: 'Red tags indicate your dive locations. Blue tags indicate dive locations used by other dive operators. For more information on a dive location, click on a tag.',
				onShown	  : function(tour) {
				}
			}
		],
		onEnd : function(tour) {
		}
	});

	var boatsTour = new Tour({
		steps: [
			{
				element   : '#boat-list-container', // @todo find a way to float this centre screen
				title	  : 'Managing Your Boats',
				placement : 'right',
				content   : 'Now, we need to add your boats. Boats are assigned to trips once activated. This allows you to manage the schedule of your boats.'
			},
			{
				element   : '#cabins-container',
				title     : 'Manage Cabins',
				placement : 'right',
				content   : 'If your dive operation offers liveaboards, you will need to declare the diffrent types of cabins.',
				onShown	  : function(tour) {
				}
			},
			{
				element   : '#boatroom-list-container',
				title     : 'Creating Cabins',
				placement : 'right',
				content   : 'To add a cabin, click ADD CABIN. Then enter a name and description in the form to thr right.',
				onShown	  : function(tour) {
				}
			},
			{
				element   : '#boat-form-container',
				title     : 'Creating Boats',
				placement : 'left',
				content   : 'Enter a name and description for the boat.',
				onShown	  : function(tour) {
					$("#boat-name").val("Barry's big boat");
					//CKEDITOR.setData("Add a description of your boat here."); 
				}
			},
			{
				element   : '#boat-capacity',
				title     : 'Assigning the Boats Size',
				placement : 'left',
				content   : 'Enter your boat capacity, excluding your crew.',
				onShown	  : function(tour) {
					$("#boat-capacity").val(25);
				}
			},
			{
				element   : '#boat-cabins',
				title     : 'Assigning Cabins to a Boat',
				placement : 'left',
				content   : 'Here shows a summary of the cabins available for this boat. To attach a cabin to a boat, click add cabin and select the cabin type and number of rooms',
				onShown	  : function(tour) {
					$("#room-types").append('<p id="cabin-option">' +
					'<select class="room-type-select">' +
					'<option value="{{id}}">Single Cabin</option>' +
					'</select> Number of rooms:' +
					'<input type="number" value="6" placeholder="0" style="width: 100px;" min="0">' +
					'<button class="btn btn-danger remove-room">&#215;</button>' +
					'</p>');
				}
			},
			{
				element   : '#boat-list-container',
				title     : 'Viewing Your Boats',
				placement : 'right',
				content   : 'Once a boat is saved, you will see it in your list. Click on a boat to view/edit the details.',
				onShown	  : function(tour) {
					$("#boat-list").append('<li id="dummy-boat"><strong>Barrys big boat</strong> | Capacity: 25</li>');
				}
			}
		],
		onEnd : function(tour) {
        	$("#dummy-boat").remove();
		}
	});
	
	var tripsTour = new Tour({
		steps: [
			{
				element   : '#trip-list-container', // @todo find a way to float this centre screen
				title	  : 'Managing Trips',
				placement : 'right',
				content   : 'Now, we need to add your trips. A trip consists of all the information for a dive, and are used to create tickets.'
			},
			{
				element   : '#trip-form-container',
				title     : 'Creating a trip',
				placement : 'left',
				content   : 'Enter a name, description and duration for the trip. Please note trip duration is in hours.',
				onShown	  : function(tour) {
					$("#trip-name").val("Single boat dive");
					$("#tripDuration").val(4);
				}
			},
			{
				element   : '#locationsList',
				title     : 'Select a loation',
				placement : 'left',
				content   : 'Next, select the locations of the trip',
				onShown	  : function(tour) {
                	$('#locationsList').find('.location').filter(':first').click();
				}
			},
			{
				element   : '#tagsList',
				title     : 'Adding tags',
				placement : 'left',
				content   : "Next, select any tags that describes what is offered in the trip. These tags will be searchable when scubawhere.com is launched. Lastly click 'Save' to create the trip.",
				onShown	  : function(tour) {
					$('#tagsList').find('.tag').filter(':first').click();
				}
			},
			{
				element   : '#trips-list-div',
				title     : 'Viewing Trips',
				placement : 'right',
				content   : 'Once a trip is saved, you will see it in your list. Click on a trip to view/edit the details.',
				onShown	  : function(tour) {
					$("#no-trips").remove();
					$("#trip-list").append('<li id="dummy-trip"><strong>Single boat dive</strong> | 0d 4h </li>');
				}
			}
		],
		onEnd : function(tour) {
        	$("#dummy-trip").remove();
		}
	});


	var ticketsTour = new Tour({
		steps: [
			{
				element   : '#tickets-list-div', // @todo find a way to float this centre screen
				title	  : 'Managing Tickets',
				placement : 'right',
				content   : 'Now, we need to add your tickets. A ticket can be valid for many trips. A ticket is a single reservation for a trip. For an educational course please create a package (see next page).'
			},
			{
				element   : '#ticket-form-container',
				title     : 'Creating a Ticket',
				placement : 'left',
				content   : 'Enter a name, description and base price for the ticket.',
				onShown	  : function(tour) {
					$("#ticket-name").val("2 dive boat ticket");
					$("#acom-price").val(50); //@todo change handlebars tempate to change name based on tab
				}
			},
			{
				element   : '#tickets-trips',
				title     : 'Assigning Trips',
				placement : 'left',
				content   : 'Select which trips a ticket can be used for. The ticket is valid for only 1 trip.',
				onShown	  : function(tour) {
					$('input').filter('[name="trips[]"]').first().click();
				}
			},
			{
				element   : '#tickets-boats',
				title     : 'Assigning Specific Boats to a Trip',
				placement : 'left',
				content   : 'You can also limit the ticket to be used for specific boats.',
				onShown	  : function(tour) {
					$("#tickets-boats-checkbox").click();
				}
			},
			{
				element   : '#tickets-boatrooms',
				title     : 'Assigning Specific Boatrooms to a Boat',
				placement : 'left',
				content   : 'You can also limit the ticket to be used on specific cabins for overnight trips. Click Save to create the ticket.',
				onShow	  : function(tour) {
					$("#tickets-boats-checkbox").click();
				},
				onShown	  : function(tour) {
					$("#tickets-boatroom-checkbox").click();
				}
			},
			{
				element   : '#tickets-availability',
				title     : "Limitin a Ticket's Availabity",
				placement : 'left',
				content   : 'You can also limit the ticket to only be booked during specific dates by entering a before and after date',
				onShow	  : function(tour) {
					$("#tickets-boatroom-checkbox").click();
				},
				onShown	  : function(tour) {
					$('#tickets-availability-checkbox').click();
				}
			},
			{
				element   : '#ticket-list-container',
				title     : 'Viewing Tickets',
				placement : 'right',
				content   : 'Once a ticket is saved, you will see it in your list. Click on a ticket to view or edit  its details.',
				onShown	  : function(tour) {
					clearForm();		
					$("#ticket-list").append('<li id="dummy-ticket"><strong>2 dive boat ticket</strong> | ' + window.currency + '50.00 </li>');
				}
			}
		],
		onEnd : function(tour) {
        	$("#dummy-ticket").remove();
		}
	});
	
	this.getAccommodationsTour = function() {
		if(window.tourStart) 
		{
			accommodationTour.init();
			accommodationTour.start(true);	
			// Force the tour to start at the beginning
			accommodationTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 1) {
                    window.currentStep = {
                        tab: "#agents",
                        position: 2
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#agent-tab').addClass("selected");
                window.location.href = "#agents";
            });
		}
	};

	var classesTour = new Tour({
		steps: [
			{
				element   : '#classes-list-div', // @todo find a way to float this centre screen
				title	  : 'Managing Classes',
				placement : 'right',
				content   : 'Now, we need to add your classes. A class is any event that requires students to participate in learning for a qualification'
			},
			{
				element   : '#class-form-container',
				title     : 'Adding a Class',
				placement : 'left',
				content   : 'Enter a name, description and duration for the class.',
				onShown	  : function(tour) {
					$("#class-name").val("Open Water Theory");
					$("#tripDuration").val(5); // @todo rename this in classes and go through all mangement tabs
				}
			},
			{
				element   : '#add-class',
				title     : 'Saving the Class',
				placement : 'left',
				content   : 'Click SAVE to create the class with the information you provided.',
				onShown	  : function(tour) {
				}
			},
			{
				element   : '#classes-list-div',
				title     : 'Viewing you Classes',
				placement : 'right',
				content   : 'Once a class is saved, you will see it in your list. Click on a class to view or edit its details.',
				onShown	  : function(tour) {
					clearForm()
					$("#class-list").append('<li id="dummy-class"><strong>Open Water Theory</strong> </li>');
				}
			}
		],
		onEnd : function(tour) {
        	$("#dummy-class").remove();
		}
	});

	this.getAgentsTour = function() {
		if(window.tourStart) 
		{
			agentsTour.init();
			agentsTour.start(true);	
			// Force the tour to start at the beginning
			agentsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 2) {
                    window.currentStep = {
                        tab: "#locations",
                        position: 3
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#location-tab').addClass("selected");
                window.location.href = "#locations";
            });
		}
	};

	this.getLocationsTour = function() {
		if(window.tourStart) 
		{
			locationsTour.init();
			locationsTour.start(true);	
			// Force the tour to start at the beginning
			locationsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 3) {
                    window.currentStep = {
                        tab: "#boats",
                        position: 4
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#boat-tab').addClass("selected");
                window.location.href = "#boats";
            });
		}
	};

	this.getBoatsTour = function() {
		if(window.tourStart) 
		{
			boatsTour.init();
			boatsTour.start(true);	
			// Force the tour to start at the beginning
			boatsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 4) {
                    window.currentStep = {
                        tab: "#trips",
                        position: 5
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#trip-tab').addClass("selected");
                window.location.href = "#trips";
            });
		}
	};

	this.getTripsTour = function() {
		if(window.tourStart) 
		{
			tripsTour.init();
			tripsTour.start(true);	
			// Force the tour to start at the beginning
			tripsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 5) {
                    window.currentStep = {
                        tab: "#tickets",
                        position: 6
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#ticket-tab').addClass("selected");
                window.location.href = "#tickets";
            });
		}
	};

	this.getTicketsTour = function() {
		if(window.tourStart) 
		{
			ticketsTour.init();
			ticketsTour.start(true);	
			// Force the tour to start at the beginning
			ticketsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 6) {
                    window.currentStep = {
                        tab: "#classes",
                        position: 7
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#class-tab').addClass("selected");
                window.location.href = "#classes";
            });
		}
	};

	this.getClassesTour = function() {
		if(window.tourStart) 
		{
			classesTour.init();
			classesTour.start(true);	
			// Force the tour to start at the beginning
			classesTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 7) {
                    window.currentStep = {
                        tab: "#courses",
                        position: 8
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#course-tab').addClass("selected");
                window.location.href = "#courses";
            });
		}
	};


};

var TourManager = new TourManager();

/*var Tour = {

    getAcommodationsTour: function() {
        if (window.tourStart) {
            $("#tour-next-step").show();
            $("#tour-finish").hide();
            introJs().setOptions({
                showStepNumbers: false,
                exitOnOverlayClick: false,
                exitOnEsc: false
            }).start().onchange(function(targetElement) {
                switch (targetElement.id) {
                    case "accommodation-form-container":
                        $("#room-name").val("3* Hotel Single Room");
                        $("#acom-price").val(50);
                        break;
                    case "acom-base":
                        $("#add-base-price").click();
                        $("#acom-price").val(50);
                        break;
                    case "acom-season":
                        $("#acom-season-price").click();
                        $("#acom-price").val(50);
                        break;
                    case "acom-rooms":
                        $("#room-amount").val(6);
                        break;
                    case "accommodations-list":
                        $("#no-accommodations").remove();
                        $("#accommodation-list").append('<li id="dummy-room"><strong>3* Hotel Single Room</strong> | 6 | 50</li>');
                        break;
                }
            }).oncomplete(function() {
                $("#dummy-room").remove();
                $("#accommodation-list").append('<p id="no-accommodations">No accommodations available.</p>');
                clearForm();
            });

            $("#tour-next-step").on("click", function() {
                window.location.href = "#agents";
                if (window.currentStep.position <= 1) {
                    window.currentStep = {
                        tab: "#agents",
                        position: 2
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#agent-tab').addClass("selected");
            });
        }
    },

    getAgentsTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 2) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "agent-form-container":
                            $("#agent-name").val("John doe");

                            $("#agent-web").val("http://www.onlinescubaholidays.com");
                            $("#branch-name").val("Scuba holidays R us");
                            $("#branch-address").val("46 grand avenue tenerife");
                            $("#branch-phone").val("+44 7866565047");
                            $("#branch-email").val("john.doe@scubaholidays.com");
                            break;

                        case "commission-div":
                            $("#commission-amount").val(20);
                            break;

                        case "agent-list-div":
                            $("#no-agents").remove();
                            $("#agent-list").append('<li id="dummy-agent"><strong>John doe</strong> | Scuba holidays r us</li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-agent").remove();
                    $("#agent-list").append('<p id="no-agents">No agents available.</p>');
                    clearForm();
                });
            }
            $("#tour-next-step").on("click", function() {
                window.location.href = "#locations";
                if (window.currentStep.position <= 2) {
                    window.currentStep = {
                        tab: "#locations",
                        position: 3
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#location-tab').addClass("selected");
            });
        }
    },

    getLocationsTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 3) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start();
            }
            $("#tour-next-step").on("click", function() {
                window.location.href = "#boats";
                if (window.currentStep.position <= 3) {
                    window.currentStep = {
                        tab: "#boats",
                        position: 4
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#boat-tab').addClass("selected");
            });
        }
    },

    getBoatsTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 4) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {

                        case "change-to-add-boatroom":
                            $("#boatroom-list").append('<li id="dummy-boatroom"><strong>Single Cabin</strong></li>');
                            break;

                        case "boat-form-container":
                            $("#boat-name").val("Barry's big boat");
                            //CKEDITOR.setData("Add a description of your boat here."); 
                            $("#boat-capacity").val(25);
                            break;

                        case "boat-cabins":
                            $("#room-types").append('<p id="cabin-option"> \
			        		<select class="room-type-select"> \
			        		<option value="{{id}}">Single Cabin</option> \
			        		</select> Number of rooms: \
			        		<input type="number" value="6" placeholder="0" style="width: 100px;" min="0"> \
			        		<button class="btn btn-danger remove-room">&#215;</button> \
			        		</p>');
                            break;

                        case "boats-list-div":
                            $("#no-boats").remove();
                            $("#cabin-option").remove();
                            $("#boat-list").append('<li id="dummy-boat"><strong>Barrys big boat</strong> | Capacity: 25</li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-boat").remove();
                    $("#boat-list").append('<p id="no-boats">No boats available.</p>');
                    clearForm();
                });
            }

            $("#tour-next-step").on("click", function() {
                if (window.boats.length != 0) {
                    window.location.href = "#trips";
                    if (window.currentStep.position <= 4) {
                        window.currentStep = {
                            tab: "#trips",
                            position: 5
                        };
                    }
                    $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                    $('#trip-tab').addClass("selected");
                } else alert("You need to add atleast one boat");
            });

        }
    },

    getTripsTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 5) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "trip-form-container":
                            $("#trip-name").val("Single boat dive");
                            $("#tripDuration").val(4);
                            break;
                        case "locationsList":
                            $('#locationsList').find('.location').filter(':first').click();
                            break;
                        case "tagsList":
                            $('#tagsList').find('.tag').filter(':first').click();
                            break;
                        case "trips-list-div":
                            $("#no-trips").remove();
                            $("#trip-list").append('<li id="dummy-trip"><strong>Single boat dive</strong> | 0d 4h </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-trip").remove();
                    $("#trip-list").append('<p id="no-trips">No trips available.</p>');
                    clearForm();
                });
            }
            $("#tour-next-step").on("click", function() {
                if (window.trips.length != 0) {
                    window.location.href = "#tickets";
                    if (window.currentStep.position <= 5) {
                        window.currentStep = {
                            tab: "#tickets",
                            position: 6
                        };
                    }
                    $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                    $('#ticket-tab').addClass("selected");
                } else alert("You need to add atleast one ticket");
            });
        }
    },

    getTicketsTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 6) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "ticket-form-container":
                            $("#ticket-name").val("2 dive boat trip");
                            $("#ticket-base").val(50);
                            break;
                        case "tickets-seasonal":
                            $"#seasonal-prices-checkbox").click();
                            break;
                        case "tickets-boats":
                            $("#tickets-boats-checkbox").click();
                            break;
                        case "tickets-boatrooms":
                            $("#tickets-boatroom-checkbox").click();
                            break;
                        case "tickets-list-div":
                            $("#no-tickets").remove();
                            $("#ticket-list").append('<li id="dummy-ticket"><strong>New diving trip</strong> | £50.00 </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-ticket").remove();
                    $("#ticket-list").append('<p id="no-tickets">No tickets available.</p>');
                    clearForm();
                });
            }

            $("#tour-next-step").on("click", function() {
                if (window.tickets.length != 0) {
                    window.location.href = "#classes";
                    if (window.currentStep.position <= 6) {
                        window.currentStep = {
                            tab: "#classes",
                            position: 7
                        };
                    }
                    $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                    $('#class-tab').addClass("selected");
                } else pageMssg("Please add atleast one ticket");
            });
        }
    },

    getClassesTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 7) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "class-form-container":
                            $("#class-name").val("Open Water Theory");
                            $("#trip-duration").val(5);
                            break;
                        case "class-list-div":
                            $("#no-classes").remove();
                            $("#class-list").append('<li id="dummy-class"><strong>Open Water Theory</strong> | £10.00 </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-class").remove();
                    $("#class-list").append('<p id="no-classes">No classes available.</p>');
                    clearForm();
                });
            }

            $("#tour-next-step").on("click", function() {
                window.location.href = "#courses";
                if (window.currentStep.position <= 7) {
                    window.currentStep = {
                        tab: "#courses",
                        position: 8
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#course-tab').addClass("selected");
            });
        }
    },

    getCoursesTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 8) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "course-form-container":
                            $("#course-name").val("PADI open water");
                            $("#course-capacity").val(10);
                            //$("#course-description").val("Beginers course to diving");
                            break;
                        case "course-list-div":
                            $("#no-courses").remove();
                            $("#course-list").append('<li id="dummy-course"><strong>PADI open water</strong> | £10.00 </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-course").remove();
                    //$("#course-list").append('<p id="no-courses">No courses available.</p>');
                    clearForm();
                });
            }

            $("#tour-next-step").on("click", function() {
                window.location.href = "#add-ons";
                if (window.currentStep.position <= 8) {
                    window.currentStep = {
                        tab: "#add-ons",
                        position: 9
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#addon-tab').addClass("selected");
            });
        }
    },

    getAddonsTour: function() {
        if (window.tourStart) {
            $("#tour-next-step").hide();
            $("#tour-finish").show();
            if (window.currentStep.position < 9) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "addon-form-container":
                            $("#addon-name").val("Reef diving tax");
                            $("#addon-price").val(10);
                            break;
                        case "addon-compulsory-div":
                            $("#addon-compulsory").attr("checked", true);
                            break;
                        case "addon-list-div":
                            $("#no-addons").remove();
                            $("#addon-list").append('<li id="dummy-addon"><strong>Reef diving tax</strong> | £10.00 </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-addon").remove();
                    $("#addon-list").append('<p id="no-addons">No addons available.</p>');
                    clearForm();
                });
            }

            $("#tour-next-step").on("click", function() {
                window.location.href = "#packages";
                if (window.currentStep.position <= 9) {
                    window.currentStep = {
                        tab: "#packages",
                        position: 10
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#package-tab').addClass("selected");
            });
        }
    },

    getPackagesTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 10) {
                window.location.href = window.currentStep.tab;
                console.log(window.currentStep.tab);
            } else {
                //$("#tour-button").empty();
                $("#tour-next-step").hide();
                $("#tour-finish").show();
                //$("#tour-button").append('<button id="tour-finish" class="btn btn-success text-uppercase">Finish tour</button>');
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "package-form-container":
                            $("#package-name").val("Family dive day");
                            break;
                        case "package-tickets":
                            $("#package-tickets").find(".ticket-select").filter(":first").val(1);
                            $("#package-tickets").find(".quantity-input").filter(":first").val(4);
                            break;
                        case "package-base":
                            $("#package-base").find(".base-price").filter(":first").val(150);
                            break;
                        case "package-seasonal":
                            $("#package-seasonal").find('input[type=checkbox]').filter(':first').click();
                            break;
                        case "package-size":
                            $("#package-capacity").val(4);
                            break;
                        case "packages-list-div":
                            $("#no-packages").remove();
                            $("#package-list").append('<li id="dummy-package"><strong>Family dive day</strong> | £150.00 </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-package").remove();
                    $("#package-list").append('<p id="no-packages">No packages available.</p>');
                    clearForm();
                });
            }

            $("#tour-finish").click(function(event) {
                var params = {
                    _token: window.token
                };
                Company.initialise(params, function success(data) {
                    //$('#addon-tab').addClass("done");
                    pageMssg("Thank you for following our wizard. Your system is now fully configured.", true);
                    setTimeout(function() {
                        window.location.href = "#dashboard";
                        window.location.reload(true);
                    }, 3000);
                });
            });

        }
    }

};*/
