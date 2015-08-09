// Interactions with the API
// Shim for up to IE8
if (!Date.now) {
	Date.now = function() { return new Date().getTime(); }
}

$.ajaxSetup({
	beforeSend: function(xhr, options) {
		// Disable caching for API requests by default
		if(options.url.substr(0, 4) === '/api' && options.type !== 'POST') {
			$.extend(this, {
				url: options.url + (options.url.indexOf('?') === -1 ? '?_=' : '&_=') + Date.now(),
				cache: false,
			});
		}

		// Enable caching for .js scripts by default
		/*else if(options.dataType === 'script') {
			// Remove '?_={random number}'' from the request url
			$.extend(this, {
				url: options.url.split('?_=')[0],
				cache: true,
			});
}*/

		// Manually trigger progress bar for tab loads, which have been set to global:false
		if(options.url.indexOf('index.php') > -1)
			NProgress.start();

		// Since the help tab does not include API requests, the progress bar needs to be manually stopped
		if(options.url.indexOf('help/index.php') > -1)
			$.extend(this, {
				complete: function() {
					NProgress.done();
				}
			});
	}
});

// Set up hearbeats to fire every minute
window.setInterval(function() {
       Company.sendHeartbeat({'h': 1});
}, 60000);

// Run on page load
$(function(){

	// Error handling
	$(document).ajaxComplete(function(event, xhr, options) {
		if(xhr.status >= 400) {
			// pageMssg('<strong>' + xhr.status + ' ' + xhr.statusText + '</strong> - No separate error message? Contact the developer!', 'info');
		}

		if(xhr.status === 503) {
			// Maintenance mode
			pageMssg('<strong>The application is in maintenance mode.</strong> Please check back in a few minutes.', 'warning');
		}
	});

	$(document).ajaxStart(function() {
		NProgress.start();
		var interval = 400 + Math.random() * 400;
		window.sw.nProgressInterval = window.setInterval(function(){NProgress.inc();}, interval);
	});
	$(document).ajaxStop(function() {
		window.clearInterval(window.sw.nProgressInterval);
		NProgress.done();
	});

	Company.getNotifications(function sucess(data) {
		var notificationTemplate = Handlebars.compile( $("#notification-message-template").html() );
		window.notifications = createNotifications(data);
		$('#notification-messages').append(notificationTemplate({notifications : window.notifications}));
	});

	$(".notifications .messages").hide();
	$(".notifications").click(function() {
		if($(this).children(".messages").children().length > 0) {
			$(this).children(".messages").fadeToggle(300);
		}
	});

	$("#logout").click(function(e){
		$.ajax({
			url: "/api/logout",
			type: "GET",
			dataType: "json",
			success: function(log) {

				location = '/';

				window.location.href = location;
			}
		});
		e.preventDefault();
	});

	//token
	if(typeof window.token === 'undefined')
		getToken();
});

//************************************
// FUNCTIONS
//************************************
var tokenRequestUnderway = false;
var tokenRequestCallbacks = [];

function getToken(callback) {
	if(typeof window.token === 'string' && window.token.length > 0) {
		if(typeof callback === 'function') callback(window.token);
		return window.token;
	}

	tokenRequestCallbacks.push(callback);

	if(tokenRequestUnderway) {
		return false;
	}

	window.tokenRequestUnderway = true;

	$.ajax({
		url: "/api/token",
		type: "GET",
		success: function(data){
			window.token = data;
			window.tokenRequestUnderway = false;

			for(var i = 0; i < tokenRequestCallbacks.length; i++) {
				if(typeof tokenRequestCallbacks[i] === 'function') tokenRequestCallbacks[i](window.token);
			}
		}
	});

	return false;
}

function setToken(element) {
	getToken(function(token) {
		$(element).val(token);
	});
}

function reproColor(id) { // Stands for: reproducible color

	// Colors from http://clrs.cc

	var colors = [ /* 14 options */
	{bgcolor: '#001F3F', txtcolor: '#FFFFFF'}, /* navy */
	{bgcolor: '#0074D9', txtcolor: '#FFFFFF'}, /* blue */
	{bgcolor: '#7FDBFF', txtcolor: '#000000'}, /* aqua */
	{bgcolor: '#39CCCC', txtcolor: '#000000'}, /* teal */
	{bgcolor: '#3D9970', txtcolor: '#000000'}, /* olive */
	{bgcolor: '#2ECC40', txtcolor: '#000000'}, /* green */
	{bgcolor: '#01FF70', txtcolor: '#000000'}, /* lime */
	{bgcolor: '#FFDC00', txtcolor: '#000000'}, /* yellow */
	{bgcolor: '#FF851B', txtcolor: '#000000'}, /* orange */
	{bgcolor: '#FF4136', txtcolor: '#FFFFFF'}, /* red */
	{bgcolor: '#85144B', txtcolor: '#FFFFFF'}, /* maroon */
	{bgcolor: '#F012BE', txtcolor: '#FFFFFF'}, /* fuchsia */
	{bgcolor: '#B10DC9', txtcolor: '#FFFFFF'}, /* purple */
	{bgcolor: '#DDDDDD', txtcolor: '#000000'}, /* silver */
	];

	var length = colors.length;

	if(id === undefined) // return default
		return colors[0];

	return colors[ (id % length) ];
}

function createNotifications(data) {
	// handle data from api call and create notification messages
	var notifications = [];
	// console.log(data);

	// check if they have done the tour
	if(data.init) notifications.push(data.init);

	// check outstanding bookings
	for(var i = 0; i < data.overdue.length; i++) {
		notifications.push("Booking " + data.overdue[i][0] + " has " + window.company.currency.symbol + data.overdue[i][1] + " outstanding to pay");
	}

	// check bookings expiring within 30 minutes
	for(var i = 0; i < data.expiring.length; i++) {
		notifications.push("Booking " + data.expiring[i][0] + " is going to expire soon!");
	}

	return notifications;
}

function colorOpacity(hex, opa) {

	// validate hex string
	hex = String(hex).replace(/[^0-9a-f]/gi, '');
	if (hex.length < 6) {
		hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
	}
	opa = opa || 1;

	// convert to decimal and change luminosity
	var rgb = "rgba(", c, i;
		for (i = 0; i < 3; i++) {
			c = parseInt(hex.substr(i*2,2), 16);
			rgb += c + ', ';
		}
		rgb += opa + ')';

return rgb;
}

window.sw.randomStrings = [];
function randomString() {
	var chars         = "ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 15;
	var result        = '';

	for (var i = 0; i < string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		result += chars.substring(rnum, rnum+1);
	}

	if(_.indexOf(window.sw.randomStrings, result) >= 0)
	{
		// If the random string is not unique (unlikely, but possible) the function recursively calls itself again
		return randomString();
	}

	// When the random string has been approved as unique, it is added to the list of generated strings and then returned
	window.sw.randomStrings.push(result);
	return result;
}

Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {

	switch (operator) {
		case '==':
		return (v1 == v2)  ? options.fn(this) : options.inverse(this);
		case '!=':
		return (v1 != v2)  ? options.fn(this) : options.inverse(this);
		case '===':
		return (v1 === v2) ? options.fn(this) : options.inverse(this);
		case '!==':
		return (v1 !== v2) ? options.fn(this) : options.inverse(this);
		case '<':
		return (v1 < v2)   ? options.fn(this) : options.inverse(this);
		case '<=':
		return (v1 <= v2)  ? options.fn(this) : options.inverse(this);
		case '>':
		return (v1 > v2)   ? options.fn(this) : options.inverse(this);
		case '>=':
		return (v1 >= v2)  ? options.fn(this) : options.inverse(this);
		case '&&':
		return (v1 && v2)  ? options.fn(this) : options.inverse(this);
		case '||':
		return (v1 || v2)  ? options.fn(this) : options.inverse(this);
		default:
		return options.inverse(this);
	}
});

Handlebars.registerHelper('unlessCond', function (v1, operator, v2, options) {

	switch (operator) {
		case '==':
		return (v1 == v2) ? options.inverse(this) : options.fn(this);
		case '===':
		return (v1 === v2) ? options.inverse(this) : options.fn(this);
		case '<':
		return (v1 < v2) ? options.inverse(this) : options.fn(this);
		case '<=':
		return (v1 <= v2) ? options.inverse(this) : options.fn(this);
		case '>':
		return (v1 > v2) ? options.inverse(this) : options.fn(this);
		case '>=':
		return (v1 >= v2) ? options.inverse(this) : options.fn(this);
		case '&&':
		return (v1 && v2) ? options.inverse(this) : options.fn(this);
		case '||':
		return (v1 || v2) ? options.inverse(this) : options.fn(this);
		default:
		return options.inverse(this);
	}
});

function decRound(number, places) {

	if(places < 1) return Math.round(number);

	return Math.round(number * Math.pow(10, places)) / Math.pow(10, places);
}

$(function() {
    var newHash      = "",
        $mainContent = $("#content"),
        $pageWrap    = $("#page-wrap"),
        baseHeight   = 0,
        $el;

    /*
    $pageWrap.height($pageWrap.height());
    baseHeight = $pageWrap.height() - $mainContent.height();
    */

    $(window).on('hashchange', function() {

        newHash = window.location.hash.substring(1); // Fetch hash without #

        // Default to dashboard when no hash found
        if(newHash === '') {
            window.location.hash = 'dashboard';
            return;
        }

        // Prepare deferred
        var tabLoaded = $.Deferred();

        // Fire off AJAX to load new content
        var newContentUrl = "tabs/" + newHash + "/index.php";
        $.ajax({
            url: newContentUrl,
            type: "GET",
            global: false, // Do not trigger global ajax events. Helps prevent double flash of progress bar when loading a tab.
            success: function(data) {
                tabLoaded.resolve(data);
            },
        });

        // Set live tab
        $('.tab-active').removeClass('tab-active');
        $('#sidenav a[href="#'+newHash+'"]').parent().addClass('tab-active');

        // Open submenu if one of its tabs is selected
        var submenuCalendar = [
            'calendar',
            'scheduling',
            'pickup-schedule'
        ];
        var submenuManagement = [
            'accommodations',
            'add-ons',
            'agents',
            'classes',
            'courses',
            'boats',
            'locations',
            'packages',
            'activate-trip',
            'tickets',
            'trips'
        ];

        if(submenuCalendar.indexOf(newHash) !== -1)   $('#calendar-submenu').css('display', 'block');
        if(submenuManagement.indexOf(newHash) !== -1) $('#management-submenu').css('display', 'block');

        // Blend out old content and display new content
        $mainContent.find('#wrapper').fadeOut(200, function() {
            $('#wrapper').remove();

            if(tabLoaded.state() === "pending")
                $mainContent.html(LOADER);

            tabLoaded.done(function(html) {
                $mainContent.html(html);
            });
        });

        // Get the page title from the menu item
        var newTitle = $('#sidenav a[href="#'+newHash+'"]').text();

        // Set breadcumb(s)
        if(newHash === 'add-transaction')
            newTitle = '<a href="#manage-bookings">Manage Bookings</a> <small><i class="fa fa-chevron-right fa-fw text-muted"></i></small> Add Transaction';
        $("#breadcrumbs").html('<a href="#dashboard" class="breadcrumbs-home"><i class="fa fa-home fa-lg fa-fw"></i></a> <small><i class="fa fa-chevron-right fa-fw text-muted"></i></small> ' + newTitle);

        window.scrollTo(0, 0);

        // Send navigation event
        getToken(function() {
            Company.sendHeartbeat({'n': 1});
        });
    });

    // Trigger content loading on initial dashboard load
    $(window).trigger('hashchange');
});

/* ACCORDION NAVIGATION */
$(function(){
    //function fires if any of the nav-items tags are clicked
    $( "#sidenav > li > div" ).click(function(){
        //show child list if not already shown
        if ($(this).parent().children().is( ":hidden" ) ) {
            $( $( this ).parent().children( "ul" ) ).slideDown( "fast" );
            //set arrow to up
            $( $(this).children( ".caret" ) ).css('transform', 'rotate(0deg)');
        } else {
            //list already on show so slide it back up
            $( $( this ).parent().children( "ul" ) ).slideUp( "fast" );
            //set arrow to down
            $( $( this ).children( ".caret" ) ).css('transform', 'rotate(-90deg)');
        }
    });
});

var Tour = {

	getAcommodationsTour : function() {
		if(window.tourStart) {
			$("#tour-next-step").show();
			$("#tour-finish").hide();
			introJs().setOptions( {
				showStepNumbers : false,
				exitOnOverlayClick : false,
				exitOnEsc : false
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
			    	if(window.currentStep.position <= 1) {
			    		window.currentStep = {
			    			tab : "#agents",
			    			position : 2
			    		};
			    	}
					$('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
					$('#agent-tab').addClass("selected");
			    });
			}
		},

		getAgentsTour : function() {
			if(window.tourStart) {
				if(window.currentStep.position < 2) {
					window.location.href = window.currentStep.tab;
				} else {
					$("#tour-next-step").show();
					$("#tour-finish").hide();
					introJs().setOptions( {
						showStepNumbers : false,
						exitOnOverlayClick : false,
						exitOnEsc : false
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
					if(window.currentStep.position <= 2) {
						window.currentStep = {
							tab : "#locations",
							position : 3
						};
					}
					$('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
					$('#location-tab').addClass("selected");
				});
			}
		},

		getLocationsTour : function() {
			if(window.tourStart) {
				if(window.currentStep.position < 3) {
					window.location.href = window.currentStep.tab;
				} else { 
					$("#tour-next-step").show();
					$("#tour-finish").hide();
					introJs().setOptions( {
						showStepNumbers : false,
						exitOnOverlayClick : false,
						exitOnEsc : false
					}).start();
				}
				$("#tour-next-step").on("click", function() {
					window.location.href = "#boats";
					if(window.currentStep.position <= 3) {
						window.currentStep = {
							tab : "#boats",
							position : 4
						};
					}
					$('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
					$('#boat-tab').addClass("selected");
				});
			}
		},

		getBoatsTour : function() {
			if(window.tourStart) {
				if(window.currentStep.position < 4) {
					window.location.href = window.currentStep.tab;
				} else {
					$("#tour-next-step").show();
					$("#tour-finish").hide();
					introJs().setOptions( {
						showStepNumbers : false,
						exitOnOverlayClick : false,
						exitOnEsc : false
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
				if(window.boats.length != 0) {
					window.location.href = "#trips";
					if(window.currentStep.position <= 4) {
						window.currentStep = {
							tab : "#trips",
							position : 5
						};
					}
					$('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
					$('#trip-tab').addClass("selected");
				} else alert("You need to add atleast one boat");
			});

		}
	},

	getTripsTour : function() {
		if(window.tourStart) {
			if(window.currentStep.position < 5) {
				window.location.href = window.currentStep.tab;
			} else {
				$("#tour-next-step").show();
				$("#tour-finish").hide();
				introJs().setOptions( {
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false
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
				if(window.trips.length != 0) {
					window.location.href = "#tickets";
					if(window.currentStep.position <= 5) {
						window.currentStep = {
							tab : "#tickets",
							position : 6
						};
					}
					$('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
					$('#ticket-tab').addClass("selected");
				} else alert("You need to add atleast one ticket");
			});
		}
	},

	getTicketsTour : function() {
		if(window.tourStart) {
			if(window.currentStep.position < 6) {
				window.location.href = window.currentStep.tab;
			} else {
				$("#tour-next-step").show();
				$("#tour-finish").hide();
				introJs().setOptions( {
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false
				}).start().onchange(function(targetElement) {
					switch (targetElement.id) {  
						case "ticket-form-container":
						$("#ticket-name").val("2 dive boat trip");
						$("#ticket-base").val(50);
						break;
						case "tickets-seasonal":
						$("#seasonal-prices-checkbox").click();
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
				if(window.tickets.length != 0) {
					window.location.href = "#classes";
					if(window.currentStep.position <= 6) {
						window.currentStep = {
							tab : "#classes",
							position : 7
						};
					}
					$('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
					$('#class-tab').addClass("selected");
				} else pageMssg("Please add atleast one ticket");
			});
		}
	},

	getClassesTour : function() {
		if(window.tourStart) {
			if(window.currentStep.position < 7) {
				window.location.href = window.currentStep.tab;
			} else {
				$("#tour-next-step").show();
				$("#tour-finish").hide();
				introJs().setOptions( {
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false
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
				if(window.currentStep.position <= 7) {
					window.currentStep = {
						tab : "#courses",
						position : 8
					};
				}
				$('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
				$('#course-tab').addClass("selected");
			});
		}
	},

	getCoursesTour : function() {
		if(window.tourStart) {
			if(window.currentStep.position < 8) {
				window.location.href = window.currentStep.tab;
			} else {
				$("#tour-next-step").show();
				$("#tour-finish").hide();
				introJs().setOptions( {
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false
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
				if(window.currentStep.position <= 8) {
					window.currentStep = {
						tab : "#add-ons",
						position : 9
					};
				}
				$('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
				$('#addon-tab').addClass("selected");
			});
		}
	},

	getAddonsTour : function() {
		if(window.tourStart) {
			$("#tour-next-step").hide();
			$("#tour-finish").show();
			if(window.currentStep.position < 9) {
				window.location.href = window.currentStep.tab;
			} else {
				$("#tour-next-step").show();
				$("#tour-finish").hide();
					introJs().setOptions( {
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false
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
				if(window.currentStep.position <= 9) {
					window.currentStep = {
						tab : "#packages",
						position : 10
					};
				}
				$('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
				$('#package-tab').addClass("selected");
			});
		}
	},

	getPackagesTour : function() {
		if(window.tourStart) {
			if(window.currentStep.position < 10) {
				window.location.href = window.currentStep.tab;
				console.log(window.currentStep.tab);
			} else {
				$("#tour-button").empty();
				$("#tour-button").append('<button id="tour-finish" class="btn btn-success text-uppercase">Finish tour</button>');
				introJs().setOptions( {
					showStepNumbers : false,
					exitOnOverlayClick : false,
					exitOnEsc : false
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
				var params = { _token : window.token };
				Company.initialise(params, function success(data) {
					$('#addon-tab').addClass("done");
					pageMssg("Thank you for following our wizard. Your system is now fully configured.", true);
					setTimeout(function () {
				       window.location.href = "#dashboard";
				       window.location.reload(true);
				    }, 3000);
				});
			});
			
		}
	}

};
var LOADER = '<div class="loader" style="left: 50%; margin-left: -13px; margin-top: 10em;"></div>';

$(function(){

	//click function used for in tab switch
	//content is loaded into section
	$('#guts').delegate(".switch-option", "click", function(){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 005 found"'); // 2015-02-18

		$('.switch-option').removeClass('option-active');
		$(this).addClass('option-active');

		//which section the switch is for
		var section = "#" + $(this).parent().attr("for");
		//get the load doc
		var doc = $(this).attr("id");
		//set the new content
		$(section).html(LOADER).load(doc);
	});


	//tooltip for hints
	$("body").on("focus", "[data-tooltip]", function() {
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 006 found"'); // 2015-02-20

    	var tooltip = $("[data-tooltip]").attr("data-tooltip");

    	//remove all other tool tips
    	$(".tooltip").remove();

    	//append the tooltip
    	$("[data-tooltip]").parent().append("<div class='tooltip'>"+tooltip+"</div>");

    	$(".tooltip").fadeIn("slow");

    	//get the inputs offset on page
    	var offset = $("[data-tooltip]").offset();

    	//get height of tooltip
    	var elHeight = $(".tooltip").height();

    	//set the new offset of tooltip
    	$( ".tooltip" ).offset({ top: (offset.top - 40 - elHeight), left: offset.left });
	});

	//tooltip for hints
	$("body").on("focusout", "[data-tooltip]", function() {
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 007 found"'); // 2015-02-20
		//remove all tool tips
		$(".tooltip").fadeOut("slow");
    	/* $(".tooltip").remove(); */
	});

	/*
	* Datepicker
	*/

	$('input.datetimepicker').datetimepicker({
		pickDate: true,
		pickTime: true,
		minuteStepping: 5,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
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
	});

	$('input.timepicker').datetimepicker({
		pickDate: false,
		pickTime: true,
		minuteStepping: 5,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
														//BOX FUNCTIONS
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	//EXPANDABLE BOX / SPACE-SAVER BOX
	$("body").delegate(".expand-box-arrow", "click", function(){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 008 found"'); // 2015-02-20
		$(this).parent().parent().children(".expandable").slideToggle();
		$(this).toggleClass("rotate");
	});

	//DELETABLE BOX
	$("body").delegate(".del-box", "click", function(){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 009 found"'); // 2015-02-20
		if($(this).isSure()){
			$(this).parent().parent().smoothRemove(function() {
				// Trigger saveAll
				$('#saveAll').click();
			});
		}
	});
});

function initPriceDatepickers() {
	$('input.datepicker').not('.datepicker-initiated').addClass('datepicker-initiated').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});
}

function checkDefaultSwitches(){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 001 found"'); // 2014-12-31
	//if there is a switch on the page set its default content
	//do when the new content is loaded
	if($(".option-active").length > 0){
		var activeOptions = $(".option-active");
		activeOptions.each(function() {
	    //which section the switch is for
				var section = "#" + $(this).parent().attr("for");
				//get the load doc
				var doc = $(this).attr("id");
				//set the new content
				$(section).html(LOADER).load(doc);
		});
	}
}

$.fn.isSure = function(){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 002 found"'); // 2014-12-31
	var sure = true;

	if($(this).attr("data-sure")){
		sure = confirm($(this).attr("data-sure"));
	}

	return sure;
};

$.fn.smoothRemove = function(callback){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 003 found"'); // 2014-12-31
    $(this).animate({height: 0, opacity: 0}, 'slow', function() {
        $(this).remove();

        if(callback !== undefined && typeof callback === "function")
        {
        	callback();
        }
    });
};

//display error message for use when validating form
$.fn.errorMssg = function(mssg){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 004 found"'); // 2014-12-31
    $(this).after("<div class='errorMssg'>" + mssg + "</div>");
};

function pageMssg(message, type, dismissable) {

	if(typeof type === 'undefined')
		type = 'danger';
	if(typeof type === 'boolean')
		type = 'success';

	if(typeof dismissable === 'undefined')
		dismissable = false;

	var el = '<div class="findMe alert alert-' + type + ' border-' + type + (dismissable ? ' alert-dismissable' : '') + '" role="alert">';

	if(dismissable)
		el += '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';

	switch(type) {
		case 'success': el += '<i class="fa fa-check fa-lg fa-fw"></i> '; break;
		case 'info':    el += '<i class="fa fa-info fa-lg fa-fw"></i> ';  break;
		case 'warning': el += '<i class="fa fa-exclamation fa-lg fa-fw"></i> '; break;
		case 'danger':  el +=  '<i class="fa fa-times fa-lg fa-fw"></i> '; break;
	}

	el += message;

	el += '</div>';

	$('#pageMssg').append(el).find('.findMe').removeClass('findMe').fadeIn(400, function() {
		if($(this).hasClass('alert-dismissable'))
			return;

		var self = this;

		setTimeout(function() {
			$(self).fadeOut(400, function() {
				$(this).remove();
			});
		},3000);
	});
}

/*
$.fn.validateForm = function(){
	$($(this).children(".valid")).each(function(){
*/
		/* $(this).validate(); */
/*
		console.log(this);
	});
}
*/

/*
$.fn.validate = function(){
	var minLen, maxLen, needsNum;

	if($(this).attr("data-min")){
		mminLen = $(this).attr("data-min");
	}

	if($(this).attr("data-max")){
		maxLen = $(this).attr("data-max");
	}

	if($(this).attr("data-needs-num")){
		needsNum = true;
	}
	console.log(minLen + " " + maxLen + " " + needsNum);

}
*/

$.fn.validateField = function(min, max){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 010 found"'); // 2015-02-20
	var bool = true;
	var val = $(this).val();

	//reset each to no error
	$(this).validationAction(bool);

	//check if it has a value at all
	if(val){
		//it has a value, so check it..
		if((val.length >= min) && (val.length <= max)){
			//all good
		}else{
			//error
			bool = false;
		}
	}else{ bool = false; }

	//set to error if there is an error
	$(this).validationAction(bool);


	return bool;
};

$.fn.validateNumericField = function(min, max){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 011 found"'); // 2015-02-20
	var bool = true;
	var val = $(this).val();

	//reset each to no error
	$(this).validationAction(bool);

	//check if it has a value at all
	//and is a number
	if((val) && ($.isNumeric(val))){
		//check if min and max are set
		if(min && max){
			//it has a value, so check it..
			if((val >= min) && (val <= max)){
				//all good
			}else{
				//error
				bool = false;
			}
		}//no? thats it then.
	}else{ bool = false; }

	$(this).validationAction(bool);

	return bool;
};

//displays error mssg if bool == false
$.fn.validationAction = function(bool){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 012 found"'); // 2015-02-20
	if(bool === true){
		$(this).css("border-color", "");
	}else{
		$(this).css("border-color", "red");
	}

};

var Accommodation = {

	get : function(params, handleData) {
		$.get("/api/accommodation", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/accommodation/all", handleData);
	},

	getAllWithTrashed : function(handleData) {
		$.get("/api/accommodation/all-with-trashed", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/accommodation/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/accommodation/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/accommodation/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	filter : function(params, handleData, errorFn){
		$.ajax({
			type: "GET",
			url: "/api/accommodation/filter",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};

var Addon = {

	getAddon : function(params, handleData) {
		$.get("/api/addon", params, function(data) {
			handleData(data);
		});
	},

	getAllAddons : function(handleData) {
		$.get("/api/addon/all", function(data){
			handleData(data);
		});
	},

	createAddon : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/addon/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateAddon : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/addon/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deleteAddon : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/addon/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};

var Agency = {

	getAll : function(handleData) {
		$.get("/api/agency/all", function(data){
			handleData(data);
		});
	}
};

var Agent = {

	getAgent : function(params, handleData) {
		$.get("/api/agent", params, function(data) {
			handleData(data);
		});
	},

	getAllAgents : function(handleData) {
		$.get("/api/agent/all", function(data){
			handleData(data);
		});
	},

	createAgent : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/agent/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateAgent : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/agent/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	/*
	deleteAgent : function(params, handleData){
		$.post("/api/agent/delete", params, function(data){
			handleData(data);
		});
	}
	*/
};

var Boat = {

	get : function(params, handleData) {
		$.get("/api/boat", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/boat/all", handleData);
	},

	getAllWithTrashed : function(handleData) {
		$.get("/api/boat/all-with-trashed", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boat/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boat/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/boat/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};

var Boatroom = {

	get : function(params, handleData) {
		$.get("/api/boatroom", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/boatroom/all", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boatroom/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/boatroom/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/boatroom/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};

var Booking = function(data) {

	// Defaults for new booking
	this.decimal_price  = "0.00";
	this.discount       = "0.00";
	this.lead_customer  = false;
	this.bookingdetails = [];
	this.accommodations = [];
	this.payments       = [];
	this.refunds        = [];

	// User interface variables
	this.currentTab        = null;
	this.selectedTickets   = {};
	this.selectedPackages  = {};
	this.selectedCourses   = {};
	this.selectedCustomers = {};
	this.sums              = {};

	if(data !== undefined) {
		$.extend(this, data);

		this.setStatus();
	}

	this.calculateSums();
};


/*
 ********************************
 ******* STATIC FUNCTIONS *******
 ********************************
 */

/**
 * Takes the required booking's ID and calls the success callback with a Booking object as its only parameter
 *
 * @param {integer} id The ID of te required session
 * @param {function} successFn Recieves new Booking object as first and only parameter
 */
Booking.get = function(id, successFn) {
	$.get("/api/booking", {id: id}, function(data) {
		successFn( new Booking(data) );
	});
};

/*
 * Calls success callback with unaltered JSON data
 */
Booking.getAll = function(successFn) {
	$.get("/api/booking/all", successFn);
};

Booking.getRecent = function(successFn, errorFn) {
	$.ajax({
		type: "GET",
		url: "/api/booking/all/0/5",
		success: successFn,
		error: errorFn
	});
};

Booking.today = function(successFn, errorFn) {
	$.ajax({
		type: "GET",
		url: "/api/booking/today",
		success: successFn,
		error: errorFn
	});
};

Booking.tomorrow = function(successFn, errorFn) {
	$.ajax({
		type: "GET",
		url: "/api/booking/tomorrow",
		success: successFn,
		error: errorFn
	});
};

Booking.filter = function(params, successFn, errorFn) {
	$.ajax({
		type: "GET",
		url: "/api/booking/filter",
		data: params,
		success: successFn,
		error: errorFn
	});
};

Booking.getCustomerBookings = function(params, successFn, errorFn) {
	$.ajax({
		type: "GET",
		url: "/api/booking/customerbookings",
		data: params,
		success: successFn,
		error: errorFn
	});
};

Booking.pickUpLocations = function(params, success) {
	$.get("/api/booking/pick-up-locations", params, success);
};

Booking.initiateStorage = function() {
	window.basil = new window.Basil({
		namespace: 'bookings',
		storages: ['local', 'cookie'], // Only use persistent storages
	});
};

/**
 * Cancels a booking.
 * Cancelled bookings DO NOT count towards sessions' utilisation
 *
 * @param  {object} params    Must contain:
 * - _token
 * - booking_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.cancel = function(params, successFn, errorFn) {

	$.ajax({
		type: "POST",
		url: "/api/booking/cancel",
		data: params,
		context: this,
		success: function(data) {
			successFn(data.status);
		},
		error: errorFn
	});
};


/*
 ********************************
 ******* PUBLIC FUNCTIONS *******
 ********************************
 */

/**
 * Save UI state to LocalStorage
 */
Booking.prototype.store = function() {
	if(typeof window.basil === 'undefined') Booking.initiateStorage();

	window.basil.set('booking_' + this.id, {
		selectedTickets   : this.selectedTickets,
		selectedCustomers : this.selectedCustomers,
		selectedPackages  : this.selectedPackages,
		selectedCourses   : this.selectedCourses,
		currentTab        : this.currentTab,
	});

	return true;
};

/**
 * Load UI state from LocalStorage and extend Booking object with it
 */
Booking.prototype.loadStorage = function() {
	if(typeof window.basil === 'undefined') Booking.initiateStorage();

	// $.extend(this, window.basil.get('booking_' + this.id));

	var storedObject = window.basil.get('booking_' + this.id);

	if(storedObject !== null) {
		// Only overwrite these attributes (other attributes could have changed on the server and are thus newer)
		this.selectedTickets   = storedObject.selectedTickets;
		this.selectedCustomers = storedObject.selectedCustomers;
		this.selectedPackages  = storedObject.selectedPackages;
		this.selectedCourses   = storedObject.selectedCourses ;
		this.currentTab        = storedObject.currentTab;
	}
};

/**
 * Initate a booking with either the 'source' of the booking or the 'agent_id'.
 * Source must be one of (telephone, email, facetoface).
 *
 * @param {object} Object containing _token and either source or agent_id. Examples:
 * - _token
 * - source (telephone, email, facetoface) || agent_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.initiate = function(params, successFn, errorFn) {
	$.ajax({
		type: "POST",
		url: "/api/booking/init",
		data: params,
		context: this,
		success: function(data) {
			this.id = data.id;
			this.reference = data.reference;

			this.source = params.source || null;
			this.agent_id = params.agent_id || null;
			this.agent_reference = params.agent_reference || null;

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Add a ticket/customer/package combo to a booking.
 *
 * @param {object} params      Must contain:
 * - _token
 * - customer_id
 * - ticket_id
 * - session_id
 * - package_id (optional)
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addDetail = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	// Determine whether we need to inject a packagefacade_id into the request
	if(typeof params.packagefacade_id === 'undefined' && typeof params.package_id !== 'undefined') {
		console.warn('WARNING: Potentially unexpected behaviour! - No packagefacade_id submitted. A new package will be assigned.');

		/*var existingDetail = _.find(this.bookingdetails, function(detail) {
			// First, test the customer_id
			if( detail.customer.id != params.customer_id )
				return false;

			// Next, check if a packagefacade exists
			if( typeof detail.packagefacade === 'undefined' || detail.packagefacade === null )
				return false;

			// Next, check if the packagefacade includes the requested package
			if( detail.packagefacade.package.id == params.package_id)
				return true;
		});
		if(typeof existingDetail !== 'undefined') { // _.find() returns `undefined` if no match is found
			console.info('Existing packagefacade_id detected: ' + existingDetail.packagefacade.id + ' - For package "' + existingDetail.packagefacade.package.name + '"');
			params.packagefacade_id = existingDetail.packagefacade.id;
		}
		else
			console.info('No packagefacade_id detected. Assigning new package.');*/
	}

	$.ajax({
		type: "POST",
		url: "/api/booking/add-detail",
		data: params,
		context: this,
		success: function(data) {
			var detail = {
				id: data.id,
				customer: window.customers[params.customer_id],
				session: params.session_id ? window.sessions[params.session_id] : null,
				ticket: params.ticket_id ? $.extend(true, {}, window.tickets[params.ticket_id]) : null, // Need to clone the ticket object, because we are going to write its decimal_price for the session's date in it
				course: params.course_id ? $.extend(true, {}, window.courses[params.course_id]) : null,
				training_session: params.training_session_id ? window.training_sessions[params.training_session_id] : null,
				addons: [], // Prepare the addons array to be able to just push to it later
			};

			if(params.package_id) {
				detail.packagefacade = {
					id: data.packagefacade_id,
					package: $.extend(true, {}, window.packages[params.package_id]),
				};
				detail.packagefacade.package.decimal_price = data.package_decimal_price;
			}
			else if(params.course_id) {
				detail.course.decimal_price = data.course_decimal_price;
			}
			else {
				detail.ticket.decimal_price = data.ticket_decimal_price;
			}

			if(data.boatroom_id)
				detail.boatroom_id = data.boatroom_id;

			// Add compulsory addons
			_.each(data.addons, function(id) {
				var addon = $.extend(true, {}, window.addons[id]);
				addon.pivot = {
					quantity: 1,
				};
				detail.addons.push(addon);
			});

			this.bookingdetails.push(detail);

			// If this is the first detail to be added and there is no lead customer yet, make this customer the lead customer
			if(!this.lead_customer && this.bookingdetails.count === 1) {
				this.lead_customer = window.customers[params.customer_id];
			}

			this.decimal_price = data.decimal_price;

			this.calculateSums();

			successFn(data.status, data.packagefacade_id);
		},
		error: errorFn
	});
};

/**
 * Remove a ticket/customer/package combo from a booking.
 * @param {object} params      Must contain
 * - _token
 * - bookingdetail_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.removeDetail = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/remove-detail",
		data: params,
		context: this,
		success: function(data) {

			var removedDetail = _.find(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			});

			this.bookingdetails = _.reject(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			});

			this.decimal_price = data.decimal_price;

			this.calculateSums();

			successFn(data.status, removedDetail);
		},
		error: errorFn
	});
};

/**
 * Sets the lead_customer_id for this booking
 * @param {object} params      Must contain
 * - _token
 * - customer_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.setLead = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/set-lead",
		data: params,
		context: this,
		success: function(data) {

			if(params.customer_id === null)
				this.lead_customer = false;
			else
				this.lead_customer = window.customers[ params.customer_id ];

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Adds an addon to the booking
 * @param {object} params      Must contain
 * - _token
 * - bookingdetail_id
 * - addon_id
 * - quantity (optional, default: 1)
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addAddon = function(params, successFn, errorFn){

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/add-addon",
		data: params,
		context: this,
		success: function(data) {

			var relatedBookingdetail = _.find(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			});

			// Check if the addon already exists
			var existingAddon = _.find(relatedBookingdetail.addons, function(addon) {
				return addon.id == params.addon_id && addon.pivot.packagefacade_id == (params.packagefacade_id || null);
			});

			if(existingAddon !== undefined) {
				// Increase quantity on existing addon
				existingAddon.pivot.quantity += parseInt(params.quantity);
			}
			else {
				var addon = $.extend(true, {}, window.addons[params.addon_id]);
				addon.pivot = {
					quantity: parseInt(params.quantity),
					packagefacade_id: params.packagefacade_id ? params.packagefacade_id : null,
				};
				relatedBookingdetail.addons.push( addon );
			}

			this.decimal_price = data.decimal_price;

			if(!params.packagefacade_id)
				this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Removes an addon from the booking
 * @param {object} params      Must contain
 * - _token
 * - bookingdetail_id
 * - addon_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.removeAddon = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/remove-addon",
		data: params,
		context: this,
		success: function(data) {

			var detail = _.find(this.bookingdetails, function(detail) {
				return detail.id == params.bookingdetail_id;
			});

			var removedAddon = _.find(detail.addons, function(addon) {
				return addon.id == params.addon_id && addon.pivot.packagefacade_id == (params.packagefacade_id || null);
			});

			if(removedAddon.pivot.quantity > 1) {
				// Reduce quantity by 1
				removedAddon.pivot.quantity--;
			}
			else {
				// Otherwise remove addon from the array
				detail.addons = _.reject(detail.addons, function(addon) {
					return addon.id == params.addon_id && addon.pivot.packagefacade_id == (params.packagefacade_id || null);
				});
			}

			this.decimal_price = data.decimal_price;

			if(removedAddon.pivot.packagefacade_id === null)
				this.calculateSums();

			successFn(data.status, removedAddon);
		},
		error: errorFn
	});
};

/**
 * Adds an accommodation to the booking
 * @param {object} params      Must contain
 * - _token
 * - accommodation_id
 * - customer_id
 * - start            (Date: YYYY-MM-DD)
 * - end              (Date: YYYY-MM-DD)
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addAccommodation = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/add-accommodation",
		data: params,
		context: this,
		success: function(data) {

			var accommodation = $.extend(true, {}, window.accommodations[params.accommodation_id]);
			accommodation.pivot = {
				start: params.start,
				end: params.end,
				customer_id: params.customer_id,
				packagefacade_id: data.packagefacade_id ? data.packagefacade_id : null,
			};

			accommodation.customer = window.customers[params.customer_id];
			accommodation.decimal_price = data.accommodation_decimal_price;

			this.accommodations.push( accommodation );

			this.decimal_price = data.decimal_price;

			if(!params.packagefacade_id)
				this.calculateSums();

			successFn(data.status, data.packagefacade_id);
		},
		error: errorFn
	});
};

/**
 * Removes an accommodation from the booking
 * @param {object} params      Must contain
 * - _token
 * - accommodation_id
 * - customer_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.removeAccommodation = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/remove-accommodation",
		data: params,
		context: this,
		success: function(data) {

			var removedAccommodation = _.find(this.accommodations, function(accommodation) {
				return accommodation.id == params.accommodation_id && accommodation.pivot.customer_id == params.customer_id && accommodation.pivot.start === params.start;
			});

			this.accommodations = _.reject(this.accommodations, function(accommodation) {
				return accommodation.id == params.accommodation_id && accommodation.pivot.customer_id == params.customer_id && accommodation.pivot.start === params.start;
			});

			this.decimal_price = data.decimal_price;

			if(removedAccommodation.pivot.packagefacade_id === null)
				this.calculateSums();

			successFn(data.status, removedAccommodation);
		},
		error: errorFn
	});
};

/**
 * Edits additional information about the booking
 * @param  {object} params    Can contain:
 * - _token
 * - pick_up_location {string}
 * - pick_up_time     {string} Must be formatted as 'YYYY-MM-DD HH:mm:ss'
 * - discount         {float}  The discount value gets substracted from the final booking price
 * - comment          {text}
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.editInfo = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/edit-info",
		data: params,
		context: this,
		success: function(data) {

			if(params.pick_up_location) this.pick_up_location = params.pick_up_location;
			if(params.pick_up_time)     this.pick_up_time     = params.pick_up_time;
			if(params.discount)         this.discount         = params.discount;
			if(params.comment)          this.comment          = params.comment;

			this.decimal_price = data.decimal_price;

			this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Reserves the booking until a specified date & time
 * ! Reserved bookings count towards sessions' utilisation !
 *
 * @param  {object} params    Must contain:
 * - _token
 * - reserved {string} The datetime until the booking should be reserved, in 'YYYY-MM-DD HH:MM:SS' format
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.reserve = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/reserve",
		data: params,
		context: this,
		success: function(data) {

			this.reserved = params.reserved;
			this.status = 'reserved';

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Saves the booking so it won't be automatically deleted and can be finished later
 * Saved bookings DO NOT count towards sessions' utilisation
 *
 * @param  {object} params    Must contain:
 * - _token
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.save = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/save",
		data: params,
		context: this,
		success: function(data) {

			this.status = 'saved';
			this.setStatus();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Confirms the booking (only possible for bookings by agent)
 *
 * @param  {object} params    Must contain:
 * - _token
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.confirm = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/confirm",
		data: params,
		context: this,
		success: function(data) {

			this.status = 'confirmed';
			this.setStatus();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Cancels the booking.
 * Cancelled bookings DO NOT count towards sessions' utilisation
 *
 * @param  {object} params    Must contain:
 * - _token
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.cancel = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/booking/cancel",
		data: params,
		context: this,
		success: function(data) {

			this.status = 'cancelled';
			this.setStatus();

			this.cancellation_fee = params.cancellation_fee;

			this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Adds a payment to the booking
 *
 * @param  {object} params    Must contain:
 * - _token
 * - amount
 * - paymentgateway_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addPayment = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/payment/add",
		data: params,
		context: this,
		success: function(data) {

			var payment = data.payment;
			payment.paymentgateway = window.paymentgateways[ payment.paymentgateway_id ];

			this.payments.push(payment);

			this.status = 'confirmed';
			this.setStatus();

			this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

Booking.prototype.loadPayments = function(successFn, errorFn) {

	var params = {
		booking_id: this.id
	};

	$.ajax({
		type: "POST",
		url: "/api/booking/payments",
		data: params,
		context: this,
		success: function(data) {

			this.payments = data;

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Adds a refund to the booking
 *
 * @param  {object} params    Must contain:
 * - _token
 * - amount
 * - paymentgateway_id
 *
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.addRefund = function(params, successFn, errorFn) {

	params.booking_id = this.id;

	$.ajax({
		type: "POST",
		url: "/api/refund/add",
		data: params,
		context: this,
		success: function(data) {

			var refund = data.refund;
			refund.paymentgateway = window.paymentgateways[ refund.paymentgateway_id ];

			this.refunds.push(refund);

			this.calculateSums();

			successFn(data.status);
		},
		error: errorFn
	});
};

Booking.prototype.loadRefunds = function(successFn, errorFn) {

	var params = {
		booking_id: this.id
	};

	$.ajax({
		type: "POST",
		url: "/api/booking/refunds",
		data: params,
		context: this,
		success: function(data) {

			this.refunds = data;

			successFn(data.status);
		},
		error: errorFn
	});
};

/**
 * Validate that all required lead customer fields are provided
 * @param {function} successFn Recieves API data.status as first and only parameter
 * @param {function} errorFn   Recieves xhr object as first parameter. xhr.responseText contains the API response in plaintext
 */
Booking.prototype.validate = function(successFn, errorFn){

	var params = {
		booking_id: this.id
	};

	$.ajax({
		type: "GET",
		url: "/api/booking/validate",
		data: params,
		context: this,
		success: function(data) {
			successFn(data.status);
		},
		error: errorFn
	});
};

Booking.prototype.calculateSums = function() {
	this.sums.payed = _.reduce(this.payments, function(memo, payment) {
		return memo + payment.amount * 1;
	}, 0).toFixed(2);

	this.sums.refunded = _.reduce(this.refunds, function(memo, refund) {
		return memo + refund.amount * 1;
	}, 0).toFixed(2);

	this.sums.have = (this.sums.payed - this.sums.refunded).toFixed(2);

	this.sums.payable = (this.decimal_price - this.sums.have).toFixed(2);

	this.sums.refundable = (this.sums.have - this.cancellation_fee).toFixed(2);
};

Booking.prototype.setStatus = function() {

	this.saved = this.reserved = this.confirmed = this.cancelled = false;

	// Set the status attribute to true (needed for Handlebars #if blocks)
	switch(this.status) {
		case 'saved':     this.saved = true;     break;
		case 'reserved':  this.reserved = true;  break;
		case 'confirmed': this.confirmed = true; break;
		case 'cancelled': this.cancelled = true; break;
		default: break;
	}
}


/*
 ********************************
 ******* PRIVATE FUNCTIONS ******
 ********************************
 */

var Class = {

	get : function(params, handleData) {
		$.get("/api/class", params, function(data) {
			handleData(data);
		});
	},

	getAll : function(handleData) {
		$.get("/api/class/all", function(data){
			handleData(data);
		});
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	/////////////////////////////////////////
	////// C L A S S - S E S S I O N S //////
	/////////////////////////////////////////

	getAllSessions : function(params, handleData) {
		$.get("/api/class-session/all", params, function(data){
			handleData(data);
		});
	},

	filter : function(params, handleData, errorFn) {
		$.ajax({
			type: "GET",
			url: "/api/class-session/filter",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	createSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class-session/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class-session/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deleteSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class-session/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deactivateSession: function(params, handleData) {
		$.post("/api/class-session/deactivate", params, function(data){
			handleData(data);
		});
	},

	restoreSession: function(params, handleData) {
		$.post("/api/class-session/restore", params, function(data){
			handleData(data);
		});
	},

	getAllCustomers: function(params, handleData, errorFn) {
		$.ajax({
			type: "GET",
			url: "/api/class-session/manifest",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};

var Company = {
	getCompany : function(handleData) {
		$.ajax({
			type: "GET",
			async: false,
			url: "/api/company",
			success: handleData,
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			url: "/api/company/update",
			type: "POST",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	initialise : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/company/initialise",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	getNotifications : function(handleData) {
		$.get("/api/company/notifications", function(data){
			handleData(data);
		});
	},

	sendFeedback : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/company/feedback",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	sendEmail : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/company/email",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	sendHeartbeat : function(params) {

		params = $.extend({}, params, {
			'route': window.location.hash,
			'_token': window.token
		});

		$.ajax({
			type: "POST",
			url: "/api/company/heartbeat",
			data: params,
			global: false
		});
	},
};

var Course = {

	get : function(params, handleData) {
		$.get("/api/course", params, function(data) {
			handleData(data);
		});
	},

	getAll : function(handleData) {
		$.get("/api/course/all", function(data){
			handleData(data);
		});
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/course/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/course/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/course/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};

var Customer = {

	getCustomer : function(params, handleData) {
		$.get("/api/customer", params, function(data) {
			handleData(data);
		});
	},

	getAllCustomers : function(handleData) {
		$.get("/api/customer/all", function(data){
			handleData(data);
		});
	},

	createCustomer : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/customer/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	filter : function(params, handleData, errorFn) {
		$.ajax({
			type: "GET",
			url: "/api/customer/filter",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateCustomer : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/customer/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};
var Place = {

	//params need to be as follows:
	//float latitude
	//float longitude
	//int limit
	around: function(params, handleData) {
		$.get("/api/company/locations", params).done(function(data){
			handleData(data);
		});
	},


	// var bounds = map.getBounds(),
	//     north  = bounds.getNorthEast().lat(),
	//     west   = bounds.getSouthWest().lng(),
	//     south  = bounds.getSouthWest().lat(),
	//     east   = bounds.getNorthEast().lng();

	// var area   = [north, west, south, east];

	//gets locations inside a rectangle
	//requires one param - "area", as above
	inside: function(params, handleData){
		$.get("/api/company/locations", params).done(function(data){
			handleData(data);
		});
	},

	tags : function(handleData) {
		$.get("/api/location/tags", handleData);
	},

	//Params:
	// @param string name        A name for the location
	// @param string description A description for the location (optional)
	// @param float  latitude
	// @param float  longitude
	// @param string tags        Tags for the location (optional)

	//Note: all created locations are available to all companies
	create: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/company/add-location",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/location/update",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	attached: function(handleData) {
		$.get("/api/location/all").done(function(data){
			handleData(data);
		});
	},

	attach: function(params, handleData){
		$.post("/api/location/attach", params).done(function(data){
			handleData(data);
		});
	},

	detach : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/location/detach",
			data: params,
			success: handleData,
			error: errorFn
		});
	},
};

var Package = {

	getPackage : function(params, handleData) {
		$.get("/api/package", params, function(data) {
			handleData(data);
		});
	},

	getAllPackages : function(handleData) {
		$.get("/api/package/all", function(data){
			handleData(data);
		});
	},

	getAllWithTrashed : function(handleData) {
		$.get("/api/package/all-with-trashed", function(data){
			handleData(data);
		});
	},

	getOnlyAvailable : function(handleData) {
		$.get("/api/package/only-available", function(data){
			handleData(data);
		});
	},

	createPackage : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/package/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updatePackage : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/package/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deletePackage : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/package/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};

var Payment = {

	/**
	 * param = {id: payment_id};
	 */
	get : function(params, handleData) {
		$.get("/api/payment", params, handleData);
	},

	getAll : function(handleData, from, take) {
		if(from === undefined)
			from = '';

		if(take === undefined)
			take = '';
		else
			take = '/' + take;

		$.get("/api/payment/all" + from + take, handleData);
	},

	getAllPaymentgateways : function(handleData) {
		$.get("/api/payment/paymentgateways", handleData);
	}
};

var Refund = {

	/**
	 * param = {id: payment_id};
	 */
	get : function(params, handleData) {
		$.get("/api/refund", params, handleData);
	},

	getAll : function(handleData, from, take) {
		if(from === undefined)
			from = '';

		if(take === undefined)
			take = '';
		else
			take = '/' + take;

		$.get("/api/refund/all" + from + take, handleData);
	},

	getAllPaymentgateways : function(handleData) {
		$.get("/api/refund/paymentgateways", handleData);
	}
};

var Report = {
	
	getPaymentGateways : function(handleData) {
		$.ajax({ url: '/api/payment/paymentgateways', success: handleData });
	},

	getPayments : function(params, handleData) {
		$.ajax({
			url: '/api/payment/filter',
			data: params,
			success: handleData
		});
	},

	getRefunds : function(params, handleData) {
		$.ajax({
			url: '/api/refund/filter',
			data: params,
			success: handleData
		});
	},

	getAgentBookings : function(params, handleData) {
		$.ajax({
			url: '/api/booking/filter-confirmed-by-agent',
			data: params,
			success: handleData
		});
	},

	getBookingHistory : function(params, handleData) {
		$.ajax({
			url: '/api/booking/filter-confirmed',
			data: params,
			success: handleData
		});
	},

	getTripUtilisation : function(params, handleData) {
		$.ajax({
			url: '/api/report/utilisation',
			data: params,
			success: handleData
		});
	},

	getClassUtilisation : function(params, handleData) {
		$.ajax({
			url: '/api/report/trainingutilisation',
			data: params,
			success: handleData
		});
	},

	getDemographics : function(params, handleData) {
		$.ajax({
			url: '/api/report/demographics',
			data: params,
			success: handleData
		});
	},

	getPickupSchedule : function(params, handleData) {
		$.ajax({
			url: 'api/company/pick-up-schedule',
			data: params,
			success: handleData
		});
	},

	getTicketsPackages : function(params, handleData) {
		$.ajax({
			url: 'api/report/revenue-streams',
			data: params,
			success: handleData
		});
	}

};
var Session = {
	//params = int id (the ID of the wanted session)
	getSpecificSession: function(params, handleData) {
		$.get("/api/session", params).done(function(data){
			handleData(data);
		});
	},

	getAllSessions: function(handleData) {
		console.warning('The function Session.getAllSessions() has been deprecated! Please use Session.filter() instead!');

		$.get("/api/session/all").done(function(data){
			handleData(data);
		});
	},

	/**
	 * Filter sessions by certain parameters.
	 *
	 * Required:
	 * - ticket_id
	 *
	 * Optional:
	 * - package_id
	 * - trip_id
	 * - after      (a datetime (in UTC) of the format 'YYYY-MM-DD hh:mm:ss')
	 * - before     (a datetime (in UTC) of the format 'YYYY-MM-DD hh:mm:ss')
	 * - with_full  (whether or not to include full boats into the result set. Defaul: false)
	 *
	 * @param  {function} handleData [recieves API 'data' as first and only parameter]
	 * @param  {function} errorFn    [recieves xhr object as first parameter.
	 *                                'xhr.responseText' contains the API response in plaintext]
	 */
	filter: function(params, handleData, errorFn) {
		$.ajax({
			type: "GET",
			url: "/api/session/filter",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	getToday: function(handleData, errorFn) {
		$.ajax({
			type: "GET",
			url: "/api/session/today",
			success: handleData,
			error: errorFn
		});
	},

	//Params:
	// @param integer trip_id      The ID of the trip that the session belongs to
	// @param string  start        The start datetime of the session. Must be interpretable by the strtotime PHP function
	// @param integer boat_id      The ID of the boat that carries this session
	// @param integer timetable_id The ID of the related timetable (optional)
	createSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/session/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/session/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deleteSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/session/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deactivateSession: function(params, handleData) {
		$.post("/api/session/deactivate", params, function(data){
			handleData(data);
		});
	},

	restoreSession: function(params, handleData) {
		$.post("/api/session/restore", params, function(data){
			handleData(data);
		});
	},

	getAllCustomers: function(params, handleData, errorFn) {
		$.ajax({
			type: "GET",
			url: "/api/session/manifest",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};

//all ticket interactions with the api

var Ticket = {

	// Param - id of ticket wanted
	getTicket : function(params, handleData){
		$.get("/api/ticket", params, function(data){
			handleData(data);
		});
	},

	// No params needed
	getAllTickets : function(handleData){
		$.get("/api/ticket/all", function(data){
			handleData(data);
		});
	},

	getAllWithTrashed : function(handleData){
		$.get("/api/ticket/all-with-trashed", function(data){
			handleData(data);
		});
	},

	getOnlyAvailable : function(handleData){
		$.get("/api/ticket/only-available", function(data){
			handleData(data);
		});
	},

	//Params
	// trip_id
	// name
	// description
	// price
	// currency (if not set then it will default to centres currency)
	// boats (optional - and array of boat_id => accomodation_id)
	createTicket : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/ticket/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	//Params
	// trip_id
	// name
	// description
	// price
	// currency (if not set then it will default to centres currency)
	// boats (optional - and array of boat_id => accomodation_id)

	// !!!!
	// The response can contain an id field. If it does it means
	// that the ticket could not simply be updated because it has
	// already been booked. Instead the old ticket has now been
	// replaced with an updated ticket in the system. The returned
	// id is the new ID of the ticket and must be used from now on!
	updateTicket : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/ticket/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	//only param is ID - the id of the ticket needed to be deleted
	deleteTicket : function(params, handleData){
		$.post("/api/ticket/delete", params, function(data){
			handleData(data);
		});
	}

};

var Timetable = {
	//params = int id (the ID of the wanted session)
	/*
	getSecificSession: function(params, handleData) {
		$.get("/api/session?" + Math.random(), params).done(function(data){
			handleData(data);
		});
	},

	getAllSessions: function(handleData) {
		$.get("/api/session/all?" + Math.random()).done(function(data){
			handleData(data);
		});
	},
	*/

	//Params:
	// @param integer trip_id      The ID of the trip that the session belongs to
	// @param string  start        The start datetime of the session. Must be interpretable by the strtotime PHP function
	// @param integer boat_id      The ID of the boat that carries this session
	// @param integer timetable_id The ID of the related timetable (optional)
	createTimetable: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/timetable/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	createClassTimetable: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/schedule/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},
};

var Trip = {
	getAllTrips : function (handleData) {
		$.get("/api/trip/all").done(function(data){
			handleData(data);
		});
	},
	getAllWithTrashed : function (handleData) {
		$.get("/api/trip/all-with-trashed").done(function(data){
			handleData(data);
		});
	},

	getSpecificTrip : function (params, handleData) {
		$.get("/api/trip", params).done(function(data){
			handleData(data);
		});
	},

	tags : function(handleData) {
		$.get("/api/trip/tags", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/trip/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/trip/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	/*deactivate : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/trip/deactivate",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	restore : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/trip/restore",
			data: params,
			success: handleData,
			error: errorFn
		});
	},*/

	delete : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/trip/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}
};
