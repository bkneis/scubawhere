<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Do you have any agents? If so, click 'next'. If not, then just 'skip' this step."></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="agent-list-div" data-step="6" data-position="right" data-intro="Once an agent is saved, you will see it in your list. Click on an agent to view/edit the details.">
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
								<p id="no-agents">No agents available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="agent-form-container" data-setp="2" data-position="left" data-intro="Create an agent profile, by entering their details and business relationship. These details will be used when generating an invoice at a later date.">
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

							<div class="form-row" id="commission-div" data-step="3" data-position="left" data-intro="Enter the percentage of commission the agent recieves for each booking.">
								<label class="field-label">Commission</label>
								<input id="commission-amount" type="text" name="commission" size="4" placeholder="00.00" value="{{commission}}"> %
							</div>

							<div class="form-row" data-step="4" data-position="top" data-intro="Define your relationship to the agent with one of the drop down options. 'Deposit only' means the agent will take the commission percentage directly, and the remaning balance will be paid directly to you. 'Full amount' means the agent gets paid the full amount for the reservation, then you will invoice the agent for payment. 'Banned' means that the agent is blocked and they are no longer allowed to make reservations. Lastly, click 'save' to add your agent.">
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

	<!--<script src="/tabs/agents/handlebars.runtime.js"></script>-->
	<script src="/tabs/agents/js/script.js"></script>
</div>
