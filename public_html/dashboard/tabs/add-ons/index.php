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
					<ul id="addon-list">
						{{#each addons}}
							<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{{price}}}</li>
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
							<input type="text" name="price" placeholder="0.00" value="{{{price}}}">
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
