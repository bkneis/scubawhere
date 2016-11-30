<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div> 
	<div class="row">
		<div class="col-md-4">
			<div id="tickets-list-div" class="panel panel-default">
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
			<div class="panel panel-default" id="ticket-form-container">
				<script type="text/x-handlebars-template" id="ticket-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} Ticket</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-ticket-form">
							<div class="form-row">
								{{#if update}}
									<span class="btn btn-danger pull-right remove-ticket">Remove</span>
                                    <input type="hidden" name="deleteable" value="{{deleteable}}">
								{{/if}}
								<label class="field-label">Ticket Name : <span class="text-danger">*</span></label>
								<input id="ticket-name" type="text" name="name" value="{{{name}}}">

								{{!-- TODO Enable deletion of ticktes, including all necessary checks and propagation
								{{#if update}}
									<span class="btn btn-danger pull-right remove-ticket">Remove</span>
								{{/if}}
								--}}
							</div>

							<div class="form-row">
								<label>
									<input type="checkbox" name="only_packaged" value="1" onchange="togglePrices(this)" {{#if only_packaged}} checked="checked"{{/if}}> Limit ticket to only be bookable in Courses or Packages
								</label>
							</div>

							<div id="container-ticket-description" class="form-row">
								<label class="field-label">Ticket Description</label>
								<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							<div class="form-row prices"{{#if only_packaged}} style="display: none;"{{/if}}>
								<p><strong>Ticket price : <span class="text-danger">*</span></strong></p>
								{{#each base_prices}}
									{{> price_input}}
								{{/each}}
								{{!--<button class="btn btn-default btn-sm add-base-price"> &plus; Add another base price</button>--}}
							</div>

							<div class="form-row prices"{{#if only_packaged}} style="display: none;"{{/if}} id="tickets-seasonal">
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
									<button class="btn btn-default btn-sm add-price"> &plus; Add another seasonal price</button>
								</div>
							</div>

							<div class="form-row" id="tickets-trips">
								<h4>Please select the trips that this ticket should be eligible for:</h4>
								<div id="ticket-selectList" class="form-row">
								{{#each available_trips}}
									<label class="trip-select{{inArray id ../trips ' checked' ''}}">
										<input type="checkbox" name="trips[]" onchange="changeParent(this)" value="{{id}}"{{inArray id ../trips ' checked' ''}}>
										{{{name}}}
									</label>
								{{else}}
									<div class="alert alert-danger clearfix">
										<i class="fa fa-exclamation-triangle fa-3x fa-fw pull-left"></i>
										<p class="pull-left">
											<strong>No trips available!</strong><br>
											Please go to <a href="#trips">Trips</a> to define open water trips.
										</p>
									</div>
								{{/each}}
								</div>
							</div>

							<div class="form-row" id="tickets-boats">
								<label style="display: block;">
									<input id="tickets-boats-checkbox" type="checkbox" onclick="showMe('#boat-select', this)"{{#if hasBoats}} checked{{/if}}> <strong>Limit the ticket to certain boats?</strong>
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

							<div class="form-row" id="tickets-boatrooms">
								<label style="display: block;">
									<input id="tickets-boatroom-checkbox" type="checkbox" onclick="showMe('#boatroom-select', this)"{{#if hasBoatrooms}} checked{{/if}}><strong> Limit the ticket to certain cabins?</strong>
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

							<div class="form-row" id="tickets-availability">
								<label style="display: block;">
									<input id="tickets-availability-checkbox" type="checkbox" onclick="showMe('#availability-select', this)"{{#if hasAvailability}} checked{{/if}}><strong> Limit the ticket's availability?</strong>
								</label>
								<div class="dashed-border" id="availability-select"{{#unless hasAvailability}} style="display:none;"{{/unless}}>
									<p>This ticket should only be available for selection between:</p>
									From: <input type="text" class="datepicker" data-date-format="YYYY-MM-DD" name="available_from" value="{{available_from}}"> &nbsp; &nbsp; Until: <input type="text" class="datepicker" data-date-format="YYYY-MM-DD" name="available_until" value="{{available_until}}">

									<p>&nbsp;</p>

									<p>This ticket should only be available for trips that start between:</p>
									From: <input type="text" class="datepicker" data-date-format="YYYY-MM-DD" name="available_for_from" value="{{available_for_from}}"> &nbsp; &nbsp; Until: <input type="text" class="datepicker" data-date-format="YYYY-MM-DD" name="available_for_until" value="{{available_for_until}}">
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
					<li>{{{this}}}</li>
				{{/each}}
			</ul>
		</div>
	</script>

    <link rel="stylesheet" type="text/css" href="/css/bootstrap-tour-standalone.min.css">
    <script type="text/javascript" src="/js/bootstrap-tour-standalone.min.js"></script>    
	<script type="text/javascript" src="/js/tour.js"></script>
	<script src="/tabs/tickets/js/script.js"></script>
</div>
