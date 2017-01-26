<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="group-list-div">
				<div class="panel-heading">
					<h4 class="panel-title">Available Mailing Lists</h4>
				</div>
				<div class="panel-body" id="customer-group-list">
					<script type="text/x-handlebars-template" id="group-list-template">
                        <button id="add-customer-group" class="btn btn-success text-uppercase">&plus; Add Mailing List</button>
						<ul id="group-list" class="entity-list">
							{{#each groups}}
								<li data-id="{{id}}"><strong>{{{name}}}</strong></li>
							{{else}}
								<p id="no-groups">You have no mailing lists yet.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="group-form-container">
				<script type="text/x-handlebars-template" id="customer-group-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} mailing list</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-group-form" accept-charset="utf-8">
							<div class="form-row">
								{{#if update}}
									<span class="btn btn-danger pull-right remove-customer-group">Remove</span>
								{{/if}}
								<label class="field-label">Group name : <span class="text-danger">*</span></label>
								<input id="group-name" type="text" name="name" value="{{{name}}}">
							</div>

							<div class="form-row">
								<label class="field-label">Group description</label>
								<textarea id="group-description" name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							<div class="form-row">
								<h5>How to group your customers</h5>
								<p>Select the common attributes you wish to group your customers, either by certification, agency, trip bought or class bought :</p>
							</div>

							<div class="form-row" style="margin-bottom: 0;">
								<div class="col-md-12" id="selected-rules">
								</div>
							</div>

							<div class="form-row" id="add-certificates">
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

							<div class="form-row" id="add-tickets">
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

							<div class="form-row" id="add-classes">
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
                            
                            {{#if update}}
								<input type="hidden" name="id" value="{{id}}">
                                <button id="view-lists-customers" data-id="{{id}}" style="margin-top:20px; margin-left:15px;" class="btn btn-primary">View customers on this mailing list</button>
							{{/if}}

							<input type="hidden" name="_token">
							<input type="submit" style="margin-top:20px" class="btn btn-primary btn-lg text-uppercase pull-right" value="SAVE">
						</form>
					</div>
				</script>
			</div>
		</div>
	</div><!-- .row -->
    
    <div class="modal fade" id="view-group-customers-modal">
        <div class="modal-dialog" style="width:70%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Customers</h4>
                </div>
                <div class="modal-body">
                    <table id="group-customers-table" class="bluethead" style="width:100%">
                        <thead>
                            <tr class="bg-primary">
                                <th style="width:35%">Customer Name</th>
                                <th style="width:35%">Customer Email</th>
                                <th>Customer ID</th>
                                <th style="width:10%">Emails Opened</th>
                                <th style="width:10%">Emails Sent</th>
                                <th style="width:10%">Click Rate</th>
                            </tr>
                        </thead>
                        <tbody class="sw-blue-table">
                            
                        </tbody>
                    </table>
					<div style="margin-bottom: 40px"></div>
                </div>
            </div>
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

	<script src="/dashboard/tabs/mailing-lists/js/script.js"></script>
</div>
