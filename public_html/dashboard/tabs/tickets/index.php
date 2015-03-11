<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Now, we need to add your tickets. A ticket can be valid for many trips. A ticket is a single reservation for a trip. For an educational course please create a package (see next page)."></div>

	<div class="row">
		<div class="col-md-4">
			<div id="tickets-list-div" class="panel panel-default" data-step="7" data-position="right" data-intro="Once a ticket is saved, you will see it in your list. Click on a ticket to view/edit the details.">
				<div class="panel-heading">
					<h4 class="panel-title">Available Tickets</h4>
				</div>
				<div class="panel-body" id="ticket-list-container">
					<button id="change-to-add-ticket" class="btn btn-success text-uppercase">&plus; Add Ticket</button>
					<script type="text/x-handlebars-template" id="ticket-list-template">
						<ul id="ticket-list" class="entity-list">
							{{#each tickets}}
								<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{pricerange base_prices prices}}</li>
							{{else}}
								<p id="no-tickets">No tickets available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="ticket-form-container" data-step="2" data-position="left" data-intro="Enter a name, description and base price for the ticket.">
				<script type="text/x-handlebars-template" id="ticket-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} Ticket</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-ticket-form">
							<div class="form-row">
								<label class="field-label">Ticket Name</label>
								<input id="ticket-name" type="text" name="name" value="{{{name}}}">

								{{!-- TODO Enable deletion of ticktes, including all necessary checks and propagation
								{{#if update}}
									<span class="btn btn-danger pull-right remove-ticket">Remove</span>
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
								<button class="btn btn-success text-uppercase add-base-price"> &plus; Add base price</button>
							</div>

							<div class="form-row" id="tickets-seasonal">
								<label>
									<input id="seasonal-prices-checkbox" type="checkbox" onchange="showMe('#seasonal-prices-list', this);"{{#if prices}} checked{{/if}}>
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
									<button class="btn btn-success text-uppercase add-price"> &plus; Add seasonal price</button>
								</div>
							</div>

							<div class="form-row" id="tickets-trips" data-step="3" data-position="top" data-intro="Select which trips a ticket can be used for. The ticket is valid for only 1 trip.">
								<h4>Please select the trips that this ticket should be eligable for:</h4>
								{{#each available_trips}}
									<p style="margin-left: 4em;">
										<label>
											<input type="checkbox" name="trips[]" value="{{id}}"{{inArray id ../trips ' checked' ''}}>
											{{{name}}}
										</label>
									</p>
								{{/each}}
							</div>

							<div class="form-row" id="tickets-boats" data-step="4" data-position="top" data-intro="You can also limit the ticket to be used for specific boats.">
								<label style="display: block;">
									<input id="tickets-boats-checkbox" type="checkbox" onclick="showMe('#boat-select', this)"{{#if hasBoats}} checked{{/if}}><strong>Limit the ticket to certain boats?</strong>
								</label>
								<div class="dashed-border" id="boat-select"{{#unless hasBoats}} style="display:none;"{{/unless}}>
									<p>Please select the boats that you want this ticket to be eligible for:</p>
									{{#each available_boats}}
										<p>
											<label>
												<input type="checkbox" name="boats[]" value="{{id}}"{{inArray id ../boats ' checked' ''}}>
												{{name}}
											</label>
										</p>
									{{/each}}
								</div>
							</div>

							<div class="form-row" id="tickets-boatrooms" data-step="6" data-position="top" data-intro="You can also limit the ticket to be used on specific cabins for overnight trips. Click 'Save' to create the ticket.">
								<label style="display: block;">
									<input id="tickets-boatroom-checkbox" type="checkbox" onclick="showMe('#boatroom-select', this)"{{#if hasBoatrooms}} checked{{/if}}><strong>Limit the ticket to certain cabins?</strong>
								</label>
								<div class="dashed-border" id="boatroom-select"{{#unless hasBoatrooms}} style="display:none;"{{/unless}}>
									<p>Please select the cabins that you want this ticket to be eligible for:</p>
									{{#each available_boatrooms}}
										<p>
											<label>
												<input type="checkbox" name="boatrooms[]" value="{{id}}"{{inArray id ../boatrooms ' checked' ''}}>
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

							<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" id="{{task}}-ticket" value="SAVE">

						</form>
					</div>
				</script>
			</div>
		</div>
	</div><!-- .row -->

	<script type="text/x-handlebars-template" id="price-input-template">
		<p{{#unless decimal_price}} class="new_price"{{/unless}}>
			<span class="currency">{{currency}}</span>
			{{#if decimal_price}}
				<span class="amount">{{decimal_price}}</span>
			{{else}}
				<input type="number" id="acom-price" name="{{#if isBase}}base_{{/if}}prices[{{id}}][new_decimal_price]" placeholder="00.00" min="0" step="0.01" style="width: 100px;">
			{{/if}}

			{{#unless isAlways}}
				{{#if decimal_price}}
					from <big>{{from}}</big>
				{{else}}
					from <input type="text" name="{{#if isBase}}base_{{/if}}prices[{{id}}][from]" class="datepicker" data-date-format="YYYY-MM-DD" value="{{from}}" style="width: 125px;">
				{{/if}}
			{{else}}
				from <strong>the beginning of time</strong>
				{{#unless decimal_price}}
					<input type="hidden" name="{{#if isBase}}base_{{/if}}prices[{{id}}][from]" value="{{from}}">
				{{/unless}}
			{{/unless}}

			{{#unless isBase}}
				{{#if decimal_price}}
					until <big>{{until}}</big>
				{{else}}
					until <input type="text" name="{{#if isBase}}base_{{/if}}prices[{{id}}][until]" class="datepicker" data-date-format="YYYY-MM-DD" value="{{until}}" style="width: 125px;">
				{{/if}}
			{{/unless}}

			{{#unless isAlways}}
				{{#unless decimal_price}}
					<button class="btn btn-danger remove-price">&#215;</button>
				{{/unless}}
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

	<script src="/js/Controllers/Boat.js"></script>
	<script src="/js/Controllers/Boatroom.js"></script>
	<script src="/js/Controllers/Trip.js"></script>
	<script src="/js/Controllers/Ticket.js"></script>
	<script src="/tabs/tickets/js/script.js"></script>
</div>
