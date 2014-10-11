<div id="wrapper">
	<div class="row">
		<div class="box100">
			<label class="purpleb">Trips</label>
			<div id='trips' class="padder dragganle-items">
				<div class="yellow-helper">
					Please drag a trip onto an appropriate date to be activated.
				</div>
				<ul style="padding-left: 0;">
					<script id="trip-list" type="text/x-handlebars-template">
						{{#each trips}}
							<li class="droppable-event">
								<div class='trip-event' data-id="{{id}}">
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

	<div id='calendar'></div>
	<div style='clear:both'></div>

	<div id="modalWindows" style="height: 0">
		<script id="session-template" type="text/x-handlebars-template">
			<div id="modal-{{id}}" class="reveal-modal">
				<p style="text-transform: uppercase; float: right; line-height: 2.8em;">
					{{#if isNew}}
						New session
					{{else}}
						Update session
					{{/if}}
				</p>
				<h2{{#if session.deleted_at}} style="text-decoration: line-through;"{{/if}}>{{{trip.name}}}</h2>
				{{#if session.deleted_at}} <h3 style="color: red;">Deactivated</h3>{{/if}}
				<table style="margin-top: 2em;">
					<tr>
						<td><strong>Date</strong></td>
						<td>{{date start}}</td>
					</tr>
					<tr>
						<td><strong>Start time</strong></td>
						<td>
							<small><span style="display: inline-block; width: 49px">hours</span><span>minutes</span></small><br>
							<input type="text" placeholder="hh" value="{{hours start}}"   class="starthours"   style="width: 25px;">:
							<input type="text" placeholder="mm" value="{{minutes start}}" class="startminutes" style="width: 25px;"> h
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

				<p>
					Boat for this session:&nbsp;
					<select name="boat_id" class="boatSelect">
						{{#each boats}}
							<option value="{{id}}" {{#if selected}}selected{{/if}}>{{{name}}}</option>
						{{/each}}
					</select>
				</p>

				{{#unless isNew}}
				{{#unless timetable_id}}
					<div>
						<strong>Define repeating timetable</strong>
					</div>
					<form style="padding: 1em; border: 3px dashed lightgray; overflow: auto;">
						<table>
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
							<tr>
								<td>2</td>
								<td><input type="checkbox" name="schedule[2][]" value="mon"></td>
								<td><input type="checkbox" name="schedule[2][]" value="tue"></td>
								<td><input type="checkbox" name="schedule[2][]" value="wed"></td>
								<td><input type="checkbox" name="schedule[2][]" value="thu"></td>
								<td><input type="checkbox" name="schedule[2][]" value="fri"></td>
								<td><input type="checkbox" name="schedule[2][]" value="sat"></td>
								<td><input type="checkbox" name="schedule[2][]" value="sun"></td>
							</tr>
						</table>
						<input type="hidden" name="_token">
						<input type="hidden" name="session_id" value="{{session.id}}">
						Iterations: <input type="number" name="iterations" value="0" style="width: 50px;"> (default: 1.5 years worth)
						<button class="bttn big-bttn blueb create-timetable-button" style="float: right;">Create timetable</button>
					</form>
				{{/unless}}
				{{/unless}}

				<div style="margin-top: 1em; text-align: right">
					{{#if isNew}}
						<a class="close-modal" title="Abort" style="margin-right: 2em;">Cancel</a>
						<button class="submit-session bttn big-bttn blueb">Activate</button>
					{{else}}
						<button class="delete-session bttn redb" style="float:left; line-height: 2em; margin-top: 0.7em;">Delete</button>
						<a class="close-modal" title="Abort" style="margin-right: 2em;">Cancel</a>
						<button class="update-session bttn big-bttn blueb">Update</button>
					{{/if}}
				</div>
				<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
			</div>
		</script>
	</div>
</div>

<script src="js/Controllers/Trip.js"></script>
<script src="js/Controllers/Boat.js"></script>
<script src="js/Controllers/Session.js"></script>
<script src="js/Controllers/Timetable.js"></script>
<script src="/common/js/jquery.reveal.js"></script>
<script src="tabs/activate-trip/js/script.js" type="text/javascript"></script>
