<div id="wrapper" class="clearfix">

	<div class="row" id="campaign-container">

		<!--<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">My Groups</h4>
				</div>
				<div id="customer-group-container" class="panel-body">
					<button id="add-customer-group" class="btn btn-success text-uppercase">&plus; New Group</button>
					<ul id="customer-group-list" class="entity-list"></ul>
				</div>
			</div>

		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="campaign-form-container"></div>
		</div>-->

	</div>

	<div class="modal fade" id="select-email-template-modal">
		<div class="modal-dialog" style="width:80%">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Choose Template</h4>
			</div>
			<div class="modal-body">
				<!-- loop through templates-->
				<div style="margin: 0 auto;">
				<div class="modal-sidebar" style="width:35%; float:left">
					<p>Little templates will go here</p>
					<img width="45%" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_2column_query.html" src="/img/email-template-temp.jpg">
					<img width="45%" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_3column_query.html" src="/img/email-template-temp.jpg">
					<img width="45%" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_basic_body_image_query.html" src="/img/email-template-temp.jpg">
					<img width="45%" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_basic_query.html" src="/img/email-template-temp.jpg">
					<img width="45%" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_body_image_2column_query.html" src="/img/email-template-temp.jpg">
				</div>
				<div style="width:65%; float:left">
					<h4>Preview:</h4>
					<iframe id="email-template-option-preview" width="100%"height="500px" style="border:none"></iframe> <!-- dynamically change src for each template -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="select-email-template" type="submit" class="btn btn-primary">Use Template</button>
			</div>
		</div>
	</div>
</div>

<script type="text/x-handlebars-template" id="view-campaigns-groups-template">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">My Groups</h4>
			</div>
			<div id="customer-group-container" class="panel-body">
				<button id="add-customer-group" class="btn btn-success text-uppercase">&plus; New Group</button>
				<ul id="customer-group-list" class="entity-list"></ul>
			</div>
		</div>

	</div>

	<div class="col-md-8">
		<div class="panel panel-default" id="campaign-form-container"></div>
	</div>
</script>

<div id="modalWindows" style="height: 0;">
</div>

<script type="text/x-handlebars-template" id="group-list-template">
	{{#each groups}}
	<li data-id="{{id}}"><strong>{{name}}</strong></li>
	{{/each}}
</script>

<script type="text/x-handlebars-template" id="customer-group-form-template">
	<div class="panel-heading">
		<h4 class="panel-title">{{task}} group</h4>
	</div>
	<div class="panel-body">
		<form id="{{task}}-group-form" accept-charset="utf-8">
			<div class="form-row">
				<label class="field-label">Group name</label>
				<input id="group-name" type="text" name="name" value="{{{name}}}">

				{{#if update}}
				<span class="btn btn-danger pull-right remove-customer-group">Remove</span>
				{{/if}}
			</div>
			<div class="form-row">
				<label class="field-label">Group description</label>
				<textarea id="group-description" name="description" style="height: 243px;">{{{description}}}</textarea>
			</div>
			<h5>How to group your customers</h5>
			<p>Select the common attributes you wish to group your customers, either by certification, agency, trip bought or class bought :</p>
			<div class="form-group" style="margin-bottom: 0;">
				<div class="col-md-12" id="selected-rules">
				</div>
			</div>
			<fieldset id="add-certificates">
				<div class="form-group">
					<div class="col-md-5">
						<label for="agency_id" class="control-label">Agency</label>
						<select id="agency_id" class="form-control select2">
						</select>
					</div>
					<div class="col-md-5">
						<label for="certificate_id" class="control-label">Certificate</label>
						<select id="certificate_id" class="form-control select2">
						</select>
					</div>
					<div class="col-md-2">
						<label>&nbsp;</label><br>
						<button class="btn btn-success add-certificate" style="width: 100%;">Add</button>
					</div>
				</div>
			</fieldset>

			<fieldset id="add-tickets">
				<div class="form-group" style="margin-bottom: 0;">
					<div class="col-md-12" id="selected-trips">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-10">
						<label for="ticket_id" class="control-label">Tickets</label>
						<select id="ticket_id" class="form-control select2">
						</select>
					</div>
					<div class="col-md-2">
						<label>&nbsp;</label><br>
						<button class="btn btn-success add-ticket" style="width: 100%;">Add</button>
					</div>
				</div>
			</fieldset>

			<fieldset id="add-classes">
				<div class="form-group" style="margin-bottom: 0;">
					<div class="col-md-12" id="selected-classes">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-10">
						<label for="class_id" class="control-label">Classes</label>
						<select id="class_id" class="form-control select2">
						</select>
					</div>
					<div class="col-md-2">
						<label>&nbsp;</label><br>
						<button class="btn btn-success add-class" style="width: 100%;">Add</button>
					</div>
				</div>
			</fieldset>

			<div style="padding-bottom:20px;"></div>

			{{#if update}}
			<input type="hidden" name="id" value="{{id}}">
			{{/if}}

			<input type="hidden" name="_token">
			<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" value="SAVE">
		</form>
	</div>
</script>

<script type="text/x-handlebars-template" id="campaigns-template">
	<div class="panel-heading">
		<h4 class="panel-title">My Campaigns</h4>
	</div>
	<div class="panel-body">
		<button id="create-campaign" class="btn btn-success text-uppercase pull-right">&plus; New Campaign</button>
		<div style="height:50px;"></div>
		<table class="bluethead">
			<thead>
				<tr class="bg-primary">
					<th>Date</th>
					<th style="width:50%">Subject</th>
					<th>Num sent to</th>
				</tr>
			</thead>
			<tbody>
				{{#each campaigns}}
				<tr>
					<td>{{sent_at}}</td>
					<td>{{subject}}</td>
					<td>{{num_sent}}</td>
				</tr>
				{{else}}
				<tr>
					<td>You have no campaigns yet</td>
				</tr>
				{{/each}}
			</tbody>
		</table>
	</div>
</script>

<script type="text/x-handlebars-template" id="create-campaign-template">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">{{task}} a campaign</h4>
			</div>
			<div class="panel-body">
				<form id="create-campaign-form" accept-charset="utf-8">
					<div class="form-row">
						<label class="field-label">Campaign name</label>
						<input id="campaign-name" type="text" name="name" style="width:100%" value="{{{name}}}">

					</div>
					<fieldset id="add-customer-group-to-campaign">
						{{#if update}}
						<div class="form-group" style="margin-bottom: 0;">
							<label>Groups Sent To</label><br>
							<div class="col-md-12" id="selected-customer-groups">
							</div>
						</div>
						{{else}}
						<div class="form-group">
							<div class="col-md-10">
								<label for="customer_group_id" class="control-label">Which groups are you sending this to?</label>
								<select id="customer_group_id" class="form-control select2">
								</select>
							</div>
							<div class="col-md-2">
								<label>&nbsp;</label><br>
								<button class="btn btn-success add-group" style="width: 100%;">Add</button>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12" style="padding-top:10px" id="selected-customer-groups">
							</div>
						</div>
						{{/if}}
					</fieldset>
					<div class="form-row">
						<label class="field-label">Email Subject</label>
						<input id="email_subject" type="text" name="subject" style="width:100%" value="{{{subject}}}">
					</div>

					<button id="show-email-browser" style="display:none" class="btn btn-success pull-right">View in browser</button>
					<button id="select-campaign-template" class="btn btn-primary">Choose Email Template</button>

					<div style="padding-bottom:10px"></div>

					<iframe id="email-template-editor" width="100%" height="400px" style="border:none; display:none"></iframe>

					<div style="padding-bottom:20px"></div>

					{{#if update}}
					<input type="hidden" name="id" value="{{id}}">
					{{/if}}

					<input type="hidden" name="_token">

					<div id="email-template"></div>

					<button class="btn btn-danger btn-lg return-campaigns text-uppercase">CANCEL</button>
					<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" value="SEND">
				</form>
			</div>
		</div>
	</div>
</script>

<script type="text/x-handlebars-template" id="agencies-template">
	<option value="">Choose agency...</option>
	{{#each agencies}}
	<option value="{{id}}">{{abbreviation}} - {{{name}}}</option>
	{{/each}}
</script>

<script type="text/x-handlebars-template" id="tickets-template">
	<option value="">Choose ticket...</option>
	{{#each tickets}}
	<option value="{{id}}">{{{name}}}</option>
	{{/each}}
</script>

<script type="text/x-handlebars-template" id="classes-template">
	<option value="">Choose class...</option>
	{{#each trainings}}
	<option value="{{id}}">{{{name}}}</option>
	{{/each}}
</script>

<script type="text/x-handlebars-template" id="certificates-template">
	<option value="all">All certificates</option>
	{{#each certificates}}
	<option value="{{id}}">{{{name}}}</option>
	{{/each}}
</script>

<script type="text/x-handlebars-template" id="selected-certificate-template">
	<div class="pull-left selected-certificate">
		<input type="checkbox" name="certificates[]" value="{{id}}" style="position: absolute; top: 0; left: -9999px;" checked="checked">
		<strong>{{abbreviation}}</strong> - {{{name}}}
		<i class="fa fa-times remove-certificate" style="cursor: pointer;"></i>
	</div>
</script>

<script type="text/x-handlebars-template" id="selected-agency-template">
	<div class="pull-left selected-agency">
		<input type="checkbox" name="agencies[]" value="{{id}}" style="position: absolute; top: 0; left: -9999px;" checked="checked">
		<strong>Agency</strong> - {{{abbreviation}}}
		<i class="fa fa-times remove-agency" style="cursor: pointer;"></i>
	</div>
</script>

<script type="text/x-handlebars-template" id="selected-ticket-template">
	<div class="pull-left selected-ticket">
		<input type="checkbox" name="tickets[]" value="{{id}}" style="position: absolute; top: 0; left: -9999px;" checked="checked">
		<strong>Ticket</strong> - {{{name}}}
		<i class="fa fa-times remove-ticket" style="cursor: pointer;"></i>
	</div>
</script>

<script type="text/x-handlebars-template" id="selected-class-template">
	<div class="pull-left selected-class">
		<input type="checkbox" name="classes[]" value="{{id}}" style="position: absolute; top: 0; left: -9999px;" checked="checked">
		<strong>Class</strong> - {{{name}}}
		<i class="fa fa-times remove-class" style="cursor: pointer;"></i>
	</div>
</script>

<script type="text/x-handlebars-template" id="selected-group-template">
	<div class="pull-left selected-certificate">
		<input type="checkbox" name="groups[]" value="{{id}}" style="position: absolute; top: 0; left: -9999px;" checked="checked">
		<strong>{{{name}}}</strong>
		<i class="fa fa-times remove-group" style="cursor: pointer;"></i>
	</div>
</script>

<script type="text/x-handlebars-template" id="group-select-template">
	<option value="">Choose group...</option>
	{{#each groups}}
	<option value="{{id}}">{{name}}</option>
	{{/each}}
</script>

<script src="/tabs/campaigns/js/script.js"></script>

</div>

