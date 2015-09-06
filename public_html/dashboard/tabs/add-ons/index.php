<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Now you need to enter your add-ons. An add-on can be attached to a booking and can be useful for things like extra dives, hotel pick ups and nitrox air etc."></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="addon-list-div" data-step="4" data-position="right" data-intro="Once a add-on is saved, you will see it in your list. Click on a add-on to view/edit the details.">
				<div class="panel-heading">
					<h4 class="panel-title">Available Add-ons</h4>
				</div>
				<div class="panel-body" id="addon-list-container">
					<button id="change-to-add-addon" class="btn btn-success text-uppercase">&plus; Add Add-on</button>
					<script type="text/x-handlebars-template" id="addon-list-template">
						<ul id="addon-list" class="entity-list">
							{{#each addons}}
								<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{currency}} {{decimal_price}}</li>
							{{else}}
								<p id="no-addons">No addons available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="addon-form-container" data-step="2" data-position="left" data-intro="Enter a name, description and price for the add-on">
				<script type="text/x-handlebars-template" id="addon-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} add-on</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-addon-form">
							<div class="form-row">
								<label class="field-label">Add-on Name</label>
								<input id="addon-name" type="text" name="name" value="{{{name}}}">

								{{#if update}}
									<span class="btn btn-danger pull-right remove-addon">Remove</span>
								{{/if}}
							</div>

							<div class="form-row">
								<label class="field-label">Add-on Description</label>
								<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							<div class="form-row">
								<label class="field-label">Add-on Price</label>
								<span class="currency">{{currency}}</span>
								<input id="addon-price" type="number" name="new_decimal_price" placeholder="0.00" min="0" step="0.01" value="{{decimal_price}}" style="width: 100px;">
							</div>

							<div class="form-row" id="addon-compulsory-div" data-step="3" data-position="left" data-intro="Additionally, you can set an addon to be compulsory for all bookings. For example, governmental dive taxes.">
								<label class="field-label">Compulsory?</label>
								<input id="addon-compulsory" type="checkbox" name="compulsory" value="1" {{#if compulsory}}checked{{/if}}> Automatically add this add-on to every trip during booking.
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
	</div><!-- .row -->

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

	<script src="/tabs/add-ons/js/script.js"></script>
</div>
