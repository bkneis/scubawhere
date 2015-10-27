<div id="wrapper" class="clearfix">
	<div class="row" id="campaign-container">
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
					<div style="width:35%; float:left; max-height:620px; overflow: auto">
						<img width="45%" height="200px" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_2column_query.html" src="/img/email-templates/email_template_1.jpg">
						<img width="45%" height="200px" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_3column_query.html" src="/img/email-templates/email_template_2.jpg">
						<img width="45%" height="200px" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_basic_body_image_query.html" src="/img/email-templates/email_template_3.jpg">
						<img width="45%" height="200px" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_basic_query.html" src="/img/email-templates/email_template_4.jpg">
						<img width="45%" height="200px" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_body_image_2column_query.html" src="/img/email-templates/email_template_5.jpg">
					</div>
					<div style="width:65%; float:left">
						<h4>Preview:</h4>
						<iframe id="email-template-option-preview" width="100%"height="600px" style="border:none"></iframe>
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
		<div class="col-md-12">
			<div class="panel panel-default" id="campaign-form-container"></div>
		</div>
	</script>
<div id="modalWindows" style="height: 0;">
</div>
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
	<div class="col-md-12" id="send-email-campaign">
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
													<iframe id="email-template-editor" width="100%" height="700px" style="border:none; display:none"></iframe>
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