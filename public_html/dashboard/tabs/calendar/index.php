<div id="wrapper">
	<div class="row">
		<!--<div class="box100">
			<label class="purpleb">Filters</label>
			<div id='filter' class="padder">
				<p>Display :
					<a onclick="">All</a> | 
					<a onclick="">Trips</a> | 
					<a onclick="">Accomodations</a>
				</p>
				<!--<div class="form-row">
					<label class="form-label">Filter by : </label>
					<select id="filter-option">
						<option value=""></option>
					</select>
				</div>
				<div class="form-row">
					<label class="form-label">Boats : </label>
					<select id="boats">
						<option value=""></option>
					</select>
				</div>
				<div class="form-row">
					<label class="form-label">Trips : </label>
					<select id="trips">
						<option value=""></option>
					</select>
				</div>-->
			<!--</div>
		</div>-->
	</div>

	<div id='calendar'></div>
	<div style='clear:both'></div>

	<div id="modalWindows" style="height: 0; visibility: hidden;">
		<script id="session-template" type="text/x-handlebars-template">
			<div id="modal-{{id}}" class="reveal-modal">
		
				<h2>{{{trip.name}}}</h2>
				<table style="margin-top: 2em;">
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
								{{ticketsLeft}} / {{capacity}} | <a href="#add-booking">BOOK NOW</a>
							{{/unless}}
						</td>
					</tr>
				</table>
				<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
			</div>
		</script>
	</div>
</div>

<script src="/common/js/fullcalendar.min.js"></script>

<script src="/common/js/ui.min/jquery-ui.min.js"></script>
<script src="/common/js/jquery.reveal.js"></script>

<script src="js/Controllers/Trip.js"></script>
<script src="js/Controllers/Boat.js"></script>
<script src="js/Controllers/Session.js"></script>
<script src="js/Controllers/Timetable.js"></script>

<script src="tabs/calendar/js/script.js" type="text/javascript"></script>
