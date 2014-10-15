<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Available addons</label>
			<div class="padder" id="addon-list-container">
				<div class="yellow-helper">
					Select an addon to change its details.
				</div>
				<button id="change-to-add-addon" style="padding: 0.5em 1em; margin: 0.4em;" class="bttn greenb">&plus; Add Addon</button>
				<script type="text/x-handlebars-template" id="addon-list-template">
					<ul id="addon-list" class="entity-list">
						{{#each addons}}
							<li data-id="{{id}}"{{#if trashed}} class="trashed"{{/if}}><strong>{{{name}}}</strong> | {{currency}} {{decimal_price}}</li>
						{{else}}
							<p>No addons available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>

		<div class="box70" id="addon-form-container">

			<script type="text/x-handlebars-template" id="addon-form-template">
				<label class="dgreyb">{{task}} addon</label>
				<div class="padder">
					<form id="{{task}}-addon-form">
						<div class="form-row">
							<label class="field-label">Addon Name</label>
							<input type="text" name="name" value="{{{name}}}">
							{{#if trashed}}
								<strong style="color: #FF7163;">(Deactivated)</strong>
							{{/if}}

							{{#if update}}
								{{#if trashed}}
									<span class="box-tool blueb restore-addon" style="color: white;">Restore</span>
								{{else}}
									{{#if has_bookings}}
										<span class="questionmark-tooltip" style="float: right;" title="This addon has been booked at least once. That is why it can only be deactivated and not removed.">?</span>
										<span class="box-tool redb deactivate-addon" style="color: white;">Deactivate</span>
									{{else}}
										<span class="box-tool redb remove-addon" style="color: white;">Remove</span>
									{{/if}}
								{{/if}}
							{{/if}}
						</div>

						<div class="form-row">
							<label class="field-label">Addon Description</label>
							<textarea name="description" rows="3" cols="10">{{{description}}}</textarea>
						</div>

						<div class="form-row">
							<label class="field-label">Addon Price</label>
							<select name="currency">
								<option value = "GBP">GBP</option>
							</select>
							<input type="text" name="price" placeholder="0.00" value="{{decimal_price}}">
						</div>

						<div class="form-row">
							<label class="field-label">Compulsory?</label>
							<input type="checkbox" name="compulsory" value="1" {{#if compulsory}}checked{{/if}}>
						</div>


						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<button class="bttn blueb big-bttn" id="{{task}}-addon">{{task}} Addon</button>

					</form>
				</div>
			</script>

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
	</div>
</div>

<script src="/dashboard/js/Controllers/Addons.js"></script>
<script src="tabs/add-ons/js/script.js"></script>
