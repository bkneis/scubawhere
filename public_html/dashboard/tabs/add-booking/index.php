<div id="wrapper">
	<div class="accordion" id="section1">Step 1: Type of Booking<span></span></div>
	<div class="container">
		<div class="content">
			<h2>Please select type of booking</h2>
			<select id="tob" onchange="validateTob()">
				<option>Please select an option</option>
				<option id="agent" value="agent">Agent</option>
				<option id="phone" value="phone">Phone</option>
				<option id="email" value="email">Email</option>
				<option id="person" value="person">In Person</option>
			</select>
			<div  id="agent-info" style="display:none">
				<h3>Please select which agent</h3>
				<select id="agents">
					<option>Select an agent</option>
					<script id="agent" type="text/x-handlebars-template">
					<option>{{name}}</option>
					</script>
				</select>
			</div>
		</div>
	</div>
	<div class="accordion" id="section2">Step 2: Trip Selection<span></span></div>
	<div class="container">
		<div class="content">
			<h2>Select trips that wish to be purchased</h2>
			<div id="trip-options">
				<h1>Trips</h1>
				<div id="trip-options">
					<ul id="trips">
						<script id="trip" type="text/x-handlebars-template">
							<li onclick="addTrip('{{name}}')">{{name}}</li>
						</script>
					</ul>
				</div>
			</div>

			<div id="selected-trips" style="padding-right:10px">
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

