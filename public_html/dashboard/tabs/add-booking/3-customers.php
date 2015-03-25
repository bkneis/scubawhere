<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Customers <small>Select the customers this booking is for.</small></h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Existing Customer</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label for="existing-customers" class="control-label">Name</label>
					<select id="existing-customers" name="existing-customers" class="form-control select2">
						<option selected="selected" value="">Search for a customer...</option>
					</select>
					<script id="customers-list-template" type="text/x-handlebars-template">
						<option selected="selected" value="">Search for a customer...</option>
						{{#each customers}}
							<option value="{{id}}">{{{firstname}}} {{{lastname}}} - {{email}}</option>
						{{/each}}
					</script>
				</div>
				<div id="selected-customer">

				</div>
				<script id="selected-customer-template" type="text/x-handlebars-template">
					<h4>Customer Details</h4>
					<li href="#" class="list-group-item" data-id="{{id}}" data-lead="{{lead}}" data-country-id="{{country_id}}">
						<a href="javascript:void(0);" class="btn btn-primary btn-xs edit-customer pull-right" data-id="{{id}}">Edit</a>
						<h5 class="list-group-item-heading">{{{firstname}}} {{{lastname}}}</h5>
						<p class="list-group-item-text">
							<a href="mailto:{{email}}" class="customer-email">{{email}}</a><br>
							{{{address_1}}}<br>
							{{{city}}}, {{{county}}}, {{postcode}}<br>
							<abbr title="Phone">P:</abbr> <span class="customer-phone">{{phone}}</span>
						</p>
					</li>
				</script>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="col-xs-12">
						<a href="javascript:void(0);" class="btn btn-primary add-customer pull-right" style="margin-left:5px;">Add</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-8">
		<div class="panel panel-primary form-horizontal">
			<div class="panel-heading">
				<h3 class="panel-title">New Customer</h3>
			</div>
			<form id="new-customer">
				<fieldset>
					<div class="panel-body form-horizontal">
						<div class="form-group">
							<div class="col-md-6">
								<label for="firstname" class="control-label">First Name <span class="text-danger">**</span></label>
								<input id="customer-firstname" name="firstname" class="form-control">
							</div>
							<div class="col-md-6">
								<label for="lastname" class="control-label">Last Name <span class="text-danger">**</span></label>
								<input id="customer-lastname" name="lastname" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="email" class="control-label">Email <span class="text-danger">*</span></label>
								<input id="customer-email" name="email" class="form-control" placeholder="@">
							</div>
							<div class="col-sm-3">
								<label for="phone" class="control-label">Dialling Code <span class="text-danger">*</span></label>
								<input type="text" name="dialling_code" class="form-control" placeholder="+44">
							</div>
							<div class="col-sm-5">
								<label for="phone" class="control-label">Phone <span class="text-danger">*</span></label>
								<input type="text" name="phone" class="form-control" placeholder="02071234567">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="address_1" class="control-label">Address 1</label>
								<input type="text" name="address_1" class="form-control" placeholder="Address 1">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="address_2" class="control-label">Address 2</label>
								<input type="text" name="address_2" class="form-control" placeholder="Address 2">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<label for="city" class="control-label">City</label>
								<input type="text" name="city" class="form-control" placeholder="City">
							</div>
							<div class="col-md-4">
								<label for="county" class="control-label">County / State</label>
								<input type="text" name="county" class="form-control" placeholder="County">
							</div>
							<div class="col-md-4">
								<label for="postcode" class="control-label">Postcode</label>
								<input type="text" name="postcode" class="form-control" placeholder="Post Code">
							</div>
						</div>
						<fieldset id="add-customer-countries">
							<div class="form-group">
								<div class="col-md-8">
									<label for="country_id" class="control-label">Country <span class="text-danger">*</span></label>
									<select id="country_id" name="country_id" class="form-control select2"></select>
								</div>
								<script id="countries-template" type="text/x-handlebars-template">
									<option value="">Choose country...</option>
									{{#each countries}}
										<option value="{{id}}">{{{name}}}</option>
									{{/each}}
								</script>
							</div>
						</fieldset>
					</div>
					<div class="panel-footer">
						<div class="row">
							<div class="col-md-12">
								<p class="pull-left text-muted"><span class="text-danger">**</span> Required for all customers &nbsp; &nbsp; &nbsp;</p>
								<p class="pull-left text-muted"><span class="text-danger">*</span> Required for lead customer</p>
								<button type="submit" class="btn btn-primary new-customer pull-right" style="margin-left:5px;">Create</button>
								<a href="javascript:void(0);" class="btn btn-warning clear-form pull-right">Clear</a>
							</div>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="edit-customer-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Edit Customer</h4>
			</div>
			<form id="edit-customer-form" role="form">
				<div class="modal-body">
					<fieldset id="edit-customer-details"></fieldset>
					<fieldset id="edit-customer-countries">
						<div class="form-group">
							<label for="country_id">Country</label>
							<select id="country_id" name="country_id" class="form-control select2">
								<option value="">Choose Country...</option>
							</select>
							<script id="countries-template" type="text/x-handlebars-template">
								{{#each countries}}
									<option value="{{id}}">{{{name}}}</option>
								{{/each}}
							</script>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script id="edit-customer-template" type="text/x-handlebars-template">
	<input type="hidden" name="id" value="{{id}}">
	<div class="form-group">
		<label for="email" class="col-sm-4 control-label">Email</label>
		<div class="col-sm-8">
			<input type="email" name="email" class="form-control" placeholder="Email" value="{{email}}">
		</div>
	</div>
	<div class="form-group">
		<label for="firstname" class="col-sm-4 control-label">First Name</label>
		<div class="col-sm-8">
			<input type="text" name="firstname" class="form-control" placeholder="First Name" value="{{{firstname}}}">
		</div>
	</div>
	<div class="form-group">
		<label for="lastname" class="col-sm-4 control-label">Last Name</label>
		<div class="col-sm-8">
			<input type="text" name="lastname" class="form-control" placeholder="Last Name" value="{{{lastname}}}">
		</div>
	</div>
	<div class="form-group">
		<label for="address_1" class="col-sm-4 control-label">Address 1</label>
		<div class="col-sm-8">
			<input type="text" name="address_1" class="form-control" placeholder="Address 1" value="{{{address_1}}}">
		</div>
	</div>
	<div class="form-group">
		<label for="address_2" class="col-sm-4 control-label">Address 2</label>
		<div class="col-sm-8">
			<input type="text" name="address_2" class="form-control" placeholder="Address 2" value="{{{address_2}}}">
		</div>
	</div>
	<div class="form-group">
		<label for="city" class="col-sm-4 control-label">City</label>
		<div class="col-sm-8">
			<input type="text" name="city" class="form-control" placeholder="City" value="{{{city}}}">
		</div>
	</div>
	<div class="form-group">
		<label for="county" class="col-sm-4 control-label">County</label>
		<div class="col-sm-8">
			<input type="text" name="county" class="form-control" placeholder="County" value="{{{county}}}">
		</div>
	</div>
	<div class="form-group">
		<label for="postcode" class="col-sm-4 control-label">Postcode</label>
		<div class="col-sm-8">
			<input type="text" name="postcode" class="form-control" placeholder="Post Code" value="{{postcode}}">
		</div>
	</div>
	<div class="form-group">
		<label for="phone" class="col-sm-4 control-label">Phone</label>
		<div class="col-sm-8">
			<input type="text" name="phone" class="form-control" placeholder="Phone" value="{{phone}}">
		</div>
	</div>
</script>
