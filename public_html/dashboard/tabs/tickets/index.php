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
							<li data-id="{{id}}"><strong>{{{name}}}</strong></li>
						{{else}}
							<p>No tickets available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>

		<script id="trip-list-template" type="text/x-handlebars-template">
			{{#each trips}}
				<input type="checkbox" name="trips[]" value="{{id}}"><label class="field-label">{{{name}}}</label>
			{{/each}}
		</script>

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
									<option value="{{id}}" {{selected name}}>{{name}}</option>
								{{/each}}
							</optgroup>
						{{/if}}
					</select>
				</label>
			{{/each}}
		</script>

		<div class="box70" id="ticket-form-container">

			<script type="text/x-handlebars-template" id="ticket-form-template">
				<label class="dgreyb">{{task}} ticket</label>
				<div class="padder">
					<form id="{{task}}-ticket-form">
						<div class="form-row">
							<label class="field-label">Ticket Name</label>
							<input type="text" name="name" value="{{{name}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Ticket Description</label>
							<input type="text" name="price" value="{{{description}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Ticket Price</label>
							<select name="currency">
								<option value="GBP">GBP</option>
							</select>
							<input type="text" name="price" value="{{{price}}}">
						</div>

						<div class="form-row">
							<p><strong>Please select the trips that this ticket should be eligable for:</strong></p>
							<div id="trip-select" style="margin-bottom: 1.5em; margin-left: 4em;">
								
							</div>
						</div>

						<div class="form-row">

						<label style="display: block;">
							<input type="checkbox" onclick="toggleShowBoats();">
							<b>Limit the ticket to certain boats?</b>
						</label>
						<div class="box50" id="boat-select" style="display:none;">
							<p>Please select the boats that you want this ticket to be eligible for:</p>
			
						</div>

					</div>

						<input type="hidden" name="_token">

						<button class="bttn blueb big-bttn" id="{{task}}-ticket">{{task}} Ticket</button>

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
