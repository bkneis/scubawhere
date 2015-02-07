<div id="wrapper">
	<div class="col-md-12">
		<div class="panel panel-default" id="calendar-filters">
			<div class="panel-heading">
				<h4 class="panel-title">Filters</h4>
			</div>
			<div class="panel-body" id="filters">
				<div class="form-row">
					<div class="onofflabel">
						<p><strong>Accommodations</strong></p>
					</div>
					<div class="onoffswitch" id="calendar-switch" >
						<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" checked>
						<label class="onoffswitch-label" for="myonoffswitch">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
					<div class="onofflabelright">
						<p><strong>Trips</strong></p>
					</div>
				</div>
				<div style="clear:both"></div>
				<div id="jump-to-date" class="form-row">
					<label style="float:left; padding-right:10px;" class="form-label">Jump to : </label>
					<input style="width:200px; float:left;" id="jump-date" type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="jumpto" placeholder="YYYY-MM-DD">
					<button id="remove-jump" style="display:none;" class="btn btn-danger">Clear</button>
				</div>
				<div style="clear:both"></div>
				<div id="filter-settings" class="form-row">
					<!--<label class="form-label">Add filter : </label>-->
					<select id="filter-options">
						<option value="all">Add filter</option>
						<option value="boat">Boats</option>
						<option value="trip">Trips</option>
					</select>
				</div>
				<div id="filter"></div>
				<script type="text/x-handlebars-template" id="boats-list-template">
					<p>
						<label class="form-label">Filter Boats : </label>
						<select class="filter" id="boats">
							<option value="all"></option>
							{{#each boats}}
								<option value="{{id}}">{{name}}</option>
							{{/each}}
						</select>
						<button id="remove-boats-filter" class="btn btn-danger remove-room">&#215;</button>
					</p>
				</script>
				<script type="text/x-handlebars-template" id="trips-list-template">
					<p>
						<label class="form-label">Filter Trips : </label>
						<select class="filter" id="trips">
							<option value="all"></option>
							{{#each trips}}
								<option value="{{id}}">{{name}}</option>
							{{/each}}
						</select>
						<button id="remove-trips-filter" class="btn btn-danger remove-room">&#215;</button>
					</p>
				</script>
			</div>
		</div>

		<div id='calendar'></div>
	</div><!-- end of col-md-12 -->

	<div id="modalWindows" style="height: 0; visibility: hidden;">
		<script id="session-template" type="text/x-handlebars-template">
			<div id="modal-{{id}}" class="reveal-modal">

				<h3>{{{trip.name}}}</h3>
				<table style="margin-top: 2em;" class="striped">
				{{#if sameDay}}
					<tr>
						<td><strong>Date</strong></td>
						<td>{{date start}}</td>
					</tr>
					<tr>
						<td><strong>Start - End time</strong></td>
						<td>
							<span class="enddatetime">{{hours start}}:{{minutes start}} - {{hours end}}:{{minutes end}}</span>
						</td>
					</tr>
				{{else}}
					<tr>
						<td><strong>Starting</strong></td>
						<td>{{date start}} - {{hours start}}:{{minutes start}}</td>
					</tr>
					<tr>
						<td><strong>Ending</strong></td>
						<td>
							{{date end}} - {{hours end}}:{{minutes end}}
						</td>
					</tr>
				{{/if}}
					<tr>
						<td><strong>Boat</strong></td>
						<td>
							{{session.boat.name}}
						</td>
					</tr>
					<tr>
						<td><strong>Tickets Available</strong></td>
						<td>{{#unless ticketsLeft}}
								<span class="soldout">SOLD OUT</span>
							{{else}}
								{{ticketsLeft}} out of {{capacity}} | <a href="#add-booking">BOOK NOW</a>
							{{/unless}}
						</td>
					</tr>
				</table>
				<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
			</div>
		</script>
		<script id="accommodation-template" type="text/x-handlebars-template">
			<div id="modal-{{id}}" class="reveal-modal">

				<h3>{{{title}}}</h3>
				<table style="margin-top: 2em;">
					<tr>
						<td><strong>Date</strong></td>
						<td>{{start}}</td>
					</tr>
					<tr>
						<td><strong>Rooms available</strong></td>
						<td>{{available}}</td>
					</tr>
					<tr>
						<td><strong>Rooms booked</strong></td>
						<td>{{booked}}</td>
					</tr>
				</table>
				<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
			</div>
		</script>
	</div>

	<script src="/common/js/fullcalendar.min.js"></script>

	<script src="/common/js/jquery/ui.min/jquery-ui.min.js"></script>
	<script src="/common/js/jquery/jquery.reveal.js"></script>

	<script type="text/javascript" src="/common/js/jquery/jquery.cookie.js"></script>
	<script type="text/javascript" src="/common/js/jquery/jquery.collapsible.js"></script>

	<script src="js/Controllers/Trip.js"></script>
	<script src="js/Controllers/Boat.js"></script>
	<script src="js/Controllers/Session.js"></script>
	<script src="js/Controllers/Timetable.js"></script>
	<script src="js/Controllers/Accommodation.js"></script>

	<script src="tabs/calendar/js/script.js" type="text/javascript"></script>
</div>
