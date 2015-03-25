<div id="wrapper" class="clearfix">
	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Trips</h4>
				</div>
				<div class="panel-body" id="trips">
					<div class="yellow-helper">
						Please drag a trip onto a day on the calendar to activate it.
					</div>
					<div id="filter-types" class="btn-group" role="group" style="margin-bottom:10px;">
					  <button id="filter-trips" display="trips" type="button" class="btn btn-default btn-primary filter-type">Trips</button>
					  <button id="filter-classes" display="classes" type="button" class="btn btn-default filter-type">Classes</button>
					</div>
					<ul id="trip-class-list" style="padding-left: 0;">
						<script id="trip-list" type="text/x-handlebars-template">
							{{#each trips}}
								<li class="droppable-event">
									<div data-type="trip" class='trip-event' data-id="{{id}}">
										{{{name}}}
									</div>
									<ul></ul>
								</li>
							{{/each}}
						</script>
						<script id="class-list" type="text/x-handlebars-template">
							{{#each classes}}
								<li class="droppable-event">
									<div data-type="class" class='trip-event' data-id="{{id}}">
										{{{name}}}
									</div>
									<ul></ul>
								</li>
							{{/each}}
						</script>
					</ul>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div id='calendar'></div>
		</div>
	</div>

	<div id="modalWindows" style="height: 0;">
		<script id="session-template" type="text/x-handlebars-template">
			<div id="modal-{{id}}" class="reveal-modal">
				<p style="text-transform: uppercase; float: right; line-height: 2.8em;">
					{{#if isNew}}
						New session
					{{else}}
						{{#unless session.deleted_at}}
							Update session
						{{/unless}}
					{{/if}}
				</p>
				<h4>{{#if session.deleted_at}}<s>{{/if}}{{{trip.name}}}{{#if session.deleted_at}}</s>{{/if}}</h4>
				{{#if session.deleted_at}} <p class="text-danger"><strong>Deactivated</strong></p>{{/if}}
				<table style="margin: 2em 0;" class="table">
					<tr>
						<td><strong>Date</strong></td>
						<td>{{date start}}</td>
					</tr>
					<tr>
						<td style="vertical-align: middle;"><strong>Departure time</strong></td>
						<td>
							<small><span style="display: inline-block; width: 49px">hour</span><span>minutes</span></small><br>
							<input type="text" placeholder="hh" value="{{hours start}}"   class="starthours"   style="width: 38px;"{{#if session.deleted_at}} disabled{{else}}{{#if isPast}} disabled{{/if}}{{/if}}>:
							<input type="text" placeholder="mm" value="{{minutes start}}" class="startminutes" style="width: 38px;"{{#if session.deleted_at}} disabled{{else}}{{#if isPast}} disabled{{/if}}{{/if}}> h
						</td>
					</tr>
					<tr>
						<td>
							<strong>Duration:</strong> {{readableDuration trip.duration}}
						</td>
						<td>
							<strong>End time:</strong> <span class="enddatetime">{{hours end}}:{{minutes end}} on {{date end}}</span>
						</td>
					</tr>
				</table>
				<div class="form-group">
				{{#if isTrip}}
					Boat for this session:&nbsp;
					<select name="boat_id" class="boatSelect"{{#if session.timetable_id}} disabled{{else}}{{#if session.deleted_at}} disabled{{else}}{{#if isPast}} disabled{{/if}}{{/if}}{{/if}}>
						{{#each boats}}
							{{#if deleted_at}}
								{{#if selected}}
									<option value="{{id}}"{{#if selected}} selected{{/if}}>{{{name}}}</option>
								{{/if}}
							{{else}}
								<option value="{{id}}"{{#if selected}} selected{{/if}}>{{{name}}}</option>
							{{/if}}
						{{/each}}
					</select>
				</div>
				{{/if}}
				{{#unless isPast}}
					{{#unless session.deleted_at}}
					{{#unless isNew}}
					{{#unless session.timetable_id}}
						<label>
							<input type="checkbox" onchange="toggleTimetableForm();">
							<h4 style="display: inline-block;">Create a repeating timetable</h4>
						</label>
						<form class="create-timetable dashed-border" style="overflow: auto; display: none;">
							<table class="table table-striped">
								<thead>
									<tr style="text-align: left;">
										<th>Week #</th>
										<th>Mon</th>
										<th>Tue</th>
										<th>Wed</th>
										<th>Thu</th>
										<th>Fri</th>
										<th>Sat</th>
										<th>Sun</th>
									</tr>
								</thead>
								<tr>
									<td>1</td>
									<td><input type="checkbox" name="schedule[1][]" value="mon" {{isWeekday 1}}></td>
									<td><input type="checkbox" name="schedule[1][]" value="tue" {{isWeekday 2}}></td>
									<td><input type="checkbox" name="schedule[1][]" value="wed" {{isWeekday 3}}></td>
									<td><input type="checkbox" name="schedule[1][]" value="thu" {{isWeekday 4}}></td>
									<td><input type="checkbox" name="schedule[1][]" value="fri" {{isWeekday 5}}></td>
									<td><input type="checkbox" name="schedule[1][]" value="sat" {{isWeekday 6}}></td>
									<td><input type="checkbox" name="schedule[1][]" value="sun" {{isWeekday 0}}></td>
								</tr>
								{{timetableWeek 2}}
							</table>
							<input type="hidden" name="_token">

							{{#if isTrip}}
							<input type="hidden" name="session_id" value="{{session.id}}">
							{{else}}
							<input type="hidden" name="training_session_id" value="{{session.id}}">
							{{/if}}

							Until:<br>
							<input type="date" name="until" placeholder="YYYY-MM-DD" style="width: 175px;"><br>
							<small>Default: for 1.5 years</small>

							<button data-type="{{#if isTrip}}trips{{else}}classes{{/if}}" class="btn btn-primary btn-lg create-timetable-button pull-right">Create timetable</button>
						</form>
					{{else}}
						<div class="horizontal-seperator"><span>Options</span></div>
						<div class="yellow-helper attention-placeholder">
							<h3>You are editing a <u>timetabled</u> session</h3>
							Do you want to move/delete all future versions of this session, too?</p>
							<p>
								<label style="display: block; margin-bottom: 0.5em;">
									<input type="radio" name="handle_timetable" value="following"> <strong>Yes</strong>, also move/delete all future versions.<br>
									<small style="display: block; margin: 0 2em; color: orange;">
										For UPDATE, this will move all future versions regardless if they have been booked or not. For DELETE, this will <u>deactivate</u> all future versions that have been booked and delete the others.
									</small>
								</label>
								<label><input type="radio" name="handle_timetable" value="only_this"> <strong>No</strong>, just move/delete this one and leave the others where they are.</label>
							</p>
						</div>
					{{/unless}}
					{{/unless}}
					{{/unless}}

					<div style="margin-top: 1em; text-align: right">
						{{#if isNew}}
							<a class="close-modal" title="Abort" style="margin-right: 2em;">Cancel</a>
							<button class="submit-session btn btn-primary btn-lg">SAVE</button>
						{{else}}
							{{#unless isPast}}
								{{#unless session.deleted_at}}
									<button class="delete-session btn btn-danger pull-left">Delete</button>
									<a class="close-modal" title="Abort" style="margin-right: 2em;">Cancel</a>
									<button class="update-session btn btn-primary btn-lg">SAVE</button>
								{{else}}
									<button class="delete-session btn btn-danger pull-left">Delete</button>
									<button class="restore-session btn btn-primary pull-left">Restore</button>
									<input type="radio" name="handle_timetable" value="only_this" checked style="visibility: hidden;">
								{{/unless}}
							{{/unless}}
						{{/if}}
					</div>
				{{else}}
					<div style="margin-top: 1em; text-align: center; color: gray;">
						Past sessions cannot be edited.
					</div>
				{{/unless}}
				<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
			</div>
		</script>

		<script id="timetable-week-template" type="text/x-handlebars-template">
			<tr>
				<td>{{week}} <input type="checkbox" name="schedule[{{week}}][]" value="no_side_effect" onchange="toggleWeek(this);" data-week="{{week}}"></td>
				<td><input type="checkbox" name="schedule[{{week}}][]" value="mon" disabled class="day_selector"></td>
				<td><input type="checkbox" name="schedule[{{week}}][]" value="tue" disabled class="day_selector"></td>
				<td><input type="checkbox" name="schedule[{{week}}][]" value="wed" disabled class="day_selector"></td>
				<td><input type="checkbox" name="schedule[{{week}}][]" value="thu" disabled class="day_selector"></td>
				<td><input type="checkbox" name="schedule[{{week}}][]" value="fri" disabled class="day_selector"></td>
				<td><input type="checkbox" name="schedule[{{week}}][]" value="sat" disabled class="day_selector"></td>
				<td><input type="checkbox" name="schedule[{{week}}][]" value="sun" disabled class="day_selector"></td>
			</tr>
		</script>
	</div>

	<script src="/common/js/fullcalendar.min.js"></script>

	<script src="/common/js/jquery/ui.min/jquery-ui.min.js"></script>
	<script src="/common/js/jquery/jquery.reveal.js"></script>

	<script src="/js/Controllers/Trip.js"></script>
	<script src="/js/Controllers/Class.js"></script>
	<script src="/js/Controllers/Boat.js"></script>
	<script src="/js/Controllers/Session.js"></script>
	<script src="/js/Controllers/Timetable.js"></script>

	<script src="/tabs/scheduling/js/script.js"></script>
</div>
