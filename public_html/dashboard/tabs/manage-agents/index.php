<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Available agents</label>
			<div class="padder" id="agent-list-container">
				<!--<div class="yellow-helper">
					Select an agent to change its details.
				</div>-->
				<button id="change-to-add-agent" style="padding: 0.5em 1em; margin: 0.4em;" class="bttn greenb">&plus; Add Agent</button>
				<script type="text/x-handlebars-template" id="agent-list-template">
					<ul id="agent-list">
						{{#each agents}}
							<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{{branch_name}}}</li>
						{{else}}
							<p>No agents available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>

		<div class="box70" id="agent-form-container">

			<script type="text/x-handlebars-template" id="agent-form-template">
				<label class="dgreyb">{{task}} agent</label>
				<div class="padder">
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
						<div id="billing-div" style="{{#unless has_billing_details}}display: none; {{/unless}}margin: 2em; border: dashed lightgray; padding: 0.5em 2em 1em 2em">
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

						<div class="form-row">
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

						<button class="bttn blueb big-bttn" id="{{task}}-agent">{{task}} Agent</button>

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

<script src="/dashboard/js/Controllers/Agent.js"></script>
<script src="tabs/manage-agents/js/script.js"></script>
