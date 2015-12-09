<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="group-list-div">
				<div class="panel-heading">
					<h4 class="panel-title">Available Mailing Lists</h4>
				</div>
				<div class="panel-body" id="customer-group-list">
                    <button id="add-automated-email-rule" class="btn btn-success text-uppercase">&plus; Add Automated Email</button>
						<ul id="enabled-rules-list" class="entity-list">
                            <script type="text/x-handlebars-template" id="automated-enabled-rules-list-template">
                                    {{#each rules}}
                                        <li data-id="{{id}}"><strong>{{{name}}}</strong></li>
                                    {{else}}
                                        <p id="no-groups">You have no rules for automated emails yet.</p>
                                    {{/each}}
                            </script>
                        </ul>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="group-form-container">
				<script type="text/x-handlebars-template" id="automated-email-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} automated email</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-automated-email-form" accept-charset="utf-8">
							<div class="form-row">
								<label class="field-label">Rule name</label>
								<input id="rule-name" type="text" name="name" value="{{{name}}}">
								{{#if update}}
									<span class="btn btn-danger pull-right remove-automated-email">Remove</span>
								{{/if}}
							</div>

							<div class="form-row">
								<label class="field-label">Rule description</label>
								<textarea id="rule-description" name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							<div class="form-row">
                                <label for="available-email-rules" class="control-label">Rule to use to activate an automated email</label>
                                <select id="available-email-rules" class="form-control select2">
                                    <option id="1">Customer's Birthday</option>
                                    <option id="2">Booking made for customers</option>
                                    <option id="3">Payment confirmation</option>
                                    <option id="4">Trip Completed</option>
                                </select>
							</div>
                            
                            <button id="choose-email-templates" class="btn btn-success btn-lg">Select email template</button>
                            
                            <input type="hidden" name="email_template_id">
                            
                            {{#if update}}
								<input type="hidden" name="id" value="{{id}}">
							{{/if}}

							<input type="hidden" name="_token">
							<input type="submit" style="margin-top:20px" class="btn btn-primary btn-lg text-uppercase pull-right" value="SAVE">
						</form>
					</div>
				</script>
			</div>
		</div>
	</div><!-- .row -->
    
    <div id="modalWindows" style="height: 0;"></div>
    
    <!-- Modals -->
    <div class="modal fade" id="select-email-template-modal">
        <div class="modal-dialog" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Choose Template</h4>
                </div>
                <div class="modal-body">
                    <div style="margin: 0 auto;">
                        <div id="template-options" class="email-options-list" style="width:35%; float:left; max-height:620px; overflow: auto;">
                            <ul id="layout-options-list" style="margin-right:10px" class="entity-list"></ul>
                        </div>
                        <div style="width:65%; float:left">
                            <h4>Preview:</h4>
                            <iframe id="email-template-option-preview" width="100%" height="600px" style="border:none"></iframe>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="select-email-template" type="submit" class="btn btn-primary">Select Template</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/x-handlebars-template" id="template-options-list-template">
        {{#each layout}}
            <li class="email-layout-option" data-html="{{html_string}}">{{name}}</li>
        {{/each}}
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

	<script src="/tabs/automated-emails/js/script.js"></script>
</div>
