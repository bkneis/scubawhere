<div id="wrapper" class="clearfix">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Available Add-ons</h4>
			</div>
			<div class="panel-body" id="addon-list-container">
				<button id="change-to-add-addon" class="btn btn-success text-uppercase">&plus; Add Add-on</button>
				<script type="text/x-handlebars-template" id="addon-list-template">
					<ul id="addon-list" class="entity-list">
						{{#each addons}}
							<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{currency.symbol}} {{decimal_price}}</li>
						{{else}}
							<p>No addons available.</p>
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
							<label class="field-label">Add-on Name</label>
							<input type="text" name="name" value="{{{name}}}">

							{{#if update}}
								<span class="btn btn-danger pull-right{{#if has_bookings}} deactivate-addon{{else}} remove-addon{{/if}}">Remove</span>
							{{/if}}
						</div>

						<div class="form-row">
							<label class="field-label">Add-on Description</label>
							<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
						</div>

						<div class="form-row">
							<label class="field-label">Add-on Price</label>
							<span class="currency">{{currency.symbol}}</span>
							<input type="number" name="new_decimal_price" placeholder="0.00" min="0" step="0.01" value="{{decimal_price}}" style="width: 100px;">
						</div>

						<div class="form-row">
							<label class="field-label">Compulsory?</label>
							<input type="checkbox" name="compulsory" value="1" {{#if compulsory}}checked{{/if}}>
						</div>


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

	<script src="/dashboard/js/Controllers/Addon.js"></script>
	<script src="tabs/add-ons/js/script.js"></script>
</div>
