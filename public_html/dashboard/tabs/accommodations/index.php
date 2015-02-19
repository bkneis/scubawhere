<div id="wrapper" class="clearfix">
<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Do you own or manage any accommodations? If so, click 'next'. If not, then just 'skip' this step."></div>
	<div class="col-md-4">
		<div class="panel panel-default" id="accommodations-list" data-step="6" data-position="right" data-intro="Once an accommodation is saved, you will see it in your list. Click on an accommodation to view/edit the details.">
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
							<p id="no-accommodations">No accommodations available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>
	</div>

	<div class="col-md-8">
		<div class="panel panel-default" id="accommodation-form-container" data-step="2" data-position="left" data-intro="To get started, enter the room name and description.">
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

						<div class="form-row" id="acom-base" data-step="3" data-position="top" data-intro="Here you can set any price changes that start from a certain date.">
							<p><strong>Set base prices for this accommodation:</strong></p>
							{{#each base_prices}}
								{{> price_input}}
							{{/each}}
							<button id="add-base-price" class="btn btn-success text-uppercase add-base-price"> &plus; Add base price</button>
						</div>

						<div class="form-row" id="acom-season" data-step="4" data-position="left" data-intro="If you have prices that change throughout the year, you can ajust your prices depening on the seasons">
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

						<div class="form-row" id="acom-rooms" data-step="5" data-position="top" data-intro="Lastly enter the number of rooms you have available. If the room is a dorm room, then treat each dorm room. Lastly, click 'save' to add your accommodation.">
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
