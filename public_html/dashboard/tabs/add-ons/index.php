<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="addon-list-div">
				<div class="panel-heading">
					<h4 class="panel-title">Available Add-ons</h4>
				</div>
				<div class="panel-body" id="addon-list-container">
					<button id="change-to-add-addon" class="btn btn-success text-uppercase">&plus; Add Add-on</button>
					<script type="text/x-handlebars-template" id="addon-list-template">
						<ul id="addon-list" class="entity-list">
							{{#each addons}}
								<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{pricerange base_prices}}</li>
							{{else}}
								<p id="no-addons">No addons available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="addon-form-container">
				<script type="text/x-handlebars-template" id="addon-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} add-on</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-addon-form">
							<div class="form-row">
								{{#if update}}
									<span class="btn btn-danger pull-right remove-addon">Remove</span>
								{{/if}}
                                <input type="hidden" name="deletable" value="{{deletable}}">
								<label class="field-label">Add-on Name</label>
								<input id="addon-name" type="text" name="name" value="{{{name}}}">
							</div>

							<div class="form-row">
								<label class="field-label">Add-on Description</label>
								<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							<div class="form-row prices">
								<p><strong>Set prices for this add-on:</strong></p>
								{{#each base_prices}}
									{{> price_input}}
								{{/each}}
								<button class="btn btn-default btn-sm add-base-price"> &plus; Add another price</button>
							</div>

							<!--<div class="form-row" id="addon-compulsory-div">
								<label class="field-label">Compulsory?</label>
								<input id="addon-compulsory" type="checkbox" name="compulsory" value="1" {{#if compulsory}}checked{{/if}}> Automatically add this add-on to every trip during booking.
							</div>-->


							{{#if update}}
								<input type="hidden" name="id" value="{{id}}">
							{{/if}}
							<input type="hidden" name="_token">

							<input type="submit" class="btn btn-lg btn-primary text-uppercase pull-right" id="{{task}}-addon" value="SAVE">

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

	<!--<script src="/tabs/add-ons/handlebars.js"></script>-->
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-tour-standalone.min.css">
    <script type="text/javascript" src="/js/bootstrap-tour-standalone.min.js"></script>    
	<script type="text/javascript" src="/js/tour.js"></script>
	<script src="/tabs/add-ons/js/script.js"></script>
</div>
