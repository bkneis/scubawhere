<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Now you need to add your packages. A package consists of many tickets and is great for offering deals that include more than 1 ticket. A package is also used for educational courses that include multiple trips."></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="packages-list-div" data-step="7" data-position="right" data-intro="Once a package is saved, you will see it in your list. Click on a package to view/edit the details.">
				<div class="panel-heading">
					<h4 class="panel-title">Available Packages</h4>
				</div>
				<div class="panel-body" id="package-list-container">
					<button id="change-to-add-package" class="btn btn-success text-uppercase">&plus; Add Package</button>
					<script type="text/x-handlebars-template" id="package-list-template">
						<ul id="package-list" class="entity-list">
							{{#each packages}}
								<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{count tickets}} tickets</li>
							{{else}}
								<p id="no-packages">No packages available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="package-form-container" data-step="2" data-position="left" data-intro="Enter a name, description and price for the package.">
				<script type="text/x-handlebars-template" id="package-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} Package</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-package-form">
							<div class="form-row">
								<label class="field-label">Package Name</label>
								<input id="package-name" type="text" name="name" value="{{{name}}}">

								{{#if update}}
									<span class="btn btn-danger pull-right remove-package">Remove</span>
								{{/if}}
							</div>

							<div class="form-row">
								<label class="field-label">Package Description</label>
								<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							<div id="package-tickets" class="form-row ticket-list" data-step="3" data-position="left" data-intro="Now, select the tickets that you want to include in the package. Once you select another ticket, another drop down box will appear to allow you to add another ticket. If you are finished adding tickets, leave the last one blank.">
								<strong>Select tickets to be included in this package:</strong>

								{{#if update}}
									{{> tickets_template}}
								{{else}}
									{{> ticket_select}}
								{{/if}}
							</div>

							<div id="package-base" class="form-row">
								<p><strong>Set base prices for this package:</strong></p>
								{{#each base_prices}}
									{{> price_input}}
								{{/each}}
								<button class="btn btn-success text-uppercase add-base-price"> &plus; Add base price</button>
							</div>

							<div id="package-seasonal" class="form-row">
								<label>
									<input type="checkbox" onchange="showMe('#seasonal-prices-list', this);"{{#if prices}} checked{{/if}}>
									Add seasonal price changes?
								</label>
								<div class="dashed-border" id="seasonal-prices-list"{{#unless prices}} style="display: none;"{{/unless}}>
									<h3>Seasonal price changes</h3>
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

							{{#if update}}
								<input type="hidden" name="id" value="{{id}}">
							{{/if}}
							<input type="hidden" name="_token">

							<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" id="{{task}}-package" value="SAVE">

						</form>
					</div>
				</script>
			</div>
		</div>
	</div><!-- .row -->

	<script type="text/x-handlebars-template" id="ticket-select-template">
		<p>
			<select class="ticket-select">
				<option value="0">Select a ticket</option>
				{{#each available_tickets}}
					<option value="{{id}}"
						{{#if ../existing}}
							{{selected ../../id}}
						{{/if}}
					>{{{name}}}</option>
				{{/each}}
			</select>

			Quantity: &nbsp;<input type="number" class="quantity-input"{{#if pivot.quantity}} name="tickets[{{id}}][quantity]"{{else}} disabled{{/if}} value="{{pivot.quantity}}" min="1" step="1" style="width: 50px;">
			{{!--
			<span class="ticket-prices" data-default="-">
				{{#if existing}}
					{{#each prices}}
						<span style="border: 1px solid lightgray; padding: 0.25em 0.5em;">{{from}} - {{until}}: {{currency}} {{multiply ../pivot.quantity decimal_price}}</span>
					{{/each}}
				{{else}}
				-
				{{/if}}
			</span>
			--}}
		</p>
	</script>

	<script type="text/x-handlebars-template" id="tickets-template">
		{{#each tickets}}
			<p>
				<big class="margin-right">{{{name}}}</big> Quantity: <big class="margin-right">{{pivot.quantity}}</big>
			</p>
		{{/each}}
	</script>

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

	<script src="/js/Controllers/Ticket.js"></script>
	<script src="/js/Controllers/Package.js"></script>
	<script src="/tabs/packages/js/script.js"></script>
</div>
