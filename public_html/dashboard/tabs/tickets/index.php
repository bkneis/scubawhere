<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Current tickets</label>
			<div class="padder" id="ticket-list-container">
				<!--<div class="yellow-helper">
					Select an ticket to change its details.
				</div>-->
				<button id="change-to-add-ticket" style="padding: 0.5em 1em;" class="bttn greenb">&plus; Add Ticket</button>
				<script type="text/x-handlebars-template" id="ticket-list-template">
					<ul id="ticket-list" class="entity-list">
						{{#each tickets}}
							<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{pricerange base_prices prices}}</li>
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
						<div class="form-row">
							<label class="field-label">Ticket Name</label>
							<input type="text" name="name" value="{{{name}}}">

							{{!-- TODO Enable deletion and deactivation of ticktes, including all necessary checks
							{{#if update}}
								<span class="box-tool redb {{#if has_bookings}}deactivate-ticket{{else}}remove-ticket{{/if}}" style="color: white;">Remove</span>
							{{/if}}
							--}}
						</div>

						<div class="form-row">
							<label class="field-label">Ticket Description</label>
							<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
						</div>

						<div class="form-row">
							<p><strong>Set base prices for this ticket:</strong></p>
							{{#each base_prices}}
								{{> price_input}}
							{{/each}}
							<button class="bttn greenb add-base-price"> &plus; Add base price</button>
						</div>

						<div class="form-row">
							<label>
								<input type="checkbox" onchange="showMe('#seasonal-prices-list', this);"{{#if prices}} checked{{/if}}>
								Add seasonal price changes?
							</label>
							<div class="dashed-border" id="seasonal-prices-list"{{#unless prices}} style="display: none;"{{/unless}}>
								<h4>Seasonal price changes</h4>
								{{#each prices}}
									{{> price_input}}
								{{else}}
									{{#with default_price}}
										{{> price_input}}
									{{/with}}
								{{/each}}
								<button class="bttn greenb add-price"> &plus; Add seasonal price</button>
							</div>
						</div>

						<div class="form-row">
							<h4>Please select the trips that this ticket should be eligable for:</h4>
							{{#each available_trips}}
								<p style="margin-left: 4em;">
									<label>
										<input type="checkbox" name="trips[]" value="{{id}}"{{inArray id ../trips ' checked'}}>
										{{{name}}}
									</label>
								</p>
							{{/each}}
						</div>

						<div class="form-row">
							<label style="display: block;">
								<input type="checkbox" onclick="showMe('#boat-select', this)"{{#if hasBoats}} checked{{/if}}><strong>Limit the ticket to certain boats?</strong>
							</label>
							<div class="dashed-border" id="boat-select"{{#unless hasBoats}} style="display:none;"{{/unless}}>
								<p>Please select the boats that you want this ticket to be eligible for:</p>
								{{#each available_boats}}
									<p>
										<label>
											<input type="checkbox" name="boats[]" value="{{id}}"{{inArray id ../boats ' checked'}}>
											{{name}}
										</label>
									</p>
								{{/each}}
							</div>
						</div>

						<div class="form-row">
							<label style="display: block;">
								<input type="checkbox" onclick="showMe('#boatroom-select', this)"{{#if hasBoatrooms}} checked{{/if}}><strong>Limit the ticket to certain boatrooms?</strong>
							</label>
							<div class="dashed-border" id="boatroom-select"{{#unless hasBoatrooms}} style="display:none;"{{/unless}}>
								<p>Please select the boatrooms that you want this ticket to be eligible for:</p>
								{{#each available_boatrooms}}
									<p>
										<label>
											<input type="checkbox" name="boatrooms[]" value="{{id}}"{{inArray id ../boatrooms ' checked'}}>
											{{name}}
										</label>
									</p>
								{{/each}}
							</div>
						</div>

						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<input type="submit" class="bttn blueb big-bttn" id="{{task}}-ticket" value="{{task}} Ticket">

					</form>
				</div>
			</script>

		</div>

		<script type="text/x-handlebars-template" id="price-input-template">
			<p>
				<span class="currency">{{currency}}</span>
				<input type="number" name="{{#if isBase}}base_{{/if}}prices[{{id}}][new_decimal_price]" value="{{decimal_price}}" placeholder="00.00" min="0" step="0.01" style="width: 100px;">

				{{#unless isAlways}}
					from <input type="text" name="{{#if isBase}}base_{{/if}}prices[{{id}}][from]" value="{{from}}" style="width: 125px;">
				{{else}}
					from <strong>the beginning of time</strong>
					<input type="hidden" name="{{#if isBase}}base_{{/if}}prices[{{id}}][from]" value="{{from}}">
				{{/unless}}

				{{#unless isBase}}
					until <input type="text" name="{{#if isBase}}base_{{/if}}prices[{{id}}][until]" value="{{until}}" style="width: 125px;">
				{{/unless}}

				{{#unless isAlways}}
					<button class="bttn redb remove-price">&nbsp;&#215;&nbsp;</button>
				{{/unless}}
			</p>
		</script>

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
<script src="/dashboard/js/Controllers/Boatroom.js"></script>
<script src="/dashboard/js/Controllers/Trip.js"></script>
<script src="/dashboard/js/Controllers/Ticket.js"></script>
<script src="tabs/tickets/js/script.js"></script>
