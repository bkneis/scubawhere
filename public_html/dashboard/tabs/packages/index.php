<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Available packages</label>
			<div class="padder" id="package-list-container">
				<!--<div class="yellow-helper">
					Select an package to change its details.
				</div>-->
				<button id="change-to-add-package" style="padding: 0.5em 1em; margin: 0.4em;" class="bttn greenb">&plus; Add Package</button>
				<script type="text/x-handlebars-template" id="package-list-template">
					<ul id="package-list" class="entity-list">
						{{#each packages}}
							<li data-id="{{id}}"{{#if trashed}} class="trashed"{{/if}}><strong>{{{name}}}</strong> | {{count tickets}} tickets | {{decimal_price}}</li>
						{{else}}
							<p>No packages available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>

		<div class="box70" id="package-form-container">

			<script type="text/x-handlebars-template" id="package-form-template">
				<label class="dgreyb">{{task}} package</label>
				<div class="padder">
					<form id="{{task}}-package-form">
						<div class="form-row">
							<label class="field-label">Package Name</label>
							<input type="text" name="name" value="{{{name}}}">
							{{#if trashed}}
								<strong style="color: #FF7163;">(Deactivated)</strong>
							{{/if}}

							{{#if update}}
								{{#if trashed}}
									<span class="box-tool blueb restore-package" style="color: white;">Restore</span>
								{{else}}
									{{#if has_bookings}}
										<span class="questionmark-tooltip" style="float: right;" title="This package has been booked at least once. That is why it can only be deactivated and not removed.">?</span>
										<span class="box-tool redb deactivate-package" style="color: white;">Deactivate</span>
									{{else}}
										<span class="box-tool redb remove-package" style="color: white;">Remove</span>
									{{/if}}
								{{/if}}
							{{/if}}
						</div>

						<div class="form-row">
							<label class="field-label">Package Description (Optional)</label>
							<textarea name="description" rows="3" cols="10">{{{description}}}</textarea>
						</div>

						<div class="form-row ticket-list">
							<strong>Select tickets to be included in this package:</strong>
							{{#each tickets}}
								{{> ticket_select}}
							{{/each}}
							{{> ticket_select}}
						</div>

						<div class="form-row">
							<label class="field-label">Package Price</label>
							<select name="currency">
								<option>GBP</option>
							</select>
							<input type="number" name="price" value="{{decimal_price}}" placeholder="00.00" style="width: 100px;" min="0">
						</div>

						<div class="form-row">
							<label class="field-label">Max. group size per boat</label>
							<input type="number" name="capacity" value="{{capacity}}" style="width: 55px;" min="0" step="1" placeholder="none">
						</div>

						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<button class="bttn blueb big-bttn" id="{{task}}-package">{{task}} Package</button>

					</form>
				</div>
			</script>

		</div>

		<script type="text/x-handlebars-template" id="ticket-select-template">
			<p>
				<select class="ticket-select">
					<option value="0">Select a ticket</option>
					{{#each available_tickets}}
						<option value="{{id}}"
							{{#if ../existing}}
								{{selected ../id}}
							{{/if}}
						>{{{name}}}</option>
					{{/each}}
				</select>
				Quantity: &nbsp;<input type="number" class="quantity-input"{{#if pivot.quantity}} name="tickets[{{id}}][quantity]"{{else}} disabled{{/if}} value="{{pivot.quantity}}" min="1" step="1" style="width: 50px;">
				<span class="ticket-price" data-default="-">{{#if pivot.quantity}}{{currency}} {{multiply pivot.quantity decimal_price}}{{else}}-{{/if}}</span>
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
	</div>
</div>

<script src="/dashboard/js/Controllers/Ticket.js"></script>
<script src="/dashboard/js/Controllers/Package.js"></script>
<script src="tabs/packages/js/script.js"></script>
