<div id="wrapper">

	<script id="saved-tickets-template" type="text/x-handlebars-template">
		{{#if tickets}}
			<table>
				<thead>
					<tr>
						<th>Ticket name</th>
						<th style="width: 100px;">Price</th>
						<th>Description</th>
						<th>Trips</th>
					</tr>
				</thead>
				<tbody>

					{{#each tickets}}
						<tr>
							<td>{{{name}}}</td>
							<td>{{{currency}}} {{{price}}}</td>
							<td>{{{description}}}</td>
							<td>
								<select>
									<option>{{count trips}} trip(s) selected</option>
									{{#each trips}}
										<option>{{{name}}}</option>
									{{/each}}
								</select>
							</td>
						</tr>
					{{/each}}

				</tbody>
			</table>
		{{else}}
			No tickets created yet.
		{{/if}}
	</script>

	<div class="box100">
		<label class="dgreyb">Saved Tickets</label>
		<div id="saved-tickets">
			<!-- This is where the above goes when complied... -->
		</div>
	</div>


	<div class="box100">
		<label class="dgreyb">Create New Ticket</label>

		<div class="padder">

			<!-- Add new tickets -->
			<form id="new-ticket-form">
				<div class="form-row">
					<label class="field-label">Ticket Name</label>
					<input type="text" name="name">
				</div>

				<div class="form-row">
					<label class="field-label">Ticket Price</label>
					<input type="text" name="price">
					<select name="currency">
						<option value="GBP">GBP</option>
					</select>
				</div>

				<div class="form-row">
					<label>Ticket Description</label>
					<textarea name="description"></textarea>
				</div>

				<div class="form-row">
				</div>

				<div class="form-row">
					<p><strong>Please select the trips that this ticket should be eligable for:</strong></p>
					<div id="trip-select" style="margin-bottom: 1.5em; margin-left: 4em;">
						<script id="trips-template" type="text/x-handlebars-template">
							{{#each trips}}
								<label><input type="checkbox" name="trips[]" value="{{id}}"> {{{name}}}</label><br>
							{{/each}}
						</script>
					</div>
				</div>

				<div class="form-row">

					<label style="display: block;">
						<input type="checkbox" onclick="toggleShowBoats();">
						<b>Limit the ticket to certain boats?</b>
					</label>
					<div class="box50" id="boat-select" style="display:none;">
						<p>Please select the boats that you want this ticket to be eligable for:</p>
						<script id="boat-template" type="text/x-handlebars-template">
							{{#each boats}}
								<label style="display: block; color: #313131;">
									<input type="checkbox" onchange="toggleBoatSelect(this);">
									{{name}}
									<select class="accom-select" name="boats[{{id}}]" style="margin-left: 1em;" disabled>
										<option value="">All room types</option>
										{{#if accommodations}}
											<optgroup label="Limit to:">
												{{#each accommodations}}
													<option value="{{id}}">{{name}}</option>
												{{/each}}
											</optgroup>
										{{/if}}
									</select>
								</label>
							{{/each}}
						</script>
					</div>

				</div>

				<input type="hidden" class="token" name="_token">
				<input type="submit" class="bttn blueb" id="save-ticket" value="Save">

			</form>
		</div>

	</div>


</div>

<script src="/dashboard/js/SetToken.js"></script>
<script src="/dashboard/js/Controllers/Boat.js"></script>
<script src="/dashboard/js/Controllers/Trip.js"></script>
<script src="/dashboard/js/Controllers/Ticket.js"></script>
<script src="tabs/tickets/js/script.js"></script>
