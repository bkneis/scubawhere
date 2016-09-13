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
                <div id="template-types" class="btn-group" style="margin-bottom:10px" role="group">
					  <button display="layout-options" type="button" class="btn btn-default btn-primary option-button">Layouts</button>
					  <button display="template-options" type="button" class="btn btn-default option-button">My Templates</button>
				</div>
				<!-- loop through templates-->
				<div style="margin: 0 auto;">
                    <div id="template-options" class="email-options-list" style="width:35%; float:left; max-height:620px; overflow: auto; display:none;">
                        <ul id="layout-options-list" style="margin-right:10px" class="entity-list"></ul>
                    </div>
					<div id="layout-options" style="width:35%; float:left; max-height:620px; overflow: auto" class="email-options-list">
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
   
    <script type="text/x-handlebars-template" id="layout-options-list-template">
		{{#each layout}}
            <li class="email-layout-option" data-html="{{html_string}}">{{name}}</li>
        {{/each}}
	</script>
	<script type="text/x-handlebars-template" id="view-campaigns-groups-template">
		<div class="col-md-12">
			<div class="panel panel-default" id="campaign-form-container"></div>
		</div>
	</script>
<div id="modalWindows" style="height: 0;">
</div>
<script type="text/x-handlebars-template" id="campaigns-template">
    <div class="col-md-12">
        <div class="panel panel-default" id="campaign-form-container">
            <div class="panel-heading">
                        <h4 class="panel-title">My Campaigns</h4>
            </div>
            <div class="panel-body">
                    <a href="/email-editor" style="color:#FFF" class="btn btn-success text-uppercase pull-right">&plus; New Campaign</a>
                    <!--<button id="create-campaign" class="btn btn-success text-uppercase pull-right">&plus; New Campaign</button>-->
                    <div style="height:50px;"></div>
                    <table id="email-campaigns-table" class="bluethead">
                        <thead>
                            <tr class="bg-primary">
                                <th style="width:10%">Date</th>
                                <th style="width:30%">Name</th>
                                <th style="width:30%">Subject</th>
                                <th style="width:20%">Groups sent to</th>
                                <th style="width:10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="sw-blue-table">
                            {{#each campaigns}}
                            {{#if is_campaign}}
                                <tr>
                                    <td>{{sent_at}}</td>
                                    <td>{{name}}</td>
                                    <td>{{subject}}</td>
                                    <td>
                                        {{listGroups groups}}
                                    </td>
                                    <td> 
                                        <i class="fa fa-line-chart fa-fw view-email-analytics" data-toggle="tooltip" data-placement="top" data-campaign-id="{{id}}" title="View analytics"></i> 
                                        <i class="fa fa-eye fa-fw view-email-campaign" data-toggle="tooltip" data-placement="top" title="View email" data-html="{{email_html}}"></i>
                                        <i class="fa fa-envelope fa-fw resend-email-campaign" data-toggle="tooltip" data-placement="top" title="Re send campaign" data-campaign-id="{{id}}"></i>
                                        <i class="fa fa-times fa-fw delete-email-campaign" data-toggle="tooltip" data-placement="top" title="Delete campaign" data-campaign-id="{{id}}"></i>
                                    </td>
                                </tr>
                            {{/if}}
                            {{else}}
                            <tr>
                                <td colspan="5">You have no campaigns yet</td>
                            </tr>
                            {{/each}}
                        </tbody>
                    </table>
            </div>
        </div>
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
                                    <div class="col-md-12" id="selected-customer-groups"></div>
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
                            <div id="email-editor-tips" class="alert alert-warning">
                                <h4>Some tips about the email editor</h4>
                                <p>Double click on an image to upload your own</p>
                                <p>Click on any of the paragraphs to change the text, add a link or change the formmating of the text</p>
                                <p>Write anywhere &#123;&#123;name&#125&#125, &#123;&#123;last_dive&#125&#125, &#123;&#123;number_of_dives&#125&#125 or &#123;&#123;birthday&#125&#125 to automatically insert information about your customer</p>
                            </div>
                            <iframe id="email-template-editor" width="100%" height="700px" style="border:none; display:none"></iframe>
                            <div style="padding-bottom:20px"></div>
                            {{#if update}}
                            <input type="hidden" name="id" value="{{id}}">
                            {{/if}}
                            <input type="hidden" name="_token">
                            <div id="email-template"></div>
                            <button class="btn btn-danger btn-lg return-campaigns text-uppercase">CANCEL</button>
                            <input type="submit" class="btn btn-success btn-lg text-uppercase pull-right" value="SEND">
                            <button id="save-as-template" style="margin-right:10px" class="btn btn-primary btn-lg text-uppercase pull-right" disabled>SAVE AS TEMPLATE</button>
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
    <div class="modal fade" id="save-email-template-modal">
        <div class="modal-dialog" style="width:40%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Save Template</h4>
                </div>
                <div style="height:240px" class="modal-body">
                    <label for="template-name" class="control-label">Template name :</label>
                    <input type="text" id="template-name" class="form-control">
                    <div style="margin-top:10px; margin-bottom:10px;" class="alert alert-warning">
                        <p><strong>Where to find your templates</strong></p>
                        <p>Once saved, you can access and edit this template by clicking select email template, then go to 'My templates'</p>
                    </div>
                    <button id="btn-save-template" style="margin-top:10px" class="btn btn-primary btn-lg text-uppercase pull-right">SAVE</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="resend-email-modal">
        <div class="modal-dialog" style="width:40%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Resend Email</h4>
                </div>
                <div style="height:200px" class="modal-body">
                    <label for="campaign-name" class="control-label">Campaign name :</label>
                    <input type="text" id="resend-campaign-name" class="form-control">
                    <label for="email-subject" class="control-label">Email subject :</label>
                    <input type="text" id="resend-email-subject" class="form-control">
                    <button id="btn-resend-campaign" style="margin-top:10px" class="btn btn-primary btn-lg text-uppercase pull-right">SEND</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="show-email-analytics-modal">
        <div class="modal-dialog" style="width:60%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Email Analytics</h4>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom:10px" class="btn-group btn-group-justified">
                        <a role="button" class="btn btn-default">
                            <p><i class="fa fa-envelope fa-3x"></i></p>
                            <p id="total-emails-sent" class="text-center">Total Sent</p>
                        </a>
                        <a role="button" class="btn btn-default">
                            <p><i class="fa fa-eye fa-3x"></i></p>
                            <p id="total-emails-seen" class="text-center">Total Viewed</p>
                        </a>
                        <a role="button" class="btn btn-default">
                            <p><i class="fa fa-pie-chart fa-3x"></i></p>
                            <p id="average-open-rate" class="text-center">Avg Opened Rate</p>
                        </a>
                        <a role="button" class="btn btn-default">
                            <p><i class="fa fa-mouse-pointer fa-3x"></i></p>
                            <p id="average-click-rate" class="text-center">Avg Click Rate</p>
                        </a>
                        <a role="button" class="btn btn-default">
                            <p><i class="fa fa-trash fa-3x"></i></p>
                            <p id="num-unsubscribed" class="text-center">No. unsubscribed</p>
                        </a>
                    </div>
                    <table id="campaign-analytics-table" class="bluethead">
                        <thead>
                            <tr class="bg-primary">
                                <th style="width:50%">Customer Name</th>
                                <th style="width:50%">Customer Email</th>
                                <th style="width:10%">Opened Count</th>
                                <th>Customer ID</th>
                                <th style="width:10%">Unsubscriptions</th>
                                <th style="width:40%">Last Opened</th>
                            </tr>
                        </thead>
                        <tbody class="sw-blue-table">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
