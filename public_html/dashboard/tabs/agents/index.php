<div id="wrapper" class="clearfix">
<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Next are agents, these are used as a source of booking. By adding all your agents details, you can easily manage an agent's commission by selecting which agent got you the booking. (If you do not have any agents that work for you, skip this part and click next step)"></div>
	<div class="col-md-4">
		<div class="panel panel-default" id="agent-list-div" data-step="6" data-position="right" data-intro="Once an agent is created it will appear on your list. Click on a agent if you want to view / edit any information">
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
		<div class="panel panel-default" id="agent-form-container" data-setp="2" data-position="left" data-intro="Enter all of the details for the agent in this form, these will be used when invoicing them.">
			<script type="text/x-handlebars-template" id="agent-form-template">
				<div class="panel-heading">
					<h4 class="panel-title">{{task}} agent</h4>
				</div>
				<div class="panel-body">
					<form id="{{task}}-agent-form">
						<div class="form-row">
							<label class="field-label">Agent Name</label>
							<input id="agent-name" type="text" name="name" value="{{{name}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Agent&rsquo;s Website (Optional)</label>
							<input id="agent-web" type="text" name="website" placeholder="http://" value="{{{website}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Branch Name</label>
							<input id="branch-name" type="text" name="branch_name" value="{{{branch_name}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Branch Address</label>
							<textarea id="branch-address" name="branch_address" rows="3" cols="10">{{{branch_address}}}</textarea>
						</div>

						<div class="form-row">
							<label class="field-label">Branch Telephone</label>
							<input id="branch-phone" type="text" name="branch_phone" placeholder="+00 000 ..." value="{{{branch_phone}}}">
						</div>

						<div class="form-row">
							<label class="field-label">Branch E-Mail</label>
							<input id="branch-email" type="text" name="branch_email" placeholder="email@agent.com" value="{{{branch_email}}}">
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

						<div class="form-row" id="commission-div" data-step="3" data-position="left" data-intro="Enter the amount of commission the agent recieves for each booking">
							<label class="field-label">Commission</label>
							<input id="commission-amount" type="text" name="commission" size="4" placeholder="00.00" value="{{commission}}"> %
						</div>

						<div class="form-row" data-step="4" data-position="top" data-intro="Additionally you can set the business terms of your arrangement with the agent. Terms can be either commission only, deposit only, full amount or banned. Once selected click save to create the agent">
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
