<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0; height:0; margin-left:50%;"></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="accommodations-list">
				<div class="panel-heading">
					<h4 class="panel-title">Available Accommodation</h4>
				</div>
				<div class="panel-body" id="accommodation-list-container">
					<button id="change-to-add-accommodation" class="btn btn-success text-uppercase">&plus; Add Accommodation</button>
					<script type="text/x-handlebars-template" id="accommodation-list-template">
						<ul id="accommodation-list" class="entity-list">
							{{#each accommodations}}
								<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{pricerange base_prices prices}}</li>
							{{else}}
								<p id="no-accommodations">No accommodations available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="accommodation-form-container">
				<script type="text/x-handlebars-template" id="accommodation-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} accommodation</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-accommodation-form">
							<div class="form-row" id="acom-name">
								{{#if update}}
									<span class="btn btn-danger pull-right remove-accommodation">Remove</span>
								{{/if}}
                                <input type="hidden" name="force" value="{{deletable}}">
								<label for="name" class="field-label">Room Name : <span class="text-danger">*</span></label>
								<input id="room-name" type="text" name="name" value="{{{name}}}">
							</div>

							<div class="form-row">
								<label for="description" class="field-label">Accommodation Description : </label>
								<textarea id="acom-description" name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							<div class="form-row" id="acom-base">
								<p><strong>Price per night : <span class="text-danger">*</span></strong></p>
								{{#each base_prices}}
									{{> price_input}}
								{{/each}}
								{{!--<button id="add-base-price" class="btn btn-default btn-sm add-base-price"> &plus; Add another base price</button>--}}
							</div>

							<div class="form-row" id="acom-season">
								<label>
									<input id="acom-season-price" type="checkbox" onchange="showMe('#seasonal-prices-list', this);" {{#if prices}} checked{{/if}}>
									Add seasonal price changes?
								</label>
								<div class="dashed-border" id="seasonal-prices-list" {{#unless prices}} style="display: none;"{{/unless}}>
									<h3>Seasonal price changes</h3>
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

							{{!--<div class="form-row" id="acom-rooms">
								<label for="capacity" class="field-label">Number of Rooms : </label>
								<input id="room-amount" type="number" name="capacity" value="{{capacity}}" style="width: 55px;" min="1" step="1">
							</div>--}}

							<input type="hidden" name="capacity" value="1">

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
	<script src="/tabs/accommodations/js/handlebars.runtime.js"></script>
    <script type="text/javascript" src="/js/bootstrap-tour-standalone.min.js"></script>    
	<script type="text/javascript" src="/js/tour.js"></script>
	<script src="/tabs/accommodations/js/script.js"></script>

</div>
