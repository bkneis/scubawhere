<div id="wrapper" class="clearfix">
<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Firstly, we need to add any accommodations that your dive centre offers. (If you don't have any then just skip this part and click next step)"></div>
	<div class="col-md-4">
		<div class="panel panel-default" id="accommodations-list" data-step="6" data-position="right" data-intro="Once an accommodation is saved you can see it in you list. Click on an accommodation to view / edit its details">
			<div class="panel-heading">
				<h4 class="panel-title">Available Accommodation</h4>
			</div>
			<div class="panel-body" id="accommodation-list-container">
				<button id="change-to-add-accommodation" class="btn btn-success text-uppercase">&plus; Add Accommodation</button>
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
	</div>

	<div class="col-md-8">
		<div class="panel panel-default" id="accommodation-form-container" data-step="2" data-position="left" data-intro="To get started, type the room name or type and description and set a base price. You can add yearly price changes to begin on certain dates in the future and also apply seasonal price changes.">
			<script type="text/x-handlebars-template" id="accommodation-form-template">
				<div class="panel-heading">
					<h4 class="panel-title">{{task}} accommodation</h4>
				</div>
				<div class="panel-body">
					<form id="{{task}}-accommodation-form">
						<div class="form-row" id="acom-name">
							<label class="field-label">Room Name</label>
							<input id="room-name" type="text" name="name" value="{{{name}}}">

							{{#if update}}
								<span class="btn btn-danger pull-right{{#if has_bookings}} deactivate-accommodation{{else}} remove-accommodation{{/if}}">Remove</span>
							{{/if}}
						</div>

						<div class="form-row">
							<label class="field-label">Accommodation Description</label>
							<textarea id="acom-description" name="description" style="height: 243px;">{{{description}}}</textarea>
						</div>

						<div class="form-row" id="acom-base" data-step="3" data-position="top" data-intro="If you expect your costs of running a trip are to increase, you can set base prices for your trip that can increase or decrease annually.">
							<p><strong>Set base prices for this accommodation:</strong></p>
							{{#each base_prices}}
								{{> price_input}}
							{{/each}}
							<button id="add-base-price" class="btn btn-success text-uppercase add-base-price"> &plus; Add base price</button>
						</div>

						<div class="form-row" id="acom-season" data-step="4" data-position="left" data-intro="Additionally you can set prices to change seasonly. If you charge accommodation prices on a 4 season year, you can easily manage its price. Just enter the dates of your season and their price.">
							<label>
								<input id="acom-season-price" type="checkbox" onchange="showMe('#seasonal-prices-list', this);"{{#if prices}} checked{{/if}}>
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

						<div class="form-row" id="acom-rooms" data-step="5" data-position="top" data-intro="Lastly enter the number of rooms you have available. If you room type is a dorm room, then this should be the number of beds. Lastly click save and your room will be added.">
							<label class="field-label">Number of Rooms/Beds</label>
							<input id="room-amount" type="number" name="capacity" value="{{capacity}}" style="width: 55px;" min="1" step="1">
						</div>

						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" id="{{task}}-accommodation" value="SAVE">

					</form>
				</div>
			</script>
		</div>
	</div>

	<script type="text/x-handlebars-template" id="price-input-template">
		<p>
			<span class="currency">{{currency}}</span>
			<input type="number" id="acom-price" name="{{#if isBase}}base_{{/if}}prices[{{id}}][new_decimal_price]" value="{{decimal_price}}" placeholder="00.00" min="0" step="0.01" style="width: 100px;">

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

	<script src="/dashboard/js/Controllers/Accommodation.js"></script>
	<script src="tabs/accommodations/js/script.js"></script>

</div>
