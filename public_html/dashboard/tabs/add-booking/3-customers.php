<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Customers <small>Add existing customers or create new customers to add to booking.</small></h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Search existing Customers</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label for="existing-customers" class="control-label">Name</label>
					<select id="existing-customers" name="existing-customers" class="form-control select2">
						<option selected="selected" value="">Search for a customer...</option>
					</select>
					<script type="text/x-handlebars-template" id="customers-list-template">
						<option selected="selected" value="">Search for a customer...</option>
						{{#each customers}}
							<option value="{{id}}">{{{firstname}}} {{{lastname}}}{{#if email}} - {{email}}{{/if}}</option>
						{{/each}}
					</script>
				</div>

				<div id="selected-customer"></div>

				<script type="text/x-handlebars-template" id="selected-customer-template">
					<h4>Customer Details</h4>
					<li href="#" class="list-group-item" data-id="{{id}}" data-lead="{{lead}}" data-country-id="{{country_id}}">
						<a href="javascript:void(0);" class="btn btn-default btn-xs edit-customer pull-right" data-id="{{id}}">Edit</a>
						<h5 class="list-group-item-heading">{{{firstname}}} {{{lastname}}}</h5>
						<p class="list-group-item-text">
							<a href="mailto:{{email}}" class="customer-email">{{email}}</a><br>
							{{#if address_1}}{{{address_1}}}<br>{{/if}}
							{{{city}}}{{#if county}}, {{/if}}{{{county}}}{{#if postcode}}, {{/if}}{{#if postcode}}{{postcode}}{{/if}}
							{{#if phone}}<br><abbr title="Phone">P:</abbr> <span class="customer-phone">{{phone}}</span>{{/if}}
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
	<div class="col-md-12">
		<div class="panel panel-primary form-horizontal">
			<div class="panel-heading">
				<h3 class="panel-title">Create new Customer</h3>
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
								<input id="customer-email" name="email" class="form-control form-email" placeholder="@">
							</div>
							<div class="col-md-6" style="margin-top:30px;">
								<input type="checkbox" id="unsubscribe" name="unsubscribe" value="true">
								<label for="unsubscribe" class="control-label">Unsubscribe from email marketing? </label>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5">
								<label for="birthday" class="control-label">Date of birth</label>
								<input type="text" id="birthday" name="birthday" class="form-control datepicker" data-date-format="YYYY-MM-DD" data-date-view-mode="years">
							</div>
							<div class="col-md-2">
								<label for="phone" class="control-label">Dialling Code</label>
								<input type="text" name="dialling_code" class="form-control" placeholder="+44">
							</div>
							<div class="col-md-5">
								<label for="phone" class="control-label">Phone</label>
								<input type="text" name="phone" class="form-control" placeholder="02071234567">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="online_source">Where did they here about us?</label>
								<input type="text" name="online_source" class="form-control" placeholder="Google, magazine etc ...">
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
								<div class="col-md-6">
									<label for="country_id" class="control-label">Country <span class="text-danger">*</span></label>
									<select id="country_id" name="country_id" class="form-control select2"></select>
								</div>
								<script type="text/x-handlebars-template" id="countries-template">
									<option value="">Choose country...</option>
									{{#each countries}}
										<option value="{{id}}">{{{name}}}</option>
									{{/each}}
								</script>
								<div class="col-md-6">
									<label for="language_id" class="control-label">Language</label>
									<select id="language_id" name="language_id" class="form-control select2"></select>
								</div>
								<script type="text/x-handlebars-template" id="languages-template">
									<option value="">Choose language...</option>
									{{#each languages}}
										<option value="{{id}}">{{{name}}}</option>
									{{/each}}
								</script>
							</div>
						</fieldset>

						<fieldset id="add-customer-agencies">
							<h5>Certificates</h5>
							<div class="form-group" style="margin-bottom: 0;">
								<div class="col-md-12" id="selected-certificates">
								</div>
							</div>
							<script type="text/x-handlebars-template" id="selected-certificate-template">
								<div class="pull-left selected-certificate">
									<input type="checkbox" name="certificates[]" value="{{id}}" style="position: absolute; top: 0; left: -9999px;" checked="checked">
									<strong>{{abbreviation}}</strong> - {{{name}}}
									<i class="fa fa-times remove-certificate" style="cursor: pointer;"></i>
								</div>
							</script>
							<div class="form-group">
								<div class="col-md-5">
									<label for="agency_id" class="control-label">Agency</label>
									<select id="agency_id" class="form-control select2">
									</select>
									<script type="text/x-handlebars-template" id="agencies-template">
										<option value="">Choose agency...</option>
										{{#each agencies}}
											<option value="{{id}}">{{abbreviation}} - {{{name}}}</option>
										{{/each}}
									</script>
								</div>
								<div class="col-md-5">
									<label for="certificate_id" class="control-label">Certificate</label>
									<select id="certificate_id" class="form-control select2">
									</select>
									<script type="text/x-handlebars-template" id="certificates-template">
										<option value="">Choose certificate...</option>
										{{#each certificates}}
											<option value="{{id}}">{{{name}}}</option>
										{{/each}}
									</script>
								</div>
								<div class="col-md-2">
									<label>&nbsp;</label><br>
									<button class="btn btn-success add-certificate" style="width: 100%;">Add Cert</button>
								</div>
							</div>
						</fieldset>

						<fieldset>
							<h5>Diving Information</h5>
							<div class="form-group">
								<div class="col-md-12">
									<label for="medication" class="control-label">Is the customer currently taking any medication? :</label>
									<input id="add_customer_medication" type="checkbox" name="medication" value="1">
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-4">
									<label for="last_dive" class="control-label">Date of last dive</label>
									<input type="text" name="last_dive" class="form-control datepicker" data-date-format="YYYY-MM-DD">
								</div>
								<div class="col-md-4">
									<label for="number_of_dives" class="control-label">Number of dives</label>
									<input type="number" min="0" step="1" name="number_of_dives" class="form-control">
								</div>
								<div class="col-md-4">
									<label for="cylinder_size" class="control-label">Cylinder Size</label>
									<input type="text" name="cylinder_size" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-4">
									<label for="chest_size" class="control-label">BCD size</label>
									<input type="text" name="chest_size" class="form-control">
								</div>
								<div class="col-md-4">
									<label for="shoe_size" class="control-label">Fin size</label>
									<input type="text" name="shoe_size" class="form-control">
								</div>
								<div class="col-md-4">
									<label for="height" class="control-label">Wetsuit Size</label>
									<input type="text" name="height" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12">
									<label for="notes" class="control-label">Notes :</label>
									<textarea width="100%" name="notes" class="form-control" placeholder="Here you can add useful information about your customer such as a diving qualification number, alergies etc."></textarea>
								</div>
							</div>
						</fieldset>
					</div>
					<div class="panel-footer clearfix">
						<p class="pull-left"><span class="text-danger">**</span> Required for all customers &nbsp; &nbsp; &nbsp;</p>
						<p class="pull-left"><span class="text-danger">*</span> Required for lead customer</p>
						<button type="submit" class="btn btn-primary new-customer pull-right" style="margin-left:5px;">Create</button>
						<a href="javascript:void(0);" class="btn btn-warning clear-form pull-right">Clear</a>
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
			<form id="edit-customer-form" class="form-horizontal" role="form">
				<div class="modal-body">
					<fieldset id="edit-customer-details">
						<!-- This is where the Handlebars template will load into -->
					</fieldset>

					<fieldset id="edit-customer-countries">
						<div class="form-group">
							<div class="col-md-6">
								<label for="country_id">Country <span class="text-danger">*</span></label>
								<select id="country_id" name="country_id" class="form-control select2"></select>
							</div>
							<div class="col-md-6">
								<label for="language_id">Language</label>
								<select id="language_id" name="language_id" class="form-control select2"></select>
							</div>
						</div>
					</fieldset>

					<fieldset id="edit-customer-hotelstays">
						<script type="text/x-handlebars-template" id="hotelstay-template">
							<div class="pull-left selected-certificate">
								<input type="checkbox" name="hotelstays[]" style="position: absolute; top: 0; left: -9999px;" checked="checked">
								<i class="fa fa-times remove-hotelstay" data-id="{{id}}" style="cursor: pointer;"></i>
								<strong>{{name}}</strong>
								<p>Arrival - {{arrival}}</p>
								<p>Departure - {{departure}}</p>
							</div>
						</script>
						<h5>Accommodation Details <small> Build a history of where your customer has stayed on their trips</small></h5>
						<div class="form-group" style="margin-bottom: 0;">
							<div class="col-md-12" id="known-hotelstays"></div>
						</div>
						<div class="form-group">
							<div class="col-md-5">
								<label for="country_id">Name</label>
								<input type="text" id="hotel_name" name="hotel_name" class="form-control">
							</div>
							<div class="col-md-5">
								<label for="language_id">Address</label></label>
								<input id="hotel_address" name="hotel_address" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5">
								<label for="arrival">Arival Date</label>
								<input type="text" id="hotel_arrival" name="hotel_arrival" class="form-control datepicker" data-date-format="YYYY-MM-DD" data-date-view-mode="years">
							</div>
							<div class="col-md-5">
								<label for="departure">Departure Date</label></label>
								<input type="text" id="hotel_departure" name="hotel_departure" class="form-control datepicker" data-date-format="YYYY-MM-DD" data-date-view-mode="years">
							</div>
							<div class="col-md-2">
								<label>&nbsp;</label>
								<button class="btn btn-success add-hotelstay" style="width: 100%;">Add Stay</button>
							</div>
						</div>
					</fieldset>

					<fieldset id="edit-customer-agencies">
						<h5>Certificates</h5>
						<div class="form-group" style="margin-bottom: 0;">
							<div class="col-md-12" id="selected-certificates">
							</div>
						</div>
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

					<fieldset id="customer-diving-information">
						<!-- This is where a Handlebars template will load into -->
					</fieldset>

				</div>
				<div class="modal-footer">
					<p class="pull-left"><span class="text-danger">**</span> Required for all customers &nbsp; &nbsp; &nbsp;</p>
					<p class="pull-left"><span class="text-danger">*</span> Required for lead customer</p>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/x-handlebars-template" id="edit-customer-template">
	<input type="hidden" id="customer_id" name="id" value="{{id}}">
	<div class="form-group">
		<div class="col-md-6">
			<label for="firstname" class="control-label">First Name <span class="text-danger">**</span></label>
			<input id="customer-firstname" name="firstname" class="form-control" value="{{{firstname}}}">
		</div>
		<div class="col-md-6">
			<label for="lastname" class="control-label">Last Name <span class="text-danger">**</span></label>
			<input id="customer-lastname" name="lastname" class="form-control" value="{{{lastname}}}">
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<label for="email" class="control-label">Email <span class="text-danger">*</span></label>
			<input id="customer-email" name="email" class="form-control template-email" placeholder="@" value="{{{email}}}">
		</div>
		<div class="col-md-6" style="margin-top:30px;">
			<input type="checkbox" id="unsubscribe" name="unsubscribe" value="true" {{#if unsubscribed}}checked{{/if}}>
			<label for="unsubscribe" class="control-label">Unsubscribe from email marketing? </label>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<label for="birthday" class="control-label">Date of birth</label>
			<input type="text" id="birthday" name="birthday" class="form-control datepicker" data-date-format="YYYY-MM-DD" data-date-view-mode="years" value="{{birthday}}">
		</div>
		<div class="col-sm-6">
			<label for="phone" class="control-label">Phone</label>
			<input type="text" name="phone" class="form-control" placeholder="02071234567" value="{{{phone}}}">
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-12">
			<label for="online_source">Where did they here about us?</label>
			<input type="text" name="online_source" class="form-control" placeholder="Google, magazine etc ..." value="{{{online_source}}}">
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-12">
			<label for="address_1" class="control-label">Address 1</label>
			<input type="text" name="address_1" class="form-control" placeholder="Address 1" value="{{{address_1}}}">
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-12">
			<label for="address_2" class="control-label">Address 2</label>
			<input type="text" name="address_2" class="form-control" placeholder="Address 2" value="{{{address_2}}}">
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-4">
			<label for="city" class="control-label">City</label>
			<input type="text" name="city" class="form-control" placeholder="City" value="{{{city}}}">
		</div>
		<div class="col-md-4">
			<label for="county" class="control-label">County / State</label>
			<input type="text" name="county" class="form-control" placeholder="County" value="{{{county}}}">
		</div>
		<div class="col-md-4">
			<label for="postcode" class="control-label">Postcode</label>
			<input type="text" name="postcode" class="form-control" placeholder="Post Code" value="{{{postcode}}}">
		</div>
	</div>
</script>
<script type="text/x-handlebars-template" id="customer-diving-information-template">
	<h5>Diving Information</h5>
	<div class="form-group">
		<div class="col-md-12">
			<label for="medication" class="control-label">Is the customer currently taking any medication? :</label>
			<input id="edit_customer_medication" type="checkbox" name="medication" value="1" {{#if medication}} checked {{/if}}>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-4">
			<label for="last_dive" class="control-label">Date of last dive</label>
			<input type="text" name="last_dive" class="form-control datepicker" data-date-format="YYYY-MM-DD" value="{{last_dive}}">
		</div>
		<div class="col-md-4">
			<label for="number_of_dives" class="control-label">Number of dives</label>
			<input type="number" min="0" step="1" name="number_of_dives" class="form-control" value="{{number_of_dives}}">
		</div>
		<div class="col-md-4">
			<label for="cylinder_size" class="control-label">Cylinder Size</label>
			<input type="text" name="cylinder_size" class="form-control" value="{{cylinder_size}}">
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-4">
			<label for="chest_size" class="control-label">BCD size</label>
			<input type="text" name="chest_size" class="form-control" value="{{chest_size}}">
		</div>
		<div class="col-md-4">
			<label for="shoe_size" class="control-label">Fin size</label>
			<input type="text" name="shoe_size" class="form-control" value="{{shoe_size}}">
		</div>
		<div class="col-md-4">
			<label for="height" class="control-label">Wetsuit size</label>
			<input type="text" name="height" class="form-control" value="{{height}}">
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-12">
			<label for="notes" class="control-label">Notes :</label>
			<textarea width="100%" name="notes" class="form-control" placeholder="Here you can add useful information about your customer such as a diving qualification number, alergies etc.">{{notes}}</textarea>
		</div>
	</div>
</script>
