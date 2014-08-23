<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Available addons</label>
			<div class="padder" id="agent-list-container">
				<!--<div class="yellow-helper">
					Select an agent to change its details.
				</div>-->
				<button id="change-to-add-agent" style="padding: 0.5em 1em; margin: 0.4em;" class="bttn greenb">&plus; Add Addon</button>
				<script type="text/x-handlebars-template" id="agent-list-template">
					<ul id="agent-list">

					</ul>
				</script>
			</div>
		</div>
		<div class="box70" id="agent-form-container">

			<script type="text/x-handlebars-template" id="agent-form-template">
				<label class="dgreyb">{{task}} addon</label>
				<div class="padder">
					<form id="{{task}}-agent-form">
						<div class="form-row">
							<label class="field-label">Addon Name</label>
							<input type="text" name="name" value="{{{name}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Addon Description</label>
							<textarea name="addon_address" rows="3" cols="10">{{{addon_address}}}</textarea>
						</div>

						<div class="form-row">
							<label class="field-label">Price</label>
							<input type="text" name="addon_phone" placeholder="£0.00" value="{{{addon_phone}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Compulsory</label>
							<input type="checkbox" name="addon_compulsory" placeholder="" value="{{{addon_email}}}">
						</div>


						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<button class="bttn blueb big-bttn" id="{{task}}-agent">{{task}} Addon</button>

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
