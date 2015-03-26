<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Trips & Classes <small>When would you like to go diving?</small></h2>
		</div>
	</div>
</div>
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
					<i class="fa fa-ticket fa-fw"></i>
					{{{name}}}
					<span class="badge badge-default small">{{qty}}</span>
				</a>
			{{/each}}
		</script>
		<script id="session-packages-template" type="text/x-handlebars-template">
			{{#each packages}}
				<div class="panel panel-default">
					<div class="panel-heading" role="tab">
						<h4 class="panel-title">
							<a class="accordian-heading" data-toggle="collapse" href="#booking-ticket-list-package-{{id}}">
								<i class="fa fa-tags fa-fw"></i>&nbsp; {{{name}}} <span class="badge badge-default small">{{qty}}</span>
								<i class="fa fa-plus-square-o expand-icon pull-right"></i>
							</a>
						</h4>
					</div>
					<div id="booking-ticket-list-package-{{id}}" class="panel-collapse collapse" role="tabpanel">
						<div class="panel-body">
							{{#if courses}}
								{{#each courses}}
									<a href="javascript:void(0);" data-id="{{id}}" data-package-id="{{../id}}" data-type="package" class="list-group-item list-group-radio">
										<i class="fa fa-graduation-cap fa-fw"></i>
										{{{name}}}
										<span class="badge badge-default small">{{pivot.quantity}}</span>
									</a>
								{{/each}}
							{{/if}}
							{{#if tickets}}
								{{#each tickets}}
									<a href="javascript:void(0);" data-id="{{id}}" data-package-id="{{../id}}" data-type="package" class="list-group-item list-group-radio">
										<i class="fa fa-ticket fa-fw"></i>
										{{{name}}}
										<span class="badge badge-default small">{{pivot.quantity}}</span>
									</a>
								{{/each}}
							{{/if}}
						</div>
					</div>
				</div>
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
								<div class="input-group">
									<input type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="after" placeholder="YYYY-MM-DD">
									<span class="input-group-addon" onclick="$(this).siblings('input').val('');" style="cursor: pointer;">
										<i class="fa fa-times" title="Clear date"></i>
									</span>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="before">Before:</label>
								<div class="input-group">
									<input type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="before" placeholder="YYYY-MM-DD">
									<span class="input-group-addon" onclick="$(this).siblings('input').val('');" style="cursor: pointer;">
										<i class="fa fa-times" title="Clear date"></i>
									</span>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="trips">Trip:</label>
								<select id="trips" name="trip_id" class="form-control select2">
									<option selected="selected" value="">All trips</option>
								</select>
								<script id="trips-list-template" type="text/x-handlebars-template">
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
									<th>Free Spaces</th>
									<th>Boat</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr><td colspan="6" style="text-align: center;"><br><i class="fa fa-refresh fa-spin fa-lg fa-fw"></i></td></tr>
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
									<h5 class="text-center text-muted">Your search did not match any trips.</h5>
								</td>
							</tr>
						{{/each}}
					</script>
				</div>
			</div>
		</div>
	</div>
</div>