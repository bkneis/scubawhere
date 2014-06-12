<div id="wrapper">
	<style type="text/css">
		#agent-list {
			list-style-type: none;
			padding-left: 0;
		}
		#agent-list li {
			cursor: pointer;
			margin-bottom: 0.5em;
			border: 1px solid #4a9cff;
			border-left-width: 5px;
			padding: 5px 10px;
		}
		#agent-list li:hover,
		#agent-list li.active {
			background-color: rgb(230, 245, 255);
		}
	</style>
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Available packages</label>
			<div class="padder" id="agent-list-container">
				<!--<div class="yellow-helper">
					Select an agent to change its details.
				</div>-->
				<button id="change-to-add-agent" style="padding: 0.5em 1em; margin: 0.4em;" class="bttn greenb">&plus; Add Package</button>
				<script type="text/x-handlebars-template" id="agent-list-template">
					<ul id="agent-list">
						{{#each packages}}
							<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{{count tickets}}} tickets</li>
						{{else}}
							<p>No packages available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>

		<div class="box70" id="agent-form-container">

			<script type="text/x-handlebars-template" id="agent-form-template">
				<label class="dgreyb">{{task}} package</label>
				<div class="padder">
					<form id="{{task}}-agent-form">
						<div class="form-row">
							<label class="field-label">Package Name</label>
							<input type="text" name="name" value="{{{name}}}">
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
							<input type="number" name="price" value="{{{decimal_price}}}" placeholder="00.00" style="width: 100px;" min="0">
						</div>

						<div class="form-row">
							<label class="field-label">Max. group size per boat</label>
							<input type="number" name="capacity" value="{{{capacity}}}" style="width: 55px;" min="0" step="1" placeholder="none">
						</div>

						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<button class="bttn blueb big-bttn" id="{{task}}-agent">{{task}} Package</button>

					</form>
				</div>
			</script>

		</div>

		<script type="text/x-handlebars-template" id="ticket-select-template">
			<p>
				<select class="ticket-select">
					<option value="0">Select a ticket</option>
					{{#each available_tickets}}
						<option value="{{id}}"{{selected ../id}}>{{{name}}}</option>
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
