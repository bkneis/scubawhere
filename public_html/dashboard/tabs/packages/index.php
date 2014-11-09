<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Available packages</label>
			<div class="padder" id="package-list-container">
				<!--<div class="yellow-helper">
					Select an package to change its details.
				</div>-->
				<button id="change-to-add-package" style="padding: 0.5em 1em; margin: 0.4em;" class="bttn greenb">&plus; Add Package</button>
				<script type="text/x-handlebars-template" id="package-list-template">
					<ul id="package-list" class="entity-list">
						{{#each packages}}
							<li data-id="{{id}}"{{#if trashed}} class="trashed"{{/if}}><strong>{{{name}}}</strong> | {{count tickets}} tickets</li>
						{{else}}
							<p>No packages available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>

		<div class="box70" id="package-form-container">

			<script type="text/x-handlebars-template" id="package-form-template">
				<label class="dgreyb">{{task}} package</label>
				<div class="padder">
					<form id="{{task}}-package-form">
						<div class="form-row">
							<label class="field-label">Package Name</label>
							<input type="text" name="name" value="{{{name}}}">
							{{#if trashed}}
								<strong style="color: #FF7163;">(Deactivated)</strong>
							{{/if}}

							{{#if update}}
								{{#if trashed}}
									<span class="box-tool blueb restore-package" style="color: white;">Restore</span>
								{{else}}
									{{#if has_bookings}}
										<span class="questionmark-tooltip" style="float: right;" title="This package has been booked at least once. That is why it can only be deactivated and not removed.">?</span>
										<span class="box-tool redb deactivate-package" style="color: white;">Deactivate</span>
									{{else}}
										<span class="box-tool redb remove-package" style="color: white;">Remove</span>
									{{/if}}
								{{/if}}
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
							<strong>Set base prices for this package:</strong>
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
								<h3>Seasonal price changes</h3>
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
							<label class="field-label">Max. group size per boat</label>
							<input type="number" name="capacity" value="{{capacity}}" style="width: 55px;" min="0" step="1" placeholder="none">
						</div>

						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<input type="submit" class="bttn blueb big-bttn" id="{{task}}-package" value="{{task}} Package">

					</form>
				</div>
			</script>

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

<script src="/dashboard/js/Controllers/Ticket.js"></script>
<script src="/dashboard/js/Controllers/Package.js"></script>
<script src="tabs/packages/js/script.js"></script>
