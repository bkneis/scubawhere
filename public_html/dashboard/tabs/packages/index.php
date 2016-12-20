<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="packages-list-div">
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
			<div class="panel panel-default" id="package-form-container">
				<script type="text/x-handlebars-template" id="package-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} Package</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-package-form">
							<div class="form-row">
								{{#if update}}
									<span class="btn btn-danger pull-right remove-package">Remove</span>
								{{/if}}
								<label class="field-label">Package Name : <span class="text-danger">*</span></label>
								<input id="package-name" type="text" name="name" value="{{{name}}}">
							</div>

							<div id="container-package-description" class="form-row">
								<label class="field-label">Package Description</label>
								<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							<div id="package-entities" class="form-row entity-lists">

								{{#if update}}
									<strong>Included tickets:</strong>
									{{#each tickets}}
										<p>
											<big class="margin-right">{{{name}}}</big> Quantity: <big class="margin-right">{{pivot.quantity}}</big>
										</p>
									{{else}} <p>-</p>
									{{/each}}

									<strong>Included courses:</strong>
									{{#each courses}}
										<p>
											<big class="margin-right">{{{name}}}</big> Quantity: <big class="margin-right">{{pivot.quantity}}</big>
										</p>
									{{else}} <p>-</p>
									{{/each}}

									<strong>Included accommodations:</strong>
									{{#each accommodations}}
										<p>
											<big class="margin-right">{{{name}}}</big> Quantity: <big class="margin-right">{{pivot.quantity}}</big>
										</p>
									{{else}} <p>-</p>
									{{/each}}

									<strong>Included addons:</strong>
									{{#each addons}}
										<p>
											<big class="margin-right">{{{name}}}</big> Quantity: <big class="margin-right">{{pivot.quantity}}</big>
										</p>
									{{else}} <p>-</p>
									{{/each}}
								{{else}}
									{{#each availables}}
										<strong>Select {{@key}}s to be included in this package:</strong>
										{{> entity_select}}
									{{/each}}
								{{/if}}
							</div>

							<div id="package-base" class="form-row">
								<p><strong>Package price : <span class="text-danger">*</span></strong></p>
								{{#each base_prices}}
									{{> price_input}}
								{{/each}}
								<button class="btn btn-default btn-sm add-base-price"> &plus; Click here to add a price change for the future</button>
							</div>

							<div id="package-seasonal" class="form-row">
								<label>
									<input id="package-season-price" type="checkbox" onchange="showMe('#seasonal-prices-list', this);"{{#if prices}} checked{{/if}}>
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
									<button class="btn btn-default btn-sm add-price"> &plus; Add another seasonal price</button>
								</div>
							</div>

							<div class="form-row" id="package-availability">
								<label style="display: block;">
									<input id="package-availability-checkbox" type="checkbox" onclick="showMe('#availability-select', this)"{{#if hasAvailability}} checked{{/if}}><strong> Limit the package's availability?</strong>
								</label>
								<div class="dashed-border" id="availability-select"{{#unless hasAvailability}} style="display:none;"{{/unless}}>
									<p>This package should only be available for selection between:</p>
									From: <input type="text" class="datepicker" data-date-format="YYYY-MM-DD" name="available_from" value="{{available_from}}"> &nbsp; &nbsp; Until: <input type="text" class="datepicker" data-date-format="YYYY-MM-DD" name="available_until" value="{{available_until}}">

									<p>&nbsp;</p>

									<p>This package should only be available for trips that start between:</p>
									From: <input type="text" class="datepicker" data-date-format="YYYY-MM-DD" name="available_for_from" value="{{available_for_from}}"> &nbsp; &nbsp; Until: <input type="text" class="datepicker" data-date-format="YYYY-MM-DD" name="available_for_until" value="{{available_for_until}}">
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

	<script type="text/x-handlebars-template" id="entity-select-template">
		<p>
			<select data-model="{{#if model}}{{model}}{{else}}{{@key}}{{/if}}" class="entity-select">
				<option value="0">Select {{#if model}}{{model}}{{else}}{{@key}}{{/if}}</option>
				{{#each availables}}
					<option value="{{id}}">{{{name}}}</option>
				{{/each}}
			</select>

			Quantity: &nbsp;<input type="number" class="quantity-input" disabled value="" min="1" step="1" style="width: 50px;">
		</p>
	</script>

	<script type="text/x-handlebars-template" id="price-input-template">
		<p{{#unless decimal_price}} class="new_price"{{/unless}}>
            <span class="currency">{{currency}}</span>
            <input type="number" id="acom-price" name="prices[{{id}}][new_decimal_price]" placeholder="00.00" min="0" step="0.01" value="{{decimal_price}}" style="width: 100px;">

            {{#unless isAlways}}
                from <input type="text" name="prices[{{id}}][from]" class="datepicker" data-date-format="YYYY-MM-DD" value="{{from}}" style="width: 125px;">
            {{else}}
                from <strong>the beginning of time</strong>
                <input type="hidden" name="prices[{{id}}][from]" value="0000-00-00">
            {{/unless}}

            {{#unless isBase}}
                until <input type="text" name="prices[{{id}}][until]" class="datepicker" data-date-format="YYYY-MM-DD" value="{{until}}" style="width: 125px;">
            {{/unless}}

            <button class="btn btn-danger remove-price">&#215;</button>
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
	<script src="/tabs/packages/js/script.js"></script>
</div>
