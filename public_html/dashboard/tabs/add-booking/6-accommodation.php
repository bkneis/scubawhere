<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Accommodation Selection <small>Do you require any accommodation?</small></h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
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
	<div class="col-sm-8">
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
</div>
