<div id="wrapper" class="clearfix">
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default" id="search-customer-container">
				<div class="panel-heading">
					<h4 class="panel-title">Search for a customer</h4>
				</div>
				<div class="panel-body">
					<button id="add-new-customer" class="btn btn-success text-uppercase">&plus; New Customer</button>
					<form id="find-customer-form">
						<div class="form-row">
							<label class="field-label">Customer's First Name</label>
							<input type="text" name="firstname" class="form-control">
						</div>

						<div class="form-row">
							<label class="field-label">Customer's Last Name</label>
							<input type="text" name="lastname" class="datepicker form-control" data-date-format="YYYY-MM-DD">
						</div>

						<div class="form-row">
							<label class="field-label">Customer's Email</label>
							<input type="text" name="email" class="form-control">
						</div>

						<input type="reset" class="btn btn-danger btn-sm" value="Clear">
						<button class="btn btn-primary pull-right" id="find-customer">Find Customer</button>
					</form>
				</div>
			</div>
		</div>

		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Customers</h4>
				</div>
				<div class="panel-body">
					<div id="customer-table-div"></div>
				</div>
			</div>
		</div>
	</div><!-- .row -->

	<script type="text/x-handlebars-template" id="customer-list-item-template">
		<table id="customers-table" class="bluethead">
			<thead>
				<tr class="bg-primary">
					<th>Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>Country</th>
					<th>Last Dive</th>
				</tr>
			</thead>
			<tbody id="customer-list">

				{{#each customers}}
				<tr class="accordion-header" data-id={{id}}>
					<td>{{{firstname}}} {{{lastname}}}</td>
					<td>{{email}}</td>
					<td>{{phone}}</td>
					<td>{{getCountry country_id}}</td>
					<td>{{checkNull last_dive}}</td>
				</tr>
				{{else}}
				<tr><td colspan="4" style="text-align: center;">You have no customers yet.</td></tr>
				{{/each}}

			</tbody>
		</table>
	</script>

	<script type="text/x-handlebars-template" id="customer-buttons-template">
		<div style="float:right; text-align:right">
			<button onclick="emailCustomer({{customerID}})" class="btn btn-default"><i class="fa fa-envelope fa-fw"></i> Email Customer</button>
			<button onclick="editDetails({{customerID}})" class="btn btn-default"><i class="fa fa-pencil fa-fw"></i> Edit / View Details</button>
			<button onclick="viewBookings({{customerID}})" class="btn btn-default"><i class="fa fa-credit-card fa-fw"></i> View Bookings</button>
		</div>
	</script>


	<div id="modalWindows" style="height: 0;"></div>

	<script id="countries-template" type="text/x-handlebars-template">
		{{#each countries}}
			<option value="{{id}}">{{{name}}}</option>
		{{/each}}
	</script>

<!--<div class="modal fade" id="edit-customer-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Edit Customer</h4>
			</div>
			<form id="edit-customer-form" class="form-horizontal" role="form">
				<div class="modal-body">
					<fieldset id="edit-customer-details"></fieldset>
					</div>
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<p class="pull-left text-muted"><span class="text-danger">**</span> Required for all customers &nbsp; &nbsp; &nbsp;</p>
					<p class="pull-left text-muted"><span class="text-danger">*</span> Required for lead customer</p>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>-->

<script type="text/x-handlebars-template" id="customer-diving-information-template">
	<h5>Diving Information</h5>
	<div class="form-group">
		<div class="col-md-4">
			<label for="last_dive" class="control-label">Date of last dive</label>
			<input type="text" name="last_dive" class="form-control datepicker" data-date-format="YYYY-MM-DD" value="{{last_dive}}">
		</div>
		<div class="col-md-4">
			<label for="number_of_dives" class="control-label">Number of dives</label>
			<input type="number" min="0" step="1" name="number_of_dives" class="form-control" value="{{number_of_dives}}">
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-4">
			<label for="chest_size" class="control-label">Chest size</label>
			<input type="text" name="chest_size" class="form-control" value="{{chest_size}}">
		</div>
		<div class="col-md-4">
			<label for="shoe_size" class="control-label">Shoe size</label>
			<input type="text" name="shoe_size" class="form-control" value="{{shoe_size}}">
		</div>
		<div class="col-md-4">
			<label for="height" class="control-label">Height</label>
			<input type="text" name="height" class="form-control" value="{{height}}">
		</div>
	</div>
</script>

<script type="text/x-handlebars-template" id="certificates-template">
	<option value="">Choose certificate...</option>
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
							<div class="col-md-8">
								<label for="country_id">Country <span class="text-danger">*</span></label></label>
								<select id="country_id" name="country_id" class="form-control select2">
								</select>
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

<div class="modal fade" id="email-customer-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Email Customer</h4>
			</div>
			<form id="email-customer-form" class="form-horizontal" role="form">
				<div class="modal-body">
					<fieldset id="email-customer-details"></fieldset>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Send Email</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="customer-bookings-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Customer Bookings</h4>
			</div>
			<div class="modal-body">
				<select id="customer-bookings-ref">
				</select>
				<div id="customer-booking"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script type="text/x-handlebars-template" id="agencies-template">
	<option value="">Choose agency...</option>
	{{#each agencies}}
		<option value="{{id}}">{{abbreviation}} - {{{name}}}</option>
	{{/each}}
</script>

<script type="text/x-handlebars-template" id="email-customer-template">
	<div class="form-group">
		<div class="col-md-12">
			<label for="subject" class="control-label">Subject</label>
			<input type="text" name="subject" class="form-control" placeholder="Subject">
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-12">
			<label for="message" class="control-label">Message</label>
			<textarea rows="6" name="message" class="form-control" placeholder="Message"></textarea>
		</div>
	</div>
	<input type="hidden" name="to" class="form-control" value="{{email}}">
	<input type="hidden" name="customer_name" class="form-control" value="{{firstname}} {{lastname}}">
</script>

<script type="text/x-handlebars-template" id="edit-customer-template">
	<input type="hidden" name="id" value="{{id}}">
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
			<input id="customer-email" name="email" class="form-control" placeholder="@" value="{{{email}}}">
		</div>
		<div class="col-sm-6">
			<label for="phone" class="control-label">Phone <span class="text-danger">*</span></label>
			<input type="text" name="phone" class="form-control" placeholder="02071234567" value="{{{phone}}}">
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<label for="birthday" class="control-label">Date of birth</label>
			<input type="text" id="birthday" name="birthday" class="form-control datepicker" data-date-format="YYYY-MM-DD" data-date-view-mode="years" value="{{birthday}}">
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

	<script type="text/x-handlebars-template" id="booking-summary-template">
		<section align="center" style="min-width: 500px; max-width: 800px;">
		<article width="600" class="w320">
			<div class="mini-container-right-hack">
					<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate !important;">
						<td class="mini-block">
							Booking Date<br />
							<span class="header-sm">{{friendlyDateNoTime created_at_local}}</span><br />
							<br />
							Booking Reference<br />
							<span class="header-sm">{{reference}}</span><br />
						</td>
						<td class="mini-block">
							Total Cost<br />
							<span class="header-sm">{{currency}} {{decimal_price}}</span><br />
							<br />
							Source<br />
							<span class="header-sm">{{sourceIcon}}</span>
						</td>
					</table>
			</div>
		</article>
	</section>
	<section align="center" valign="top" style="border-top: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5;">
		<article width="600" class="w320">
			<div class="item-table">
				<table class="w320" cellspacing="0" cellpadding="0" width="100%">
				{{#if bookingdetails}}
					<tr>
						<td class="title-dark">
							 Trips & Classes
						</td>
						<td class="title-dark" width="100"></td>
						<td class="title-dark" width="100"></td>
					</tr>

					{{#each bookingdetails}}
						<tr>
							<td class="item-col" colspan="3">
								<table cellspacing="0" cellpadding="0" width="100%">
									<tr>
										<td class="item-col-inner title" colspan="2">
											<span style="color: #4d4d4d; font-weight:bold; font-size: 17px;">
												{{#if session}}
													<i class="fa fa-ship fa-fw"></i> {{{session.trip.name}}}
												{{else}}
													<i class="fa fa-graduation-cap fa-fw"></i> {{{training_session.training.name}}}
												{{/if}}
											</span>
											<span style="color: #4d4d4d; font-size: 14px; display: block; margin-top: 5px; margin-left: 28px; margin-bottom: -15px;">
												{{#if session}}
													{{friendlyDate session.start}} - {{tripFinish session.start session.trip.duration}}
												{{else}}
													{{friendlyDate training_session.start}} - {{tripFinish training_session.start training_session.training.duration}}
												{{/if}}
											</span>
										</td>
									</tr>
									<tr>
										<td class="item-col-inner item" style="padding-left: 28px;">
											<table cellspacing="0" cellpadding="0" width="100%">
												<tr>
													<td style="width: 90px;">
														<span style="color: #4d4d4d; font-weight:bold;">Customer:</span>
													</td>
													<td>
														{{{customer.firstname}}} {{{customer.lastname}}}
													</td>
												</tr>
												{{#if session}}
													<tr>
														<td>
															<span style="color: #4d4d4d; font-weight:bold;">Ticket:</span>
														</td>
														<td>
															{{{ticket.name}}}
														</td>
													</tr>
												{{/if}}
											</table>
										</td>
										<td class="item-col-inner item">
											<table cellspacing="0" cellpadding="0" width="100%">
												{{#if addons}}
													<tr>
														<td style="width: 90px;">
															<span style="color: #4d4d4d; font-weight:bold;">Addons:</span>
														</td>
														<td>
															{{#each addons}}
																{{{name}}} <small><span class="badge badge-default">{{pivot.quantity}}</span></small><br />
															{{/each}}
														</td>
													</tr>
												{{/if}}

												{{#if course}}
													<tr>
														<td style="padding-bottom: 0; width: 90px;">
															<span style="color: #4d4d4d; font-weight:bold;">Course:</span>
														</td>
														<td style="padding-bottom: 0;">
															<i class="fa fa-graduation-cap fa-fw"></i> {{{course.name}}}
														</td>
													</tr>
												{{/if}}

												{{#if packagefacade}}
													<tr>
														<td style="padding-top: 0; width: 90px;">
															<span style="color: #4d4d4d; font-weight:bold;">Package:</span>
														</td>
														<td style="padding-top: 0;">
															<i class="fa fa-tags fa-fw"></i> {{{packagefacade.package.name}}}
														</td>
													</tr>
												{{/if}}
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					{{/each}}
				{{/if}}

				{{#if accommodations}}
					<tr>
						<td class="item-col item mobile-row-padding" style="border-bottom: 0;"></td>
					</tr>
					<tr>
						<td class="title-dark">
							 Accommodations
						</td>
						<td class="title-dark" width="100"></td>
						<td class="title-dark" width="100"></td>
					</tr>
					{{#each accommodations}}
						<tr>
							<td class="item-col item" colspan="3">
								<table cellspacing="0" cellpadding="0" width="100%">
									<tr>
										<td class="item-col-inner title" colspan="2">
											<span style="color: #4d4d4d; font-weight:bold; font-size: 17px;">
												<i class="fa fa-bed fa-fw"></i> {{{name}}}
											</span>

											<span style="color: #4d4d4d; font-size: 14px; display: block; margin-top: 5px; margin-left: 28px; margin-bottom: -15px;">
												{{friendlyDateNoTime pivot.start}} - {{friendlyDateNoTime pivot.end}}
											</span>
										</td>
											</tr>
											<tr>
												<td class="item-col-inner item" style="padding-left: 28px;">
													<table cellspacing="0" cellpadding="0" width="100%">
														<tr>
															<td style="width: 90px;">
																<span style="color: #4d4d4d; font-weight:bold;">Customer:</span>
															</td>
															<td>
																{{{customer.firstname}}} {{{customer.lastname}}}
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
					{{/each}}
				{{/if}}
				</table>
			</div>
		</article>
	</section>
	</script>



	<link rel="stylesheet" href="/tabs/add-booking/css/style.css" type="text/css" />

	<script src="/common/js/jquery/jquery.serialize-object.min.js"></script>
	<script src="/common/js/jquery/jquery.reveal.js"></script>

	<script src="/tabs/customers/js/script.js"></script>
</div>
