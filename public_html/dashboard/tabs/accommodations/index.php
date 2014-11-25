<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Available accommodations</label>
			<div class="padder" id="accommodation-list-container">
				<!--<div class="yellow-helper">
					Select an accommodation to change its details.
				</div>-->
				<button id="change-to-add-accommodation" style="padding: 0.5em 1em;" class="bttn greenb">&plus; Add Accommodation</button>
				<script type="text/x-handlebars-template" id="accommodation-list-template">
					<ul id="accommodation-list" class="entity-list">
						{{#each accommodations}}
							<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{capacity}} | {{pricerange base_prices prices}}</li>
						{{else}}
							<p>No accommodations available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>

		<div class="box70" id="accommodation-form-container">

			<script type="text/x-handlebars-template" id="accommodation-form-template">
				<label class="dgreyb">{{task}} accommodation</label>
				<div class="padder">
					<form id="{{task}}-accommodation-form">
						<div class="form-row">
							<label class="field-label">Accommodation Name</label>
							<input type="text" name="name" value="{{{name}}}">

							{{#if update}}
								<span class="box-tool redb {{#if has_bookings}}deactivate-accommodation{{else}}remove-accommodation{{/if}}" style="color: white;">Remove</span>
							{{/if}}
						</div>

						<div class="form-row">
							<label class="field-label">Accommodation Description</label>
							<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
						</div>

						<div class="form-row">
							<strong>Set base prices for this accommodation:</strong>
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
							<label class="field-label">Number of Rooms/Beds</label>
							<input type="number" name="capacity" value="{{capacity}}" style="width: 55px;" min="1" step="1">
						</div>

						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<input type="submit" class="bttn blueb big-bttn" id="{{task}}-accommodation" value="{{task}} Accommodation">

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

<script src="/dashboard/js/Controllers/Accommodation.js"></script>
<script src="tabs/accommodations/js/script.js"></script>
