<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="agent-list-div">
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
			<div class="panel panel-default" id="agent-form-container">
				<script type="text/x-handlebars-template" id="agent-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} agent</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-agent-form">
                            {{#if update}}
                                <span class="btn btn-danger pull-right remove-agent">Remove</span>
                            {{/if}}
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

							<div class="form-row" id="commission-div">
								<label class="field-label">Commission (%)</label>
								<input id="commission-amount" type="text" name="commission" size="4" placeholder="00.00" value="{{commission}}">
							</div>

							<div id="terms-div" class="form-row">
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
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-tour-standalone.min.css">
    <script type="text/javascript" src="/js/bootstrap-tour-standalone.min.js"></script>    
	<script type="text/javascript" src="/js/tour.js"></script>
	<script src="/tabs/agents/js/script.js"></script>
</div>
