<div id="wrapper" class="clearfix" data-step="1" data-position="right" data-intro="This tab is to manage you agents">
	<div class="col-md-4">
		<div class="panel panel-default" data-step="2" data-position="right" data-intro="Here shows your current agents. Click on one of them to edit it, or create a new one by clicking add agent.">
			<div class="panel-heading">
				<h4 class="panel-title">Available Agents</h4>
			</div>
			<div class="panel-body" id="agent-list-container">
				<button id="change-to-add-agent" class="btn btn-success text-uppercase">&plus; Add Agent</button>
				<script type="text/x-handlebars-template" id="agent-list-template">
					<ul id="agent-list" class="entity-list">
						{{#each agents}}
							<li data-id="{{id}}"{{isBanned}}><strong>{{{name}}}</strong> | {{{branch_name}}}</li>
						{{else}}
							<p>No agents available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>
	</div>

	<div class="col-md-8">
		<div class="panel panel-default" id="agent-form-container" data-step="3" data-position="left" data-intro="Set the commission rate in a % Set the business terms. If the Travel Agent usually takes the full amount from the customer on booking and you send them a bill later, select “Full Amount” in the Business Terms drop down. If the Travel Agent is only allowed to take their commission and the customer must pay the remaining amount directly to you, select “Deposit Only”. If the Travel Agent is naughty, hasn’t paid their bills or you simply want to stop receiving bookings from them. Select “Banned”.">
			<script type="text/x-handlebars-template" id="agent-form-template">
				<div class="panel-heading">
					<h4 class="panel-title">{{task}} agent</h4>
				</div>
				<div class="panel-body">
					<form id="{{task}}-agent-form">
						<div class="form-row">
							<label class="field-label">Agent Name</label>
							<input type="text" name="name" value="{{{name}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Agent&rsquo;s Website (Optional)</label>
							<input type="text" name="website" placeholder="http://" value="{{{website}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Branch Name</label>
							<input type="text" name="branch_name" value="{{{branch_name}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Branch Address</label>
							<textarea name="branch_address" rows="3" cols="10">{{{branch_address}}}</textarea>
						</div>

						<div class="form-row">
							<label class="field-label">Branch Telephone</label>
							<input type="text" name="branch_phone" placeholder="+00 000 ..." value="{{{branch_phone}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Branch E-Mail</label>
							<input type="text" name="branch_email" placeholder="email@agent.com" value="{{{branch_email}}}">
						</div>

						<div class="form-row">
							<label>
								<input type="checkbox" onchange="showMe('#billing-div', this);"{{#if has_billing_details}} checked{{/if}}>
								Enter different billing details?
							</label>
						</div>
						<div id="billing-div" class="dashed-border" style="{{#unless has_billing_details}}display: none; {{/unless}}">
							<h3>Billing Information</h3>
							<div class="form-row">
								<label class="field-label">Billing Address</label>
								<textarea name="billing_address" rows="3" cols="10"{{#unless has_billing_details}} disabled{{/unless}}>{{{billing_address}}}</textarea>
							</div>

							<div class="form-row">
								<label class="field-label">Billing Phone Number</label>
								<input type="text" name="billing_phone"{{#unless has_billing_details}} disabled{{/unless}} placeholder="+00 000 ..." value="{{{billing_phone}}}">
							</div>

							<div class="form-row">
								<label class="field-label">Billing E-Mail Address</label>
								<input type="text" name="billing_email"{{#unless has_billing_details}} disabled{{/unless}} placeholder="billing@agent.com" value="{{{billing_email}}}">
							</div>
						</div>

						<div class="form-row">
							<label class="field-label">Commission</label>
							<input type="text" name="commission" size="4" placeholder="00.00" value="{{commission}}"> %
						</div>

						<div class="form-row" data-step="3" data-position="top" data-intro="By selecting these business terms for each Travel Agent, it will feed into when Adding a Booking, with the final amount required would be effected and invoices to be automatically created on request.">
							<label class="field-label">Business Terms</label>
							<select id="terms" name="terms">
								<option>Please select..</option>
								<option value="fullamount"{{selected 'fullamount'}}>Full Amount</option>
								<option value="deposit"{{selected 'deposit'}}>Deposit Only</option>
								<option value="banned"{{selected 'banned'}}>Banned</option>
							</select>
						</div>

						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<button class="btn btn-primary btn-lg text-uppercase pull-right" id="{{task}}-agent">SAVE</button>
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

	<script src="/dashboard/js/Controllers/Agent.js"></script>
	<script src="tabs/agents/js/script.js"></script>
</div>
