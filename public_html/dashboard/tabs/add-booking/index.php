<div id="wrapper">
	<div class="accordion" id="section1">Step 1: Source of Booking<span></span></div>
	<div class="container">
		<div class="content">
			<h2>Please select source of booking</h2>
			<select id="sob" onchange="validateSob()">
				<option value="0">Please select an option</option>
				<option id="agent" value="agent">Agent</option>
				<option id="telephone" value="telephone">Phone</option>
				<option id="email" value="email">Email</option>
				<option id="facetoface" value="facetoface">In Person</option>
			</select>
			<div>
				<div id="agent-info" style="display:none">
					<h3>Please select which agent</h3>
					<select id="agents">
						<option>Select an agent</option>
						<script id="agents-list-template" type="text/x-handlebars-template">
						{{#each agents}}
						<option value='{{id}}'>{{name}}</option>
						{{/each}}
						</script>
					</select>
				</div>
			</div>
			<!--<button onclick="getToday()">Testing button</button>-->
		</div>
	</div>
	<!-- change! -->
	<div class="accordion" id="section2">Step 2: Trip Selection<span></span></div>
	<div class="container">
		<div class="content">
			<h2>Select trips that wish to be purchased</h2>
			<div class="products-col">
				<h1>Tickets</h1>
				<ul id="available-tickets" class="product-list"><!--trips-->
					<script id="tickets-list-template" type="text/x-handlebars-template">
					{{#each tickets}}
					<li onclick="selectTicket('{{name}}', '{{id}}', '{{price}}')">{{name}}</li>
					{{/each}}
					</script>
				</ul>
			</div>
			<div class="products-col">
				<h1>Packages</h1>
				<ul id="available-packages" class="product-list">
					<script id="packages-list-template" type="text/x-handlebars-template">
					{{#each packages}}
					<li onclick="selectPackage('{{name}}', {{id}}, '{{price}}')">{{name}}</li>
					{{/each}}
					</script>
				</ul>
			</div>

			<div id="selected-tickets-div" class="products-col">
				<h1>Selected Trips</h1>
				<div class="trips-container">
					<ol id="selected-tickets">
					</ol>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="accordion" id="section3">Step 3: Add Customer Details<span></span></div>
	<div class="container">
		<div class="content">
			<h2>Please fill in the customer details and assign their trips<h2>
				<ul id="tabul">
					<li id="litab" class="ntabs add"><a href="" id="addtab">+</a></li>
				</ul>
				<div id="tabcontent"></div>
				<button class="bttn blueb big-bttn fancybox" id="assign-ticket" onclick="refreshCal()" href="#ticket-fancybox" style="margin-top:10px; display:none">Assign ticket</button>
				<!--<button class="bttn blueb big-bttn fancybox" id="assign-ticket" onclick="openFancy()" style="margin-top:10px;">Assign ticket</button>-->

				<!-- Pop up box -->
				<div id="ticket-fancybox" style="display:none; height:600px%; width:700px">
					<!--<button onclick="test9()">test</button>-->
					<div id="customer-select">
						<!-- Here il display all the customers names, then have onclick to send customer-id to hidden data aswell look up if thier lead-->
						<p>Customer: <select id="customers" onChange=""><option value="0">Please select...</option></select></p>
					</div>
					<div id="tickets-select" style="width:22%;float:left;">
						<p>Ticket: <select id="customer-tickets" onChange="showSessions()"><option value="0">Please select...</option></select></p>
					</div>
					<!--<div id="packages-select" style="width:40%; float:left;">

							<p>or Package: <select id="customer-packages" onChange="displayPackageTickets()"><option value="0">Please select...</option></select>
							Select ticket:
							<select id="customer-package-tickets" style="display:none;">
								<option value="0">Select a trip...</option>
							</select>
						</p>
					</div>-->
					<div style="clear:both;"></div>
					<!--<div id="info" style="display:none">
						<p id="session-id"></p>
				</div>-->
				<div id="calendar"></div>
				<button class="bttn blueb big-bttn" id="btnAssign" onclick="assignTicket()" style="float:right">Assign Ticket</button>
			</div><!-- End of pop up box -->
		</div>
	</div>
		<div class="accordion" id="section4">Step 4: Payment / Trip Summary<span></span></div>
		<div class="container">
			<div class="content">
				<div id="customers-trips-summary" style="max-height:200px; overflow-y:scroll;">
					<table id="customers-trips-table">
						<tr>
							<td><strong>Customer Name</strong></td>
							<td><strong>Ticket</strong></td>
							<td><strong>Start Date</strong></td>
							<td><strong>End Date</strong></td>
							<td><strong>Price</strong></td>
						</tr>
						<!--ADD CUSTOMER TICKETS HERE-->
					</table>
				</div>
				<table id="tblBookingCost">
						<tr>
							<td><Strong>Total Booking Cost</strong></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td id="totalBookingCost"><strong></strong></td>
						</tr>
					</table>
				<!--<div id="trip-info" style="float:left; width:45%;">
					<h2>Trip Type</h2>
					<ul id="trips-list"></ul>
					<h3>Total Cost</h3>
				</div>
				<div id="trip-cost" style="float:left; width:45%;">
					<h2>Trip Cost</h2>
					<ul id="trips-cost-list"></ul>
					<p>0</p>
				</div>-->
				<div class="clear"></div>
				<div id="pay-options">
					<h2>Payment Options</h2>
					<div class="pay-option">
						<p>Cash</p>
						<input class="payment" id="pay-cash" type="text" size="5" maxlength="5" style="width:80%" />
					</div>
					<div class="pay-option">
						<p>Card</p>
						<input class="payment" id="pay-card" type="text" size="5" maxlength="5" style="width:80%" />
					</div>
					<div class="pay-option">
						<p>Cheque</p>
						<input class="payment" id="pay-cheque" type="text" size="5" maxlength="5" style="width:80%" />
					</div>
					<div class="pay-option">
						<p>Bank</p>
						<input class="payment" id="pay-bank" type="text" size="5" maxlength="5" style="width:80%" />
					</div>
					<div class="pay-option">
						<p>POB</p>
						<input class="payment" id="pay-pob" type="text" size="5" maxlength="5" style="width:80%" />
					</div>
					<div class="pay-option" style="padding-top:24px;">
						<input name="validate-booking" type="button" value="Finalise Booking" onclick="validateBooking()"/>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<!--<div class="accordion" id="section5">Summary<span></span></div>
		<div class="container">
			<div class="content">
				<div>Summary</div>
				<p>Summary of trips, customers and costs</p>
				<form id="booking-data">
				</form>
			</div>
		</div>-->
	</div>
	<!--Accordion-->
	<script type="text/javascript" src="tabs/add-booking/js/jquery.min.js"></script>
	<script type="text/javascript" src="tabs/add-booking/js/highlight.pack.js"></script>
	<script type="text/javascript" src="tabs/add-booking/js/jquery.accordion.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {

        //syntax highlighter
        hljs.tabReplace = '    ';
        hljs.initHighlightingOnLoad();

        $.fn.slideFadeToggle = function(speed, easing, callback) {
        	return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);
        };

        //accordion
        $('.accordion').accordion({
        	defaultOpen: 'section1',
        	cookieName: 'accordion_nav',
        	speed: 'slow',
            animateOpen: function (elem, opts) { //replace the standard slideUp with custom function
            	elem.next().stop(true, true).slideFadeToggle(opts.speed);
            },
            animateClose: function (elem, opts) { //replace the standard slideDown with custom function
            	elem.next().stop(true, true).slideFadeToggle(opts.speed);
            }
        });

    });
	</script>

	<!--Fancy box-->
	<script type="text/javascript" src="tabs/add-booking/js/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="tabs/add-booking/css/jquery.fancybox.css?v=2.1.5" media="screen" />
	<script type="text/javascript">
	$(document).ready(function() {
		$('.fancybox').fancybox({
			/*onLoad : function(){
		     $('#calendar').fullCalendar('render');
		    }*/
		});
	});
	</script>

	<!--My scripts-->
	<script type="text/javascript" src="tabs/add-booking/js/script.js"></script>
	<script type="text/javascript" src="tabs/add-booking/js/tabs.js"></script>
	<link rel="stylesheet" href="tabs/add-booking/css/style.css" type="text/css" />

	<!-- Calendar-->
	<link rel='stylesheet' href='tabs/add-booking/calendar/fullcalendar.css' />
	<!--<script src='tabs/add-booking/calendar/lib/jquery.min.js'></script>-->
	<script src='tabs/add-booking/calendar/lib/moment.min.js'></script>
	<script src='tabs/add-booking/calendar/fullcalendar.js'></script>
	<script>
	$(document).ready(function() {

	    // page is now ready, initialize the calendar...

	    $('#calendar').fullCalendar({
	        // put your options and callbacks here
	        eventClick: function(calEvent, view) {

	        	sessionID = calEvent.sessionID;
	        	startDate = calEvent.start;
	        	endDate = calEvent.end;


		        alert('Trip selected: ' + calEvent.title);
		        //alert(sessionID);
		        //alert('Session ID: ' + calEvent.sessionID);
		        //alert('View: ' + view.name);

		        // change the border color just for fun
		        $(this).css('border-color', 'red');

		    	}//,
		    	//'option', 'height', 200,
	    		//'option', 'width', 300
	     
	    	
	    });



	});
	</script>

	<!--Controllers-->
	<script src="/dashboard/js/Controllers/Agent.js"></script>
	<script src="/dashboard/js/Controllers/Ticket.js"></script>
	<script src="/dashboard/js/Controllers/Package.js"></script>
	<script src="/dashboard/js/Controllers/Session.js"></script>
	<script src="/dashboard/js/Controllers/Booking.js"></script>
	<script src="/dashboard/js/Controllers/Trip.js"></script>
	<script src="/dashboard/js/Controllers/Customer.js"></script>
