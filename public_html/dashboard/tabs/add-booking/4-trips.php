<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Trips & Classes <small>Select a customer, select a ticket and assign a date. Or add your tickets without date.</small></h2>
		</div>
	</div>
</div>
<div class="row session-requirements">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Step 1 : Select Customer</h2>
			</div>
			<div class="panel-body">
				<div class="list-group" id="session-customers">
				</div>
			</div>
		</div>
		<script type="text/x-handlebars-template" id="session-customers-template">
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
				<h2 class="panel-title">Step 2 : Select Ticket</h2>
			</div>
			<div class="panel-body">
				<div class="list-group" id="session-tickets" data-toggle="buttons">
				</div>
			</div>
		</div>
		<script type="text/x-handlebars-template" id="session-tickets-template">
			{{#each tickets}}
				<label data-id="{{id}}" data-type="ticket" class="list-group-item btn btn-default">
					<input type="radio" name="selectables" />
					<i class="fa fa-ticket fa-fw"></i>
					{{{name}}}
					<span class="badge badge-default small">{{qty}}</span>
				</label>
			{{/each}}
		</script>
		<script type="text/x-handlebars-template" id="session-packages-template">
			{{#each packages}}
				<div class="panel panel-warning">
					<div class="panel-heading" role="tab">
						<h4 class="panel-title">
							<a class="accordion-heading" data-toggle="collapse" href="#booking-ticket-list-{{UID}}">
								<i class="fa fa-tags fa-fw"></i>&nbsp;
								{{{name}}}
								<i class="fa fa-plus-square-o expand-icon pull-right"></i>
							</a>
						</h4>
					</div>
					<div id="booking-ticket-list-{{UID}}" class="panel-collapse collapse" role="tabpanel">
						<div class="panel-body">
							<div class="list-group">
								{{#if courses}}
									{{#each courses}}
										<div class="panel panel-info">
											<div class="panel-heading" role="tab">
												<h4 class="panel-title">
													<a class="accordion-heading" data-toggle="collapse" href="#booking-ticket-list-{{UID}}">
														<i class="fa fa-graduation-cap fa-fw"></i>&nbsp;
														{{{name}}}
														<i class="fa fa-plus-square-o expand-icon pull-right"></i>
													</a>
												</h4>
											</div>
											<div id="booking-ticket-list-{{UID}}" class="panel-collapse collapse" role="tabpanel">
												<div class="panel-body">
													<div class="list-group">
														{{#if trainings}}
															{{#each trainings}}
																<label data-id="{{id}}" data-type="training" data-parent="course" data-parent-id="{{../id}}" data-parent-uid="{{../UID}}" data-parent-parent="package" data-parent-parent-id="{{../../id}}" data-parent-parent-uid="{{../../UID}}" data-identifier="{{../identifier}}" data-packagefacade="{{../../packagefacade}}" class="list-group-item btn btn-default">
																	<input type="radio" name="selectables" />
																	<i class="fa fa-graduation-cap fa-fw"></i>
																	{{{name}}}
																	<span class="badge badge-default small">{{qty}}</span>
																</label>
															{{/each}}
														{{/if}}
														{{#if tickets}}
															{{#each tickets}}
																<label data-id="{{id}}" data-type="ticket" data-parent="course" data-parent-id="{{../id}}" data-parent-uid="{{../UID}}" data-parent-parent="package" data-parent-parent-id="{{../../id}}" data-parent-parent-uid="{{../../UID}}" data-identifier="{{../identifier}}" data-packagefacade="{{../../packagefacade}}" class="list-group-item btn btn-default">
																	<input type="radio" name="selectables" />
																	<i class="fa fa-ticket fa-fw"></i>
																	{{{name}}}
																	<span class="badge badge-default small">{{qty}}</span>
																</label>
															{{/each}}
														{{/if}}
													</div>
												</div>
											</div>
										</div>
									{{/each}}
								{{/if}}
								{{#if tickets}}
									{{#each tickets}}
										<label data-id="{{id}}" data-type="ticket" data-parent="package" data-parent-id="{{../id}}" data-parent-uid="{{../UID}}" data-packagefacade="{{../packagefacade}}" class="list-group-item btn btn-default">
											<input type="radio" name="selectables" />
											<i class="fa fa-ticket fa-fw"></i>
											{{{name}}}
											<span class="badge badge-default small">{{qty}}</span>
										</label>
									{{/each}}
								{{/if}}
							</div>
						</div>
					</div>
				</div>
			{{/each}}
		</script>
		<script type="text/x-handlebars-template" id="session-courses-template">
			{{#if courses}}
				{{#each courses}}
					<div class="panel panel-info">
						<div class="panel-heading" role="tab">
							<h4 class="panel-title">
								<a class="accordion-heading" data-toggle="collapse" href="#booking-ticket-list-{{UID}}">
									<i class="fa fa-graduation-cap fa-fw"></i>&nbsp;
									{{{name}}}
									<i class="fa fa-plus-square-o expand-icon pull-right"></i>
								</a>
							</h4>
						</div>
						<div id="booking-ticket-list-{{UID}}" class="panel-collapse collapse" role="tabpanel">
							<div class="panel-body">
								<div class="list-group">
									{{#if trainings}}
										{{#each trainings}}
											<label data-id="{{id}}" data-type="training" data-parent="course" data-parent-id="{{../id}}" data-parent-uid="{{../UID}}" data-identifier="{{../identifier}}" class="list-group-item btn btn-default">
												<input type="radio" name="selectables" />
												<i class="fa fa-graduation-cap fa-fw"></i>
												{{{name}}}
												<span class="badge badge-default small">{{qty}}</span>
											</label>
										{{/each}}
									{{/if}}
									{{#if tickets}}
										{{#each tickets}}
											<label data-id="{{id}}" data-type="ticket" data-parent="course" data-parent-id="{{../id}}" data-parent-uid="{{../UID}}" data-identifier="{{../identifier}}" class="list-group-item btn btn-default">
												<input type="radio" name="selectables" />
												<i class="fa fa-ticket fa-fw"></i>
												{{{name}}}
												<span class="badge badge-default small">{{qty}}</span>
											</label>
										{{/each}}
									{{/if}}
								</div>
							</div>
						</div>
					</div>
				{{/each}}
			{{/if}}
		</script>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default" id="sessions-panel">
			<div class="panel-heading" style="padding-right: 5px;">
				<button id="assign-no-dates" class="btn btn-sm btn-default pull-right assign-session" style="margin-top: -5px;" data-temporary="temporary">Add without date <i class="fa fa-fast-forward fa-fw"></i></button>
				<h3 class="panel-title">Step 3 : Assign to a Session</h3>
			</div>
			<div class="panel-body">
				<div class="row ">
					<form role="form" id="session-filters">
						<div class="col-sm-3">
							<div class="form-group">
								<label for="after">Show trips from:</label>
								<div class="input-group">
									<input type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="after" placeholder="YYYY-MM-DD"value="<?php echo date('Y-m-d'); ?>">
									<span class="input-group-addon" onclick="$(this).siblings('input').val('');" style="cursor: pointer;">
										<i class="fa fa-times" title="Clear date"></i>
									</span>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<!--
							<div class="form-group">
								<label for="before">Before:</label>
								<div class="input-group">
									<input type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="before" placeholder="YYYY-MM-DD">
									<span class="input-group-addon" onclick="$(this).siblings('input').val('');" style="cursor: pointer;">
										<i class="fa fa-times" title="Clear date"></i>
									</span>
								</div>
							</div>
							-->
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="trips">Trip:</label>
								<select id="trips" name="trip_id" class="form-control select2">
									<option selected="selected" value="">All trips</option>
								</select>
								<script type="text/x-handlebars-template" id="trips-list-template">
									<option selected="selected" value="">All trips</option>
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
									<th>Capacity</th>
									<th>Boat</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr><td colspan="6" style="text-align: center;"><br><i class="fa fa-refresh fa-spin fa-lg fa-fw"></i></td></tr>
							</tbody>
						</table>
					</div>
					<script type="text/x-handlebars-template" id="sessions-table-template">
						{{#each sessions}}
							{{#unless deleted_at}}
								<tr>
									<td class="session-start">{{friendlyDate start}}</td>
									<td class="session-end">{{#if trip}}{{tripFinish start trip.duration}}{{else}}{{tripFinish start training.duration}}{{/if}}</td>
									<td class="session-trip">{{#if trip}}{{{trip.name}}}{{else}}{{{training.name}}}{{/if}}</td>
									<td>{{freeSpaces capacity}}</td>
									<td>{{#if trip}}{{{boat.name}}}{{else}}-{{/if}}</td>
									<td><a href="javascript:void(0);" class="btn btn-primary btn-sm assign-session" data-id="{{id}}">Assign</a></td>
								</tr>
							{{/unless}}
						{{else}}
							<tr>
								<td colspan="6">
									<h3 class="text-center text-muted"><i class="fa fa-exclamation-triangle"></i></h3>
									<h5 class="text-center text-muted">There are no relevant trips or all your tickets have been assigned</h5>
								</td>
							</tr>
						{{/each}}
					</script>
				</div>
			</div>
		</div>
	</div>
</div>
