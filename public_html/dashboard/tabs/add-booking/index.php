<div id="wrapper" class="clearfix">
	<div class="col-md-12">
		<ul class="nav nav-wizard" role="tablist">
			<li role="presentation" class="active">
				<a href="javascript:void(0)" class="selected" role="tab" data-toggle="tab" data-target="#source-tab">
					<span class="step-number">1</span>
					<span class="step-description">Sources</span>
				</a>
			</li>
			<li role="presentation">
				<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#ticket-tab">
					<span class="step-number">2</span>
					<span class="step-description">Tickets</span>
				</a>
			</li>
			<li role="presentation">
				<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#customer-tab">
					<span class="step-number">3</span>
					<span class="step-description">Customers</span>
				</a>
			</li>
			<li role="presentation">
				<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#session-tab">
					<span class="step-number">4</span>
					<span class="step-description">Sessions</span>
				</a>
			</li>
			<li role="presentation">
				<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#addon-tab">
					<span class="step-number">5</span>
					<span class="step-description">Addons</span>
				</a>
			</li>
			<li role="presentation">
				<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#accommodation-tab">
					<span class="step-number">7</span>
					<span class="step-description">Accommodation</span>
				</a>
			</li>
			<li role="presentation">
				<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#extra-tab">
					<span class="step-number">7</span>
					<span class="step-description">Extra Info</span>
				</a>
			</li>
			<li role="presentation">
				<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#summary-tab">
					<span class="step-number">8</span>
					<span class="step-description">Summary</span>
				</a>
			</li>
		</ul>

		<div class="tab-content">

			<div role="tabpanel" class="tab-pane fade in active" id="source-tab">
				<div class="row row-header">
					<div class="col-xs-12">
						<div class="page-header">
							<h2>Booking Source <small>Where is your booking coming from?</small></h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-8 col-sm-offset-2">
						<div class="form-group">
							<div class="btn-group btn-group-justified booking-source">
								<a role="button" class="btn btn-default btn-lg" data-type="agent">
									<p><i class="fa fa-user fa-3x"></i></p>
									<p class="text-center">Agent</p>
								</a>
								<a role="button" class="btn btn-default btn-lg" data-type="telephone">
									<p><i class="fa fa-phone fa-3x"></i></p>
									<p class="text-center">Phone</p>
								</a>
								<a role="button" class="btn btn-default btn-lg" data-type="email">
									<p><i class="fa fa-envelope fa-3x"></i></p>
									<p class="text-center">Email</p>
								</a>
								<a role="button" class="btn btn-default btn-lg" data-type="facetoface">
									<p><i class="fa fa-eye fa-3x"></i></p>
									<p class="text-center">In Person</p>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="row" id="agent-info">
					<div class="col-sm-4 col-sm-offset-4">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h2 class="panel-title">Select Agent</h2>
							</div>
							<div class="panel-body">
								<div class="list-group" id="agents-list">
									<span class="loader"></span>
								</div>
							</div>
						</div>
						<script id="agents-list-template" type="text/x-handlebars-template">
							{{#each agents}}
							<a href="javascript:void(0);" data-id="{{id}}" class="list-group-item list-group-radio">
								{{{name}}} <span class="badge">{{{branch_name}}}</span>
							</a>
							{{/each}}
						</script>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<a href="javascript:void(0);" class="btn btn-primary source-finish pull-right">Next</a>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane fade" id="ticket-tab">
				<div class="row row-header">
					<div class="col-xs-12">
						<div class="page-header">
							<h2>Tickets <small>Which tickets would you like to book?</small></h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4 col-sm-push-8">
						<ul class="list-group">
							<li class="list-group-item active">
								<h4 class="list-group-item-heading">Selected Tickets</h4>
							</li>
							<div id="selected-tickets"></div>
							<div id="selected-packages"></div>
						</ul>
						<script id="selected-tickets-template" type="text/x-handlebars-template">
						{{#each tickets}}
							<li class="list-group-item">
								<p class="list-group-item-text">
									<i class="fa fa-ticket"></i>
									<a href="javascript:void(0);" title="Click to remove" class="remove-ticket" data-id="{{id}}">{{{name}}}</a>
									<span class="badge qty">{{qty}}</span>
								</p>
							</li>
						{{else}}
							<li class="list-group-item">
								<p class="list-group-item-text text-muted">
									No tickets selected yet...
								</p>
							</li>
						{{/each}}
						</script>
						<script id="selected-packages-template" type="text/x-handlebars-template">
						{{#each packages}}
							<li class="list-group-item">
								<p class="list-group-item-text">
									<i class="fa fa-tags"></i>
									<a href="javascript:void(0);" title="Click to remove" class="remove-package" data-id="{{id}}">{{{name}}}</a>
									<span class="badge qty">{{qty}}</span>
								</p>
							</li>
						{{else}}
							<li class="list-group-item">
								<p class="list-group-item-text text-muted">
									No packages selected yet...
								</p>
							</li>
						{{/each}}
						</script>
					</div>
					<div class="col-sm-8 col-sm-pull-4">
						<div class="row">
							<div class="col-sm-12" id="tickets-list">
								<span class="loader"></span>
								<script id="tickets-list-template" type="text/x-handlebars-template">
									{{#each tickets}}
										<div class="col-sm-6 col-md-4">
											<div class="panel panel-default">
												<div class="panel-body">
													<p class="text-center ticket-icon"><i class="fa fa-ticket fa-4x"></i></p>
													<p class="text-center ticket-name"><strong>{{{name}}}</strong></p>
													<p class="text-center ticket-price">{{priceRange base_prices}}</p>
													<a role="button" class="btn btn-primary btn-block btn-sm add-ticket" data-id="{{id}}">Add</a>
												</div>
											</div>
										</div>
									{{/each}}
								</script>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12" id="package-list">
								<span class="loader"></span>
								<script id="package-list-template" type="text/x-handlebars-template">
									{{#each packages}}
										<div class="col-sm-6 col-md-4">
											<div class="panel panel-default">
												<div class="panel-body">
													<p class="text-center ticket-icon"><i class="fa fa-tags fa-4x"></i></p>
													<p class="text-center ticket-name"><strong>{{{name}}}</strong></p>
													<p class="text-center ticket-price">{{priceRange base_prices}}</p>													
														<ul class="list-group">
															{{#each tickets}}
																<li class="list-group-item package-ticket-item">
																	<span class="badge">{{pivot.quantity}}</span>
																	<i class="fa fa-ticket fa-fw"></i> {{{name}}}
																</li>
															{{/each}}
														</ul>											
													<a role="button" class="btn btn-warning btn-block btn-sm add-package" data-id="{{id}}">Add</a>
												</div>
											</div>
										</div>
									{{/each}}
								</script>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<a href="javascript:void(0);" class="btn btn-primary tickets-finish pull-right">Next</a>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane fade" id="customer-tab">
				<div class="row row-header">
					<div class="col-xs-12">
						<div class="page-header">
							<h2>Customers <small>Select the customers this booking is for.</small></h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-7">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Existing Customer</h3>
							</div>
							<div class="panel-body">
								<div class="form-group">
									<label for="existing-customers" class="col-sm-3 control-label">Name</label>
									<div class="col-sm-9">
										<select id="existing-customers" name="existing-customers" class="form-control select2">
											<option selected="selected" value="">Loading...</option>
										</select>
										<script id="customers-list-template" type="text/x-handlebars-template">
											<option selected="selected" value="">Search for a customer...</option>
											{{#each customers}}
												<option value="{{id}}">{{{firstname}}} {{{lastname}}} - {{email}}</option>
											{{/each}}
										</script>
									</div>
								</div>
							</div>
							<div class="panel-footer">
								<div class="row">
									<div class="col-xs-12">
										<a href="javascript:void(0);" class="btn btn-primary add-customer pull-right" style="margin-left:5px;">Add to booking</a>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-success form-horizontal">
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
											<div class="col-sm-6">
												<label for="phone" class="control-label">Phone <span class="text-danger">*</span></label>
												<input type="text" name="phone" class="form-control" placeholder="+1234567890">
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
													<select id="country_id" name="country_id" class="form-control select2">
														<option value="">Loading...</option>
													</select>
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
											<div class="col-xs-12">
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
					<div class="col-md-4">
						<ul class="list-group">
							<li href="#" class="list-group-item active">
								<h4 class="list-group-item-heading">Selected Customers</h4>
							</li>
							<div id="selected-customers"></div>
							<script id="selected-customers-template" type="text/x-handlebars-template">
								{{#each customers}}
									<li href="#" class="list-group-item" data-id="{{id}}" data-lead="{{lead}}" data-country-id="{{country_id}}">
										<h4 class="list-group-item-heading">{{{firstname}}} {{{lastname}}} {{isLead id}}</h4>
										<p class="list-group-item-text">
											<a href="mailto:{{email}}" class="customer-email">{{email}}</a><br>
											{{{address_1}}}<br>
											{{{city}}}, {{{county}}}, {{postcode}}<br>
											<abbr title="Phone">P:</abbr> <span class="customer-phone">{{phone}}</span>
										</p>
										<a href="javascript:void(0);" class="btn btn-primary btn-xs edit-customer" data-id="{{id}}">Edit</a>
										<a href="javascript:void(0);" class="btn btn-danger btn-xs remove-customer" data-id="{{id}}">Remove</a>
										<a href="javascript:void(0);" class="btn btn-warning lead-customer btn-xs" data-id="{{id}}">Lead Customer</a>
									</li>
								{{else}}
									<li class="list-group-item">
										<p class="list-group-item-text text-muted">
											No customers selected yet...
										</p>
									</li>
								{{/each}}
							</script>
						</ul>
						<div class="row">
							<div class="col-xs-12">
								<a href="javascript:void(0);" class="btn btn-primary customers-finish pull-right">Next</a>
							</div>
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
												<option value="">Loading...</option>
											</select>
											<script id="countries-template" type="text/x-handlebars-template">
												<option value="">Choose Country...</option>
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
			</div>

			<div role="tabpanel" class="tab-pane fade" id="session-tab">
				<div class="row row-header">
					<div class="col-xs-12">
						<div class="page-header">
							<h2>Sessions <small>When would you like to go diving?</small></h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-8">
						<div class="row session-requirements">
							<div class="col-sm-6">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h2 class="panel-title">Select Customer</h2>
									</div>
									<div class="panel-body">
										<div class="list-group" id="session-customers">
										</div>
									</div>
								</div>
								<script id="session-customers-template" type="text/x-handlebars-template">
									{{#each customers}}
										<a href="javascript:void(0);" data-id="{{id}}" class="list-group-item list-group-radio">
											{{{firstname}}} {{{lastname}}}
										</a>
									{{/each}}
								</script>
							</div>
							<div class="col-sm-6">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h2 class="panel-title">Select Ticket</h2>
									</div>
									<div class="panel-body">
										<div class="list-group" id="session-tickets">
										</div>
									</div>
								</div>
								<script id="session-tickets-template" type="text/x-handlebars-template">
									{{#each tickets}}
										<a href="javascript:void(0);" data-id="{{id}}" data-type="ticket" class="list-group-item list-group-radio">
											<span class="label label-default">Ticket</span>
											{{{name}}}
										</a>
									{{/each}}
								</script>
								<script id="session-packages-template" type="text/x-handlebars-template">
									{{#each packages}}
										{{#each tickets}}
											<a href="javascript:void(0);" data-id="{{id}}" data-package-id="{{../id}}" data-type="package" class="list-group-item list-group-radio">
												<span class="label label-warning">{{{../name}}}</span>
												{{{name}}}
											</a>
										{{/each}}
									{{/each}}
								</script>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h3 class="panel-title">Assign to Session</h3>
									</div>
									<div class="panel-body">
										<div class="row ">
											<form role="form" id="session-filters">
												<div class="col-sm-3">
													<div class="form-group">
														<label for="after">After:</label>
														<input type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="after" placeholder="YYYY-MM-DD">
													</div>
												</div>
												<div class="col-sm-3">
													<div class="form-group">
														<label for="before">Before:</label>
														<input type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="before" placeholder="YYYY-MM-DD">
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group">
														<label for="trips">Trip:</label>
														<select id="trips" name="trip_id" class="form-control select2">
															<option selected="selected" value="">Loading...</option>
														</select>
														<script id="trips-list-template" type="text/x-handlebars-template">
															<option selected="selected" value="">Choose trip...</option>
															{{#each trips}}
																<option value="{{id}}">{{{name}}}</option>
															{{/each}}
														</script>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group">
														<label for="">&nbsp;</label>
														<button type="submit" class="btn btn-primary btn-block">Filter</button>
													</div>
												</div>
											</form>
										</div>
										<div class="row">
											<div class="col-xs-12">
												<table class="table table-condensed" id="sessions-table">
													<thead>
														<tr>
															<th>Start</th>
															<th>Finish</th>
															<th>Trip</th>
															<th>Free Spaces</th>
															<th>Boat</th>
															<th>Actions</th>
														</tr>
													</thead>
													<tbody>

													</tbody>
												</table>
											</div>
											<script id="sessions-table-template" type="text/x-handlebars-template">
												{{#each sessions}}
													{{#unless deleted_at}}
														<tr>
															<td class="session-start">{{friendlyDate start}}</td>
															<td class="session-end">{{tripFinish start trip.duration}}</td>
															<td class="session-trip">{{{trip.name}}}</td>
															<td>{{freeSpaces capacity}}</td>
															<td>{{{boat.name}}}</td>
															<td><a href="javascript:void(0);" class="btn btn-primary btn-sm assign-session" data-id="{{id}}">Assign</a></td>
														</tr>
													{{/unless}}
												{{else}}
													<tr>
														<td colspan="6">
															<h3 class="text-center text-muted"><i class="fa fa-exclamation-triangle"></i></h3>
															<h5 class="text-center text-muted">Your search did not match any sessions.</h5>
														</td>
													</tr>
												{{/each}}
											</script>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<ul class="list-group">
							<li href="#" class="list-group-item active">
								<h4 class="list-group-item-heading">Assigned Sessions</h4>
							</li>
							<div id="booking-details"></div>
							<script id="booking-details-template" type="text/x-handlebars-template">
								{{#each details}}
									<li class="list-group-item">
										<h4 class="list-group-item-heading"><span class="customer-name">{{{customer.firstname}}} {{{customer.lastname}}}</span></h4>
										<p class="list-group-item-text">
											<strong>Ticket:</strong> 
											{{#if packagefacade}} 
												<span class="label label-warning">{{{packagefacade.package.name}}}</span>
											{{else}}
												<span class="label label-default">Ticket</span>
											{{/if}}
											<span class="ticket-name">{{{ticket.name}}}</span>
										</p>
										<p class="list-group-item-text"><strong>Trip:</strong> <span class="trip-name">{{{session.trip.name}}}</span></p>
										<p class="list-group-item-text"><strong>Date:</strong> <span class="start-date">{{friendlyDate session.start}}</span></p>
										<a href="javascript:void(0);" class="btn btn-danger btn-xs unassign-session" data-id="{{id}}">Unassign</a>
									</li>
								{{else}}
									<li class="list-group-item">
										<p class="list-group-item-text text-muted">
											No tickets/packages assigned yet...
										</p>
									</li>
								{{/each}}
							</script>
						</ul>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<a href="javascript:void(0);" class="btn btn-primary sessions-finish pull-right">Next</a>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane fade" id="addon-tab">
				<div class="row row-header">
					<div class="col-xs-12">
						<div class="page-header">
							<h2>Addon Selection <small>Do you require any addons?</small></h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h2 class="panel-title">Step 1: Select the booking detail</h2>
							</div>
							<div class="panel-body">
								<div class="list-group" id="addon-booking-details">
								</div>
							</div>
							<script id="addon-booking-details-template" type="text/x-handlebars-template">
								{{#each details}}
									<a href="javascript:void(0);" class="list-group-item list-group-radio" data-id="{{id}}">
										<h4 class="list-group-item-heading"><span class="customer-name">{{{customer.firstname}}} {{{customer.lastname}}}</span></h4>
										<p class="list-group-item-text"><strong>Ticket:</strong> 
											{{#if packagefacade}} 
												<span class="label label-warning">{{{packagefacade.package.name}}}</span>
											{{else}}
												<span class="label label-default">Ticket</span>
											{{/if}}
											<span class="ticket-name">{{{ticket.name}}}</span>
										</p>
										<p class="list-group-item-text"><strong>Trip:</strong> <span class="trip-name">{{{session.trip.name}}}</span></p>
										<p class="list-group-item-text"><strong>Date:</strong> <span class="start-date">{{friendlyDate session.start}}</span></p>
									</a>
								{{/each}}
							</script>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h2 class="panel-title">Step 2: Select Addons</h2>
							</div>
							<div class="panel-body">
								<ul class="list-group" id="addons-list">
									<span class="loader"></span>
								</ul>
							</div>
						</div>
						<script id="addons-list-template" type="text/x-handlebars-template">
							{{#each addons}}
								<li data-id="{{id}}" class="list-group-item">
									<h4 class="list-group-item-heading addon-name">{{{name}}}</h4>
									<p>{{{description}}}</p>
									<div class="row">
										<div class="col-md-4">
											<p class="lead mb5">£ <span id="baseprice-{{id}}" class="price">{{decimal_price}}</span></p>
										</div>
										<div class="col-md-5 pull-right">
											<div class="input-group">
												<input type="number" min="1" max="50" value="1" name="addon-qty" data-id="{{id}}" class="form-control input-sm addon-qty">
												<span class="input-group-btn">
													<button data-id="{{id}}" class="btn btn-primary btn-sm add-addon" type="button">Add</button>
												</span>
											</div>
										</div>
									</div>
								</li>
							{{/each}}
						</script>
					</div>
					<div class="col-sm-5">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h2 class="panel-title">Addons Summary (Total: <span id="addons-summary-total">0.00</span>)</h2>
							</div>
							<div class="panel-body">
								<ul class="list-group" id="selected-addons">

								</ul>
							</div>
						</div>
						<script id="selected-addons-template" type="text/x-handlebars-template">
							{{#each details}}
								<li class="list-group-item summary-item">
									<div class="row">
										<div class="col-md-6">
											<h4 class="list-group-item-heading">{{{customer.firstname}}} {{{customer.lastname}}}</h4>
											<p class="list-group-item-text"><strong>Ticket:</strong> 
												{{#if packagefacade}} 
													<span class="label label-warning">{{{packagefacade.package.name}}}</span>
												{{else}}
													<span class="label label-default">Ticket</span>
												{{/if}}
												{{{ticket.name}}}
											</p>
											<p class="list-group-item-text"><strong>Trip:</strong> {{{session.trip.name}}}</p>
											<p class="list-group-item-text"><strong>Date:</strong> {{friendlyDate session.start}}</p>
										</div>
										<div class="col-md-6">
											<h4 class="list-group-item-heading">Addons</h4>
											{{#each addons}}
												<p class="list-group-item-text">Name: {{{name}}}</p>
												<p class="list-group-item-text"><strong>Price:</strong> <span class="price">{{decimal_price}}</span></p>
												<p class="list-group-item-text"><strong>Quantity:</strong> <span class="qty">{{pivot.quantity}}</span></p>
												<a class="btn btn-danger btn-xs remove-addon" href="javascript:void(0);" data-id="{{id}}" data-bookingdetail-id="{{../id}}">Remove</a>
											{{/each}}
										</div>
									</div>
								</li>
							{{/each}}
						</script>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<a href="javascript:void(0);" class="btn btn-primary addon-finish pull-right">Next</a>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane fade" id="accommodation-tab">
				<div class="row row-header">
					<div class="col-xs-12">
						<div class="page-header">
							<h2>Accommodation Selection <small>Do you require any accommodation?</small></h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h2 class="panel-title">Step 1: Select Customer</h2>
							</div>
							<div class="panel-body">
								<div class="list-group" id="accommodation-customers">
								</div>
							</div>
							<script id="accommodation-customers-template" type="text/x-handlebars-template">
								{{#each customers}}
									<a href="javascript:void(0);" class="list-group-item list-group-radio accommodation-customer" data-id="{{id}}">
										<h4 class="list-group-item-heading">{{{firstname}}} {{{lastname}}}</h4>
										{{#each bookingdetails}}
											<p class="list-group-item-text">
												<strong>Ticket:</strong> 
												{{#if packagefacade}} 
													<span class="label label-warning">{{{packagefacade.package.name}}}</span>
												{{else}}
													<span class="label label-default">Ticket</span>
												{{/if}}
												<span class="ticket-name">{{{ticket.name}}}</span>
											</p>
											<p class="list-group-item-text"><strong>Trip: </strong>{{{session.trip.name}}}</p>
											<p class="list-group-item-text session-start" data-date="{{session.start}}"><strong>Date: </strong>{{friendlyDate session.start}}</p>
										{{/each}}
									</a>
								{{/each}}
							</script>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h2 class="panel-title">Step 2: Select Accommodation</h2>
							</div>
							<div class="panel-body">
								<ul class="list-group" id="accommodations-list">
									<span class="loader"></span>
								</ul>
							</div>
						</div>
						<script id="accommodations-list-template" type="text/x-handlebars-template">
							{{#each accommodations}}
								<li data-id="{{id}}" class="list-group-item accommodation-item">
									<h4 class="list-group-item-heading">{{{name}}}</h4>
									<p>{{{description}}}</p>
									<div class="row">
										<div class="form-group">
											<label for="" class="col-lg-1 control-label">From: </label>
											<div class="col-lg-4">
												<input type="text" name="start" class="form-control input-sm datepicker accommodation-start" data-date-format="YYYY-MM-DD">
											</div>
											<label class="col-lg-1 control-label">To: </label>
											<div class="col-lg-4">
												<input type="text" name="end" class="form-control input-sm datepicker accommodation-end" data-date-format="YYYY-MM-DD">
											</div>
											<div class="col-lg-2">
												<button class="btn btn-primary btn-sm add-accommodation pull-right" data-id="{{id}}">Add</button>
											</div>
										</div>
									</div>
								</li>
							{{/each}}
						</script>
					</div>
					<div class="col-sm-4">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h2 class="panel-title">Assigned Accommodation</h2>
							</div>
							<div class="panel-body">
								<ul class="list-group" id="assigned-accommodations">

								</ul>
							</div>
						</div>
						<script id="assigned-accommodations-template" type="text/x-handlebars-template">
							{{#each accommodations}}
								<li class="list-group-item summary-item">
									<h4 class="list-group-item-heading">{{{customer.firstname}}} {{{customer.lastname}}}</h4>
									<p class="list-group-item-text"><strong>Accommodation: </strong> {{{name}}}</p>
									<p class="list-group-item-text"><strong>Start:</strong> {{friendlyDate pivot.start}}</p>
									<p class="list-group-item-text"><strong>End:</strong> {{friendlyDate pivot.end}}</p>
									<a class="btn btn-danger btn-xs remove-accommodation" href="javascript:void(0);" data-id="{{id}}" data-customer-id="{{customer.id}}">Remove</a>
								</li>
							{{/each}}
						</script>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<a href="javascript:void(0);" class="btn btn-primary accommodation-finish pull-right">Next</a>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane fade" id="extra-tab">
				<div class="row row-header">
					<div class="col-xs-12">
						<div class="page-header">
							<h2>Extra Details <small>Is there anything else we should know?</small></h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-3 col-xs-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h2 class="panel-title">Extra Information (Optional)</h2>
							</div>
							<form id="extra-form" class="form-horizontal">
								<fieldset>
									<div class="panel-body">
										<div class="form-group">
											<label for="pick-up-location" class="col-sm-4 control-label">Pick Up Location</label>
											<div class="col-md-8">
												<div class="input-group">
													<input type="text" class="form-control" id="pick-up-location" name="pick_up_location" />
													<span class="input-group-addon"><i class="fa fa-search"></i></span>
												</div>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="pick-up-date" class="col-sm-4 control-label">Pick Up Date & Time</label>
											<div class="col-md-4">
												<input type="text" id="pick-up-date" name="pick_up_date" class="form-control pull-left datepicker" data-date-format="YYYY-MM-DD">
												<input type="text" id="pick-up-time" name="pick_up_time" class="form-control timepicker" data-date-format="HH:mm">
											</div>
										</div>
										<div class="form-group">
											<label for="discount" class="col-sm-4 control-label">Discount</label>
											<div class="col-md-8">
												<input id="discount" name="discount" class="form-control" type="number">
											</div>
										</div>
										<div class="form-group col-xs-12">
											<textarea id="comment" name="comment" class="form-control" rows="3" placeholder="Any extra comments?"></textarea>
										</div>
									</div>
									<div class="panel-footer">
										<div class="row">
											<div class="col-xs-12">
												<button type="submit" class="btn btn-primary pull-right" style="margin-left:5px;">Save & Next</button>
												<a href="javascript:void(0);" class="btn btn-warning clear-form pull-right">Clear</a>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
						</div>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane fade" id="summary-tab">
				<div class="row row-header">
					<div class="col-xs-12">
						<div class="page-header">
							<h2>Summary <small>Booking summary</small></h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-10 col-lg-offset-1">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-7">
										<ul class="list-group">
											<li class="list-group-item active">
												<h4 class="list-group-item-heading">Sessions &amp; Addon</h4>
											</li>
											<div id="summary-booking-details"></div>
											<script id="summary-booking-details-template" type="text/x-handlebars-template">
												{{#each bookingdetails}}
													<li class="list-group-item">
														<div class="row">
															<div class="col-md-6">
																<h4 class="list-group-item-heading">{{{customer.firstname}}} {{{customer.lastname}}}</h4>
																<p class="list-group-item-text">
																	<strong>Ticket:</strong> 
																	{{#if packagefacade}} 
																		<span class="label label-warning">{{{packagefacade.package.name}}}</span>
																	{{else}}
																		<span class="label label-default">Ticket</span>
																	{{/if}}
																	<span class="ticket-name">{{{ticket.name}}}</span>
																</p>
																<p class="list-group-item-text"><strong>Trip:</strong> {{{session.trip.name}}}</p>
																<p class="list-group-item-text"><strong>Date:</strong> {{friendlyDate session.start}}</p>
															</div>
															<div class="col-md-6">
																<ul class="list-group">
																	{{#each addons}}
																		<li class="list-group-item">
																			<p class="list-group-item-text"><strong>Addon Name:</strong> {{{name}}}</p>
																			<p class="list-group-item-text"><strong>Quantity:</strong> {{{pivot.quantity}}}</p>
																			<p class="list-group-item-text"><strong>Price:</strong> {{{decimal_price}}}</p>
																		</li>
																	{{/each}}
																</ul>
															</div>
														</div>
													</li>
												{{/each}}
											</script>
										</ul>
									</div>
									<div class="col-md-5">
										<ul class="list-group">
											<li class="list-group-item active">
												<h4 class="list-group-item-heading">Accommodation</h4>
											</li>
											<div id="summary-accommodations"></div>
											<script id="summary-accommodations-template" type="text/x-handlebars-template">
												{{#each accommodations}}
													<li class="list-group-item">
														<h4 class="list-group-item-heading">{{{customer.firstname}}} {{{customer.lastname}}}</h4>
														<p class="list-group-item-text"><strong>Name:</strong> {{{name}}}</p>
														<p class="list-group-item-text"><strong>Price:</strong> {{{decimal_price}}}</p>
														<p class="list-group-item-text"><strong>From:</strong> {{friendlyDate pivot.start}}</p>
														<p class="list-group-item-text"><strong>To:</strong> {{friendlyDate pivot.end}}</p>
													</li>
												{{/each}}
											</script>
										</ul>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h4 class="panel-title">Lead Customer</h4>
											</div>
											<div class="panel-body" id="summary-lead"></div>
											<script id="summary-lead-template" type="text/x-handlebars-template">
												<p class="lead"><strong>{{{firstname}}} {{{lastname}}}</strong></p>
												<p><strong>Email:</strong> {{{email}}}</p>
												<p><strong>Phone Number:</strong> {{{phone}}}</p>
												<p><strong>Country:</strong> {{{countryName country_id}}}</p>
											</script>
										</div>
									</div>
									<div class="col-md-3 col-md-offset-5 summary-totals">
										<div class="row">
											<div class="col-md-6">
												<div class="text-right">
													<!--<p>Sub-total:</p>-->
													<!--<p>VAT:</p>-->
													<h4 class="text-success">Total:</h4>
												</div>
											</div>
											<div class="col-md-6">
												<div class="text-right" id="summary-price">
													<script id="summary-price-template" type="text/x-handlebars-template">
														{{!--<p>£800.00</p>
														<p>£200.00</p>--}}
														<h4 class="text-success">{{currency}} {{decimal_price}}</h4>
														(Status: {{status}})
													</script>
												</div>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-md-12">
												<button class="btn btn-success btn-block save-booking mb10"><i class="fa fa-save fa-fw"></i> Save For Later</button>
												<button onclick="addTransaction();" class="btn btn-primary btn-block add-transaction"><i class="fa fa-credit-card fa-fw"></i> Add Transaction</button>
												<h4 class="text-center">Reserve Booking</h4>
												<form id="reserve-booking" class="form-horizontal">
													<div class="form-group">
														<div class="radio col-md-12">
															<label>
																<input type="radio" name="email" id="email-yes" value="1" checked>
																Send confirmation email to customer
															</label>
														</div>
														<div class="radio col-md-12">
															<label>
																<input type="radio" name="email" id="email-no" value="0">
																Do not send email
															</label>
														</div>
													</div>
													<div class="form-group">
														<div class="col-md-12">
															<button class="btn btn-warning btn-block"><i class="fa fa-clock-o fa-fw"></i> Reserve</button>
														</div>
													</div>
													<div class="form-group">
														<label for="reserve-until" class="col-sm-6 control-label">For (hours)</label>
														<div class="col-md-6">
															<input id="reserve-until" name="reserved" class="form-control timepicker" placeholder="hh:mm" data-date-format="hh:mm">
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--Styling (Temporary)-->
	<link rel="stylesheet" href="tabs/add-booking/css/style.css" type="text/css" />

	<!--Select 2-->
	<link rel="stylesheet" href="/common/css/select2.css" type="text/css" />
	<link rel="stylesheet" href="/common/css/select2-bootstrap.css" type="text/css" />

	<script type="text/javascript" src="/common/js/select2.min.js"></script>

	<!--Typeahead-->
	<script type="text/javascript" src="/common/vendor/typeahead/bootstrap3-typeahead.min.js"></script>

	<!--Basil LocalStorage Wrapper-->
	<script type="text/javascript" src="/common/vendor/basil.js/build/basil.min.js"></script>

	<!--jQuery plugins-->
	<script src="/common/js/jquery/jquery.serialize-object.min.js"></script>

	<!--Controllers-->
	<script src="/dashboard/js/Controllers/Agent.js"></script>
	<script src="/dashboard/js/Controllers/Ticket.js"></script>
	<script src="/dashboard/js/Controllers/Package.js"></script>
	<script src="/dashboard/js/Controllers/Session.js"></script>
	<script src="/dashboard/js/Controllers/Booking.js"></script>
	<script src="/dashboard/js/Controllers/Trip.js"></script>
	<script src="/dashboard/js/Controllers/Customer.js"></script>
	<script src="/dashboard/js/Controllers/Addon.js"></script>
	<script src="/dashboard/js/Controllers/Accommodation.js"></script>
	<script src="/dashboard/js/Controllers/Boatroom.js"></script>
	<!--My scripts-->
	<script type="text/javascript" src="tabs/add-booking/js/script.js"></script>
</div>
