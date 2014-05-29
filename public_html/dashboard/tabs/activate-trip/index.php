<div id="wrapper">
	<div class="row">
		<div class="box50">
			<label class="purpleb">Trips</label>
			<div id='trips' class="padder dragganle-items">
				<div class="yellow-helper">
					Please drag a trip onto an appropriate date to be activated.
				</div>
				<ul>
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
				<p>New session</p>
				<h2>{{{trip.name}}}</h2>

				<table>
					<tr>
						<td><strong>Date</strong></td>
						<td>{{date start}}</td>
					</tr>
					<tr>
						<td><strong>Start time</strong></td>
						<td>
							<input type="text" placeholder="hh" value="{{hours start}}"   class="starthours"   size="2">:
							<input type="text" placeholder="mm" value="{{minutes start}}" class="startminutes" size="2">
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

				<div style="margin-top: 1em; font-size: 1.2em; text-align: center">
					{{#if isNew}}
						<button class="submit-session buttn blueb">Activate</button>
					{{else}}
						Sorry, updating a session is not yet possible
						<!-- <button class="update-session buttn blueb">Update</button> -->
					{{/if}}
				</div>
				<a class="close-reveal-modal" title="Abort">&#215;</a>
			</div>
		</script>
	</div>
</div>

<script src="js/Controllers/Trip.js"></script>
<script src="js/Controllers/Boat.js"></script>
<script src="js/Controllers/Sessions.js"></script>
<script src="/common/js/jquery.reveal.js"></script>
<script src="tabs/activate-trip/js/script.js" type="text/javascript"></script>
