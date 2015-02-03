<div id="wrapper" class="clearfix">
	<div class="col-md-4">
		<div class="panel panel-default">
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
							<p>No packages available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>
	</div>

	<div class="col-md-8">
		<div class="panel panel-default" id="package-form-container">
			<script type="text/x-handlebars-template" id="package-form-template">
				<div class="panel-heading">
					<h4 class="panel-title">{{task}} Package</h4>
				</div>
				<div class="panel-body">
					<form id="{{task}}-package-form">
						<div class="form-row">
							<label class="field-label">Package Name</label>
							<input type="text" name="name" value="{{{name}}}">

							{{#if update}}
								<span class="btn btn-danger pull-right {{#if has_bookings}}deactivate-package{{else}}remove-package{{/if}}">Remove</span>
							{{/if}}
						</div>

						<div class="form-row">
							<label class="field-label">Package Description</label>
							<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
						</div>

						<div class="form-row ticket-list">
							<strong>Select tickets to be included in this package:</strong>
							{{#each tickets}}
								{{> ticket_select}}
							{{/each}}
							{{> ticket_select}}
						</div>

						<div class="form-row">
							<p><strong>Set base prices for this package:</strong></p>
							{{#each base_prices}}
								{{> price_input}}
							{{/each}}
							<button class="btn btn-success text-uppercase add-base-price"> &plus; Add base price</button>
						</div>

						<div class="form-row">
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

						<div class="form-row">
							<label class="field-label">Max. group size per boat</label>
							<input type="number" name="capacity" value="{{capacity}}" style="width: 55px;" min="0" step="1" placeholder="none">
							(Enter 0 or nothing for <i>no limit</i>)
						</div>

						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<input type="submit" class="btn btn-primary btn-lg text-uppercase" id="{{task}}-package" value="{{task}} Package">

					</form>
				</div>
			</script>
		</div>
	</div>

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

	<script type="text/x-handlebars-template" id="price-input-template">
		<p>
			<span class="currency">{{currency}}</span>
			<input type="number" name="{{#if isBase}}base_{{/if}}prices[{{id}}][new_decimal_price]" value="{{decimal_price}}" placeholder="00.00" min="0" step="0.01" style="width: 100px;">

			{{#unless isAlways}}
				from <input type="text" name="{{#if isBase}}base_{{/if}}prices[{{id}}][from]" class="datepicker" data-date-format="YYYY-MM-DD" value="{{from}}" style="width: 125px;">
			{{else}}
				from <strong>the beginning of time</strong>
				<input type="hidden" name="{{#if isBase}}base_{{/if}}prices[{{id}}][from]" class="datepicker" data-date-format="YYYY-MM-DD" value="{{from}}">
			{{/unless}}

			{{#unless isBase}}
				until <input type="text" name="{{#if isBase}}base_{{/if}}prices[{{id}}][until]" class="datepicker" data-date-format="YYYY-MM-DD" value="{{until}}" style="width: 125px;">
			{{/unless}}

			{{#unless isAlways}}
				<button class="btn btn-danger remove-price">&#215;</button>
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

	<script src="/dashboard/js/Controllers/Ticket.js"></script>
	<script src="/dashboard/js/Controllers/Package.js"></script>
	<script src="tabs/packages/js/script.js"></script>
</div>
