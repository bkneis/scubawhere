<div id="wrapper">

	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#source-tab">Source</a></li>
		<li role="presentation"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#ticket-tab">Ticket</a></li>
		<li role="presentation"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#customer-tab">Customers</a></li>
		<li role="presentation"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#session-tab">Sessions</a></li>
	</ul>

	<div class="tab-content">

		<div role="tabpanel" class="tab-pane fade in active" id="source-tab">
			<div class="row">
				<div class="col-xs-12">
					<div class="page-header">
						<h2>Booking Source <small>Where is your booking coming from?</small></h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-8 col-sm-offset-2">
					<div class="form-group">
						<div class="btn-group btn-group-justified booking-source">
							<a role="button" class="btn btn-default btn-lg" data-type="agent">
								<p><i class="fa fa-user fa-3x"></i></p>
								<p class="text-center">Agent</p>
							</a>
							<a role="button" class="btn btn-default btn-lg" data-type="telephone">
								<p><i class="fa fa-phone fa-3x"></i></p>
								<p class="text-center">Phone</p>
							</a>
							<a role="button" class="btn btn-default btn-lg" data-type="email">
								<p><i class="fa fa-envelope fa-3x"></i></p>
								<p class="text-center">Email</p>
							</a>
							<a role="button" class="btn btn-default btn-lg" data-type="facetoface">
								<p><i class="fa fa-eye fa-3x"></i></p>
								<p class="text-center">In Person</p>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="row" id="agent-info">
				<div class="col-sm-4 col-sm-offset-4">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2 class="panel-title">Select Agent</h2>
						</div>
						<div class="panel-body">
							<div class="list-group" id="agents">
							</div>
						</div>
					</div>
					<script id="agents-list-template" type="text/x-handlebars-template">
						{{#each agents}}
						<a href="javascript:void(0);" data-id="{{id}}" class="list-group-item list-group-radio">
							{{name}} <span class="badge">{{branch_name}}</span>
						</a>
						{{/each}}
					</script>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<a href="javascript:void(0);" class="btn btn-primary source-finish pull-right">Next</a>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane fade" id="ticket-tab">
			<div class="row">
				<div class="col-xs-12">
					<div class="page-header">
						<h2>Tickets <small>Which tickets would you like to book?</small></h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-8">
					<div class="form-group" id="tickets">
						
					</div>
					<script id="tickets-list-template" type="text/x-handlebars-template">
						{{#each tickets}}
							<div class="col-sm-3">
								<a role="button" class="btn btn-default btn-lg btn-ticket" data-id="{{id}}">
									<p class="ticket-icon"><i class="fa fa-ticket fa-3x"></i></p>
									<p class="text-center ticket-name">{{name}}</p>
									<p class="text-center ticket-price">{{base_prices.decimal_price}}</p>
								</a>
							</div>
						{{/each}}
					</script>
				</div>
				<div class="col-sm-4">
					<ul class="list-group">
						<li class="list-group-item active">
							<h4 class="list-group-item-heading">Basket</h4>
							<p class="list-group-item-text">Total tickets: <span id="basket-total">0</span></p>
						</li>
						<li class="list-group-item" id="basket">

						</li>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<a href="javascript:void(0);" class="btn btn-primary tickets-finish pull-right">Next</a>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane fade" id="customer-tab">
			<div class="row">
				<div class="col-xs-12">
					<div class="page-header">
						<h2>Customers <small>Select the customers this booking is for.</small></h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-7">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">Existing Customer</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label for="existing-customers" class="col-sm-4 control-label">Name</label>
								<div class="col-sm-8">
									<select id="existing-customers" name="existing-customers" class="form-control select2"></select>
									<script id="customers-list-template" type="text/x-handlebars-template">
										{{#each customers}}
											<option value="{{id}}">{{firstname}} {{lastname}} - {{email}}</option>
										{{/each}}
									</script>
								</div>
							</div>
						</div>
						<div class="panel-footer">
							<div class="row">
								<div class="col-xs-12">
									<a href="javascript:void(0);" class="btn btn-primary add-customer pull-right" style="margin-left:5px;">Add to booking</a>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-default form-horizontal">
						<div class="panel-heading">
							<h3 class="panel-title">New Customer</h3>
						</div>
						<form id="new-customer">
							<fieldset>
								<div class="panel-body">
									<div class="form-group">
										<label for="email" class="col-sm-4 control-label">Email</label>
										<div class="col-sm-8">
											<input id="customer-email" name="email" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label for="firstname" class="col-sm-4 control-label">First Name</label>
										<div class="col-sm-8">
											<input id="customer-firstname" name="firstname" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label for="lastname" class="col-sm-4 control-label">Last Name</label>
										<div class="col-sm-8">
											<input id="customer-lastname" name="lastname" class="form-control">
										</div>
									</div>
								</div>
								<div class="panel-footer">
									<div class="row">
										<div class="col-xs-12">
											<button type="submit" class="btn btn-primary new-customer pull-right" style="margin-left:5px;">Create</button>
											<a href="javascript:void(0);" class="btn btn-warning clear-customer pull-right">Clear</a>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="col-md-5">
					<ul class="list-group">
						<li href="#" class="list-group-item active">
							<h4 class="list-group-item-heading">Added Customers</h4>
						</li>
						<div id="added-customers">

						</div>
						<script id="added-customers-template" type="text/x-handlebars-template">
							<li href="#" class="list-group-item">
								<h4 class="list-group-item-heading">{{firstname}} {{lastname}}</h4>
								<p class="list-group-item-text">
									<a href="mailto:{{email}}">{{email}}</a><br>
									{{address_1}}<br>
									{{city}}, {{county}}, {{postcode}}<br>
									<abbr title="Phone">P:</abbr> {{phone}}
								</p>
								<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-id="{{id}}">Edit</a>
								<a href="javascript:void(0);" class="btn btn-danger btn-sm remove-customer">Remove</a>
							</li>
						</script>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<a href="javascript:void(0);" class="btn btn-primary customers-finish pull-right">Next</a>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane fade" id="session-tab">
			<div class="row">
				<div class="col-xs-12">
					<div class="page-header">
						<h2>Sessions <small>When would you like to go diving?</small></h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4 col-sm-offset-2">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2 class="panel-title">Select Customer</h2>
						</div>
						<div class="panel-body">
							<div class="list-group" id="session-customers">
							</div>
						</div>
					</div>
					<script id="session-customers-template" type="text/x-handlebars-template">
						<a href="javascript:void(0);" data-id="{{id}}" class="list-group-item list-group-radio">
							{{firstname}} {{lastname}}
						</a>
					</script>
				</div>
				<div class="col-sm-4">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2 class="panel-title">Select Ticket</h2>
						</div>
						<div class="panel-body">
							<div class="list-group" id="session-tickets">
							</div>
						</div>
					</div>
					<script id="session-tickets-template" type="text/x-handlebars-template">
						<a href="javascript:void(0);" data-id="{{id}}" class="list-group-item list-group-radio">
							{{name}}
						</a>
					</script>
				</div>
			</div>
			<div class="row">
				<form class="form-inline" role="form">
					<div class="col-sm-4">
						<div class="form-group">
							<label for="" class="col-sm-5 control-label">After:</label>
							<div class="col-sm-7">
								<input type="email" class="form-control" id="inputEmail3" placeholder="Email">
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label for="" class="col-sm-5 control-label">Before:</label>
							<div class="col-sm-7">
								<input type="email" class="form-control" id="inputEmail3" placeholder="Email">
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label for="" class="col-sm-5 control-label">Trip:</label>
							<div class="col-sm-7">
								<select id="trips" name="trips" class="form-control select2"></select>
							</div>
							<script id="trips-list-template" type="text/x-handlebars-template">
								{{#each trips}}
									<option value="{{id}}">{{name}}</option>
								{{/each}}
							</script>
						</div>
					</div>
				</form>
			</div>
			<div class="row">
				<div class="col-md-10 col-md-offset-1">
					<table class="table table-condensed" id="sessions-table">
						<thead>
							<tr>
								<th>Start</th>
								<th>Finish</th>
								<th>Trip</th>
								<th>Free Spaces</th>
								<th>Boat</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>
				<script id="sessions-table-template" type="text/x-handlebars-template">
					{{#each sessions}}
						<tr>
							<td>{{friendlyDate start}}</td>
							<td>{{tripFinish start trip.duration}}</td>
							<td>{{trip.name}}</td>
							<td id="free-spaces-{{id}}">{{freeSpaces capacity}}</td>
							<td>{{boat.name}}</td>
							<td><a href="javascript:void(0);" class="btn btn-primary btn-sm assign-session" data-id="{{id}}">Assign</a></td>
						</tr>
					{{/each}}
				</script>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane fade" id="settings-tab">...</div>
	</div>
</div>

<div id="wrapper">
	<div class="accordion" id="section1">Step 1: Source of Booking<span></span></div>
	<div>
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
	<div>
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
	<div>
		<div class="content">
			<h2>Please fill in the customer details and assign their trips<h2>
				<ul id="tabul">
					<li id="litab" class="ntabs add"><a href="" id="addtab">+</a></li>
				</ul>
				<div id="tabcontent"></div>
				<button class="bttn blueb big-bttn fancybox" id="assign-ticket" onclick="refreshCal()" href="#ticket-fancybox" style="margin-top:10px; display:inline">Assign ticket</button>
				<!--<button class="bttn blueb big-bttn fancybox" id="assign-ticket" onclick="openFancy()" style="margin-top:10px;">Assign ticket</button>-->

				<!-- Pop up box -->
				<div id="ticket-fancybox" style="display:none; height:600px%; width:700px">
					<!--<button onclick="test9()">test</button>-->
					<div id="customer-select">
						<!-- Here il display all the customers names, then have onclick to send customer-id to hidden data aswell look up if thier lead-->
						<p>Customer: <select id="customers" onChange=""><option value="0">Please select...</option></select></p>
					</div>
					<div id="tickets-select" style="float:left;">
						<p>Ticket: <select id="customer-tickets" onChange="showSessions()"><option value="0">Please select...</option></select>
						<select id="addons" onChange=""><option value="0">Addons...</option>
						<script id="addons-template" type="text/x-handlebars-template">
							{{#each addons}}
								<option id=addon{{id}}>{{name}} - {{price}} {{currency}}</option> 
							{{/each}}
						</script>
						</select></p>
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
		<div>
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
		<div>
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
	<script type="text/javascript" src="tabs/add-booking/js/frontend.js"></script>

	<link rel="stylesheet" href="tabs/add-booking/css/style.css" type="text/css" />

	<link rel="stylesheet" href="common/css/bootstrap.min.css" type="text/css" />
	<script type="text/javascript" src="common/js/bootstrap.min.js"></script>

	<link rel="stylesheet" href="common/css/select2.css" type="text/css" />
	<link rel="stylesheet" href="common/css/select2-bootstrap.css" type="text/css" />
	<script type="text/javascript" src="common/js/select2.min.js"></script>

	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

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
	<script src="/dashboard/js/Controllers/Addons.js"></script>
