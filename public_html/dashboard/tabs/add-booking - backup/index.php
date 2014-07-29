<div id="wrapper">
	<div class="accordion" id="section1">Step 1: Source of Booking<span></span></div>
	<div class="container">
		<div class="content">
			<h2>Please select source of booking</h2>
			<select id="tob" onchange="validateTob()">
				<option>Please select an option</option>
				<option id="agent" value="agent">Agent</option>
				<option id="phone" value="phone">Phone</option>
				<option id="email" value="email">Email</option>
				<option id="person" value="person">In Person</option>
			</select>
			<div>
				<button onClick="test()">Test</button>
			<div id="agent-info" style="display:none">
				<h3>Please select which agent</h3>
				<select id="agents">
					<option>Select an agent</option>
					<script id="agents-list-template" type="text/x-handlebars-template">
					{{#each agents}}
					<option>{{name}}</option>
					{{/each}}
					</script>
				</select>
			</div>
		</div>
	</div>
</div>
	<!-- change! -->
	<div class="accordion" id="section2">Step 2: Trip Selection<span></span></div>
	<div class="container">
		<div class="content">
			<h2>Select trips that wish to be purchased</h2>
			<div class="products-col">
				<h1>Tickets</h1>
				<ul id="trips" class="product-list">
					<script id="trips-list-template" type="text/x-handlebars-template">
					{{#each tickets}}
					<li onclick="addTrip('{{name}}', '{{price}}', '{{id}}')">{{name}}</li>
					{{/each}}
					</script>
				</ul>
			</div>
			<div class="products-col">
				<h1>Packages</h1>
				<ul id="packages" class="product-list">
					<script id="packages-list-template" type="text/x-handlebars-template">
					{{#each packages}}
					<li onclick="addPackage('{{name}}', '{{price}}', {{id}})">{{name}}</li>
					{{/each}}
					</script>
				</ul>
			</div>

			<div id="selected-trips" class="products-col">
				<h1>Selected Trips</h1>
				<div class="trips-container">
					<ol id="selected-trips-list">
					</ol>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="accordion" id="section3">Step 3: Customer Details<span></span></div>
	<div class="container">
		<div class="content">
			<h2>Please fill in the customer details and assign their trips<h2>
				<ul id="tabul">
					<li id="litab" class="ntabs add"><a href="" id="addtab">+</a></li>
				</ul>
				<div id="tabcontent"></div>
				<div id="trip-select-popup" style="display:none; height:600px">
					<div id="trips-select">
						<p>Trip: <select id="cust-trips" onChange="tripSelect()"><option value="0">Please select...</option></select></p>
					</div>
					<div id="packages-select">
						<p>
							Package: <select id="cust-packages" onChange="packageSelect()"><option value="0">Please select...</option></select>
							Trip: 
							<select id="cust-package-tickets" style="display:none;">
								<option>Select a trip</option>
								<script id="package-tickets-list-template" type="text/x-handlebars-template">
								{{#each tickets}}
								<option>{{name}}</option>
								{{/each}}
								</script>
							</select>
						</p>
					</div>
					<div id="calendar"></div>
					<button class="bttn blueb big-bttn" id="btnAssign">Assign Ticket</button>
				</div>
			</div>
		</div>
		<div class="accordion" id="section4">Step 4: Payment<span></span></div>
		<div class="container">
			<div class="content">
				<div id="trip-info" style="float:left; width:45%;">
					<h2>Trip Type</h2>
					<ul id="trips-list"></ul>
					<h3>Total Cost</h3>
				</div>
				<div id="trip-cost" style="float:left; width:45%;">
					<h2>Trip Cost</h2>
					<ul id="trips-cost-list"></ul>
					<p>0</p>
				</div>
				<div class="clear"></div>
				<div id="pay-options">
					<div class="pay-option">
						<p>Cash</p>
						<input class="payment" name="cash" type="text" size="5" maxlength="5" style="width:80%" />
					</div>
					<div class="pay-option">
						<p>Card</p>
						<input class="payment" name="card" type="text" size="5" maxlength="5" style="width:80%" />
					</div>
					<div class="pay-option">
						<p>Cheque</p>
						<input class="payment" name="cheque" type="text" size="5" maxlength="5" style="width:80%" />
					</div>
					<div class="pay-option">
						<p>Bank</p>
						<input class="payment" name="bank" type="text" size="5" maxlength="5" style="width:80%" />
					</div>
					<div class="pay-option">
						<p>POB</p>
						<input class="payment" name="pob" type="text" size="5" maxlength="5" style="width:80%" />
					</div>
					<div class="pay-option" style="padding-top:15px;">
						<input name="pay-online" type="button" value="Pay Online" />
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="accordion" id="section5">Summary<span></span></div>
		<div class="container">
			<div class="content">
				<div>Summary</div>
				<p>Summary of trips, customers and costs</p>
				<form id="booking-data">
				</form>
			</div>
		</div>
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

		$('.fancybox').fancybox();
	});
	</script>

	<!--My scripts-->
	<script type="text/javascript" src="tabs/add-booking/js/script.js"></script>
	<script type="text/javascript" src="tabs/add-booking/js/tabs.js"></script>
	<link rel="stylesheet" href="tabs/add-booking/css/style.css" type="text/css" />

	<!--Controllers-->
	<script src="/dashboard/js/Controllers/Agent.js"></script>
	<script src="/dashboard/js/Controllers/Ticket.js"></script>
	<script src="/dashboard/js/Controllers/Package.js"></script>
	<script src="/dashboard/js/Controllers/Sessions.js"></script>

	<!--Calander-->
	<link href='tabs/add-booking/calander/fullcalendar.css' rel='stylesheet' />
	<link href='tabs/add-booking/calander/fullcalendar.print.css' rel='stylesheet' media='print' />
	<script src='tabs/add-booking/calander/lib/moment.min.js'></script>
	<!--<script src='tabs/add-booking/calander/lib/jquery.min.js'></script>-->
	<script src='tabs/add-booking/calander/lib/jquery-ui.custom.min.js'></script>
	<script src='tabs/add-booking/calander/fullcalendar.min.js'></script>
	<script>

	$(document).ready(function() {
		var days = 0;
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: '2014-06-12',
			selectable: true,
			selectHelper: true,
			select: function(start, end) {
				//var title = prompt('Event Title:');
				days++;
				var title = 'Day '+days+' of diving';
				var eventData;
				if (title) {
					eventData = {
						title: title,
						start: start,
						end: end
					};
					$('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
				}
				$('#calendar').fullCalendar('unselect');
			},
			editable: false,
			events: [
			{
				title: 'Fun Dive',
				start: '2014-06-01'
			},
			{
				title: 'Boat Trip',
				start: '2014-06-07',
				end: '2014-06-10'
			},
			{
				id: 999,
				title: 'Diving Club',
				start: '2014-06-09T16:00:00'
			},
			{
				id: 999,
				title: 'Diving Club',
				start: '2014-06-16T16:00:00'
			},
			{
				title: 'Meeting',
				start: '2014-06-12T10:30:00',
				end: '2014-06-12T12:30:00'
			},
			{
				title: 'Click for Google',
				url: 'http://google.com/',
				start: '2014-06-28'
			}
			]
		});

});

</script>
<style>

body {
	margin: 0;
	padding: 0;
	font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
	font-size: 14px;
}

#calendar {
	width: 90%;
	margin: 20px auto;
	height:70%;
}

</style>

