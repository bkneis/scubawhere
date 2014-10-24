<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Current tickets</label>
			<div class="padder" id="ticket-list-container">
				<!--<div class="yellow-helper">
					Select an ticket to change its details.
				</div>-->
				<button id="change-to-add-ticket" style="padding: 0.5em 1em; margin: 0.4em;" class="bttn greenb">&plus; Add Ticket</button>
				<script type="text/x-handlebars-template" id="ticket-list-template">
					<ul id="ticket-list" class="entity-list">
						{{#each tickets}}
							<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{currency}} {{decimal_price}}</li>
						{{else}}
							<p>No tickets available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>

		<div class="box70" id="ticket-form-container">

			<script type="text/x-handlebars-template" id="ticket-form-template">
				<label class="dgreyb">{{task}} ticket</label>
				<div class="padder">
					<form id="{{task}}-ticket-form">
						<input type="hidden" name="id" value="{{id}}">
						<div class="form-row">
							<label class="field-label">Ticket Name</label>
							<input type="text" name="name" value="{{{name}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Ticket Description</label>
							<textarea name="description">{{{description}}}</textarea>
						</div>

						<div class="form-row">
							<label class="field-label">Ticket Price</label>
							<select name="currency">
								<option value="GBP">GBP</option>
							</select>
							<input type="text" name="price" value="{{decimal_price}}">
						</div>

						<div class="form-row">
							<h3>Please select the trips that this ticket should be eligable for:</h3>
							{{#each available_trips}}
								<label style="margin: 0.5em 0 0.5em 4em; display: block;">
									<input type="checkbox" name="trips[]" value="{{id}}"{{inArray id ../trips ' checked'}}>
									{{{name}}}
								</label>
							{{/each}}
						</div>

						<div class="form-row">

							<label style="display: block;">
								<input type="checkbox" onclick="toggleShowBoats()"{{#if hasBoats}} checked{{/if}}><strong>Limit the ticket to certain boats?</strong>
							</label>
							<input type="hidden" name="boats[]" value="">
							<div class="dashed-border" id="boat-select"{{#unless hasBoats}} style="display:none;"{{/unless}}>
								<p>Please select the boats that you want this ticket to be eligible for:</p>
								{{#each available_boats}}
									<label>
										<input type="checkbox" onchange="toggleBoatSelect(this);"{{inArray id ../boats ' checked'}}>
										{{name}}
										<select class="accom-select" name="boats[{{id}}]" style="margin-left: 1em;"{{inArray id ../boats '' ' disabled'}}>
											<option value="">All room types</option>
											{{#if accommodations}}
												<optgroup label="Limit to:">
													{{#each accommodations}}
														<option value="{{id}}"{{isEqualDeepPivot id ../../../boats ../../id 'accommodation_id' ' selected'}}>{{name}}</option>
													{{/each}}
												</optgroup>
											{{/if}}
										</select>
									</label>
								{{/each}}
							</div>

						</div>

						<input type="hidden" name="_token">
						<input type="submit" class="bttn blueb big-bttn" id="{{task}}-ticket" value="{{task}} Ticket">

					</form>
				</div>
			</script>

		</div>

		<script type="text/x-handlebars-template" id="errors-template">
			<div class="yellow-helper errors" style="color: #E82C0C;">
				<strong>There are a few problems with the form:</strong>
				<ul>
					{{#each errors}}
						<li>{{this}}</li>
					{{/each}}
				</ul>
			</div>
		</script>
	</div>
</div>

<script src="/dashboard/js/Controllers/Boat.js"></script>
<script src="/dashboard/js/Controllers/Trip.js"></script>
<script src="/dashboard/js/Controllers/Ticket.js"></script>
<script src="tabs/tickets/js/script.js"></script>
